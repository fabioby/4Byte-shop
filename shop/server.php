<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();

$page_name = basename($_SERVER['PHP_SELF']);
date_default_timezone_set('Europe/Budapest');
//header('Content-Type: application/json');

$data_settings = querySQL("SELECT * FROM settings WHERE id = 1")[0];

//main
$domain = $_SERVER['HTTP_HOST'];
$app_name = $data_settings['name'];
$ver = $data_settings['ver'];
$description = $data_settings['description'];
$title = $app_name." | Webáruház";



function connectToDB(){
	//db
	$host = "localhost";
	$user = "bytehu";
	$pass = "Z0nx)b0YA5+rY4";
	$db = "bytehu_webshop";
	$conn = mysqli_connect($host,$user,$pass,$db);
	if($conn) {
		return $conn;
	}
    else {
		echo "<script>alert('Connection failed: ".mysqli_connect_error()."')</script>";
		die("Connection failed: ".mysqli_connect_error());	
	}
}
function querySQL($sql){
	$conn = connectToDB();
	if($conn){
		$query = mysqli_query($conn, $sql);
		mysqli_close($conn);
		
		if(strpos($sql, "SELECT") !== false){
			$row = mysqli_num_rows($query);
			if($row > 0){
				$container = [];
				while($data = mysqli_fetch_assoc($query)){
					array_push($container, $data);
				}
				$row = mysqli_fetch_array($query,MYSQLI_ASSOC);
				return $container;
			}
			else{
				return false;
			}
		}
		else{
			return true;
		}
	}
}
function sendMail($consignor, $consignee, $subject, $msg){
	include 'PHPMailer/SMTP.php';
	include 'PHPMailer/PHPMailer.php';
	include 'PHPMailer/Exception.php';
	
	$mail = new PHPMailer(true);
	$domain = $_SERVER['HTTP_HOST'];
	$data_settings = querySQL("SELECT * FROM settings WHERE id = 1")[0];
	$app_name = $data_settings['name'];
	$mailHost = 'mail.'.$domain;
	$mailUser = 'bytehu';
	$mailPass = 'Z0nx)b0YA5+rY4';

	try {
		//Server settings
		//$mail->SMTPDebug = SMTP::DEBUG_SERVER;
		$mail->isSMTP();
		$mail->Host = $mailHost;
		$mail->SMTPAuth = true;
		$mail->Username = $mailUser;
		$mail->Password = $mailPass;
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		$mail->Port = 465;
		$mail->CharSet = 'UTF-8';
		$mail->Encoding = 'base64';
		//Recipients
		$mail->setFrom($consignor, $app_name);
		$mail->addAddress($consignee);
		//Content
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body = $msg;
		$mail->send();
	}
	catch (Exception $e) {
		echo "<script>alert('Message could not be sent. Mailer Error: ".$mail->ErrorInfo."');</script>";
	}
}
function uploadFiles($source, $targetDir, $compress){
    $img="";
    if($source['size']!=0){
        $allowedExtensions = array('jpg','png','jpeg','gif','webp');
    	$array_file = array_filter($source['name']);
    	foreach($array_file as $key=>$val){
            $file = basename($source['name'][$key]);  
            $fileTemp = $source['tmp_name'][$key];
        	$fileExtension = strtolower(pathinfo($targetDir.basename($file), PATHINFO_EXTENSION));
            
            if(in_array($fileExtension, $allowedExtensions)){
        		$uniq = uniqid().time();
        		$targetName = $uniq.'.webp';
                $result = convertImageToWebP($file, $fileTemp, $targetDir, $targetName);
                
                if ($result) {
    		        $targetNameCom = $uniq.'_c.webp';
                    resizeWebPImage($targetDir.$targetName, $targetDir."compressed/".$targetNameCom, $compress);
                    $img.=$targetName;
                    if($key < count($array_file)-1){
                         $img.=",";
                    }
                }
                else {
                    echo 'Image conversion to WebP failed.';
                }
                /*
                createResizedImg($fileTemp, $targetDir."compressed/".$fileNameCom, 300);
                if(move_uploaded_file($fileTemp, $targetDir.$fileName)){
                    //return $fileName;
                } 
                else {
                    echo "<script>alert('Error: Could not upload file.');</script>";
        			return false;
                }
                */
            }
        	else {
        		echo "<script>alert('".$file." Error: Unsupported file type (A feltöltött fájlformátum nem támogatott. Támogatott fájltípusok: .webp,.jpg,.jpeg,.png,.gif');</script>";
        		return false;
        	}
    	}
    }
    return $img;
}
function resizeWebPImage($sourceWebPPath, $outputResizedPath, $newWidth) {
    // Get the dimensions of the source .webp image
    list($width, $height) = getimagesize($sourceWebPPath);
    
    // Calculate the new height based on the new width while maintaining aspect ratio
    $newHeight = ($newWidth / $width) * $height;
    
    // Create a new image resource for the resized image
    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Load the source .webp image
    $sourceImage = imagecreatefromwebp($sourceWebPPath);
    
    // Resize the image
    imagecopyresized($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // Save the resized .webp image
    imagewebp($resizedImage, $outputResizedPath);
    
    // Clean up resources
    imagedestroy($resizedImage);
    imagedestroy($sourceImage);
    
    return $outputResizedPath;
}
function convertImageToWebP($file, $fileTemp, $targetDir, $targetName) {
    $sourceImagePath = $targetDir.$file."webp";
    $img_info = getimagesize($fileTemp);
    $img_type = $img_info[2];
    
    if (move_uploaded_file($fileTemp, $sourceImagePath)) {
        //$image;
        if ($img_type == IMAGETYPE_JPEG) {
            $image = imagecreatefromjpeg($sourceImagePath);
        } elseif ($img_type == IMAGETYPE_PNG) {
            $image = imagecreatefrompng($sourceImagePath);
        } elseif ($img_type == IMAGETYPE_GIF) {
            $image = imagecreatefromgif($sourceImagePath);
        } elseif ($img_type == IMAGETYPE_WEBP) {
            $image = imagecreatefromwebp($sourceImagePath);
        }
        imagewebp($image, $targetDir.$targetName);
        imagedestroy($image);
        unlink($sourceImagePath); // Delete the original uploaded image
        return $targetDir.$targetName;
    }
    else {
        return false; // Failed to move uploaded file
    }
}
function RgbToHex($R,$G,$B){
    $R = dechex($R);
    if (strlen($R)<2)
    $R = '0'.$R;

    $G = dechex($G);
    if (strlen($G)<2)
    $G = '0'.$G;

    $B = dechex($B);
    if (strlen($B)<2)
    $B = '0'.$B;

    return '#' . $R . $G . $B;
}
function HexToRgb( $colour ) {
	if ( $colour[0] == '#' ) {
			$colour = substr( $colour, 1 );
	}
	if ( strlen( $colour ) == 6 ) {
			list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
	} elseif ( strlen( $colour ) == 3 ) {
			list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
	} else {
			return false;
	}
	$r = hexdec( $r );
	$g = hexdec( $g );
	$b = hexdec( $b );
	return $r.",".$g.",".$b;
}
function arrayDifference($array1, $array2) {
    // Find the differences between $array1 and $array2
    $differences = array_diff($array1, $array2);
    
    // Find the differences between $array2 and $array1
    $differences2 = array_diff($array2, $array1);
    
    // Combine the differences from both arrays
    $result = array_merge($differences, $differences2);
    
    // Re-index the result array
    $result = array_values($result);
    
    return $result;
}
function formatPrice($price) {
    $price = intval($price);
    if ($price >= 1000) {
        return number_format($price / 1000, 3, '.', '');
    } 
	else {
        return $price;
    }
}


if(isset($_POST['submit_settings'])){
	$name = $_POST['name'];
	$description = $_POST['description'];
	$ver = $_POST['ver'];
	$width = $_POST['width'];
	$radius = $_POST['radius'];
	$rgb1 = HexToRgb($_POST['hex1']);
	$rgb2 = HexToRgb($_POST['hex2']);

	if (querySQL("UPDATE settings SET name='$name', description='$description', width='$width', radius='$radius', main_color='$rgb1', second_color='$rgb2', ver='$ver' WHERE id=1")) {
		echo "<script>alert('Beállítások sikeresen módosítva.');</script>";
	}
	else {
		echo "<script>alert('ERROR: Could not modify.');</script>";
	}
	//header("Refresh:0; url=admin.php?tab=3");	
}
if(isset($_POST['submit_register'])){
	$email = $_POST['email'];
	$pass = $_POST['pass'];
	$pass2 = $_POST['pass2'];
	$terms = isset($_POST['terms']);

	$query = querySQL("SELECT id FROM users WHERE email = '$email'");
	
	if(empty($query)){
		if($terms){
			if($pass == $pass2){
				$pass = password_hash($pass, PASSWORD_DEFAULT);
				$name = $_POST['name'];
				$phone = $_POST['phone'];
				$country = $_POST['country'];
				$city = $_POST['city'];
				$postcode = $_POST['postcode'];
				$strnum = $_POST['address'];
				$address = $country.", ".$city." ".$postcode.", ".$strnum;
				$token = bin2hex(openssl_random_pseudo_bytes(32));
				$date = date("Y/m/d");
				$query = querySQL("INSERT INTO users (name,email,phone,password,address,active,token,date) VALUES('$name','$email','$phone','$pass','$address','0','$token','$date')");		
				
				if ($query) {
					$message = '
					<div style="width:600px;display:block;margin:auto">
						<div style="padding:22px;background:rgb(0,0,0,0.1);display:flex;align-items:center;border-top-left-radius:4px;border-top-right-radius:4px;"><img style="height:40px;filter:brightness(0%) invert(1)" src="https://4byte.hu/files/images/logo.png"></div>
						<div style="padding:22px;border:1px solid rgba(0,0,0,0.12);border-bottom:none">
							<h3>Tisztelt '.$name.'!</h3>
							<p style="line-height:100%;color:rgba(0,0,0,0.8)">Ezt az üzenetet azért kapta, mert regisztrált webáruházunkba. A regisztráció befejezéséhez kattintson az alábbi linkre:</p>
							<a href="www.'.$domain.'/showroom/shop/server.php?token_register='.$token.'" style="color:rgb('.$data_settings['main_color'].')">Kattintson ide az aktiváláshoz</a>
							<br>
							<p>Ez egy automatikus levél, kérjük, ne válaszoljon rá! Amennyiben bármilyen, kérdése akad, keressen minket az info@'.$domain.' címen, vagy a +36 1 447 7888 telefonszámon!</p>
							<p>Üdvözlettel: '.$app_name.' csapata.</p>
						</div>
						<div style="padding:22px;background:rgba(0,0,0,0.1);font-size:14px;border-bottom-left-radius:4px;border-bottom-right-radius:4px">
							<div style="margin-bottom:10px">
								<a href="www.'.$domain.'" style="line-height:100%;color:rgb('.$data_settings['main_color'].')">www.'.$domain.'</a>
								<p style="line-height:100%">Kérdése van? <a href="'.$domain.'/shop/showroom/contact.php" style="color:rgb('.$data_settings['main_color'].')">Kapcsolat</a></p>
								<p style="line-height:100%">További információkért: <a href="'.$domain.'/shop/showroom/info.php" style="color:rgb('.$data_settings['main_color'].')">Információk</a></p>
							</div>
							<p style="margin-top:40px;margin-bottom:10px;line-height:120%;color:rgba(0,0,0,0.5);">Copyright © 2022 '.$app_name.'.</p>
						</div>
					</div>';
    
                    sendMail("info@".$domain, $email, "Regisztráció megerősítése", $message);

					echo "1";
				} 
                else {
					echo $query;
				}
			}
			else {
				echo "A megadott jelszavak nem egyeznek.";
			}
		}
		else {
			echo "A regisztráció folytatásához el kell fogadnia a szerződési feltételeket.";
		}
	}
	else {
		echo "A megadott email cím foglalt.";
	}
	exit;
}
if(isset($_GET['token_register'])){
	$token = $_GET['token_register'];
	$data = querySQL("SELECT active FROM users WHERE token='$token'");

	if(empty($data)){
		echo "<script>alert('Invalid token');</script>";
		header("Refresh:0; url=index.php");
	}
	else {
		querySQL("UPDATE users SET active='1',token='' WHERE token='$token'");
		echo "<script>alert('Regisztráció hitelesítve');</script>";
		header("Refresh:0; url=index.php?login");
	}
}
if(isset($_POST['submit_reset'])){
	$email = $_POST['email'];
	$token = bin2hex(openssl_random_pseudo_bytes(32));
	$message = '<html><body><div><p>To set a new password click the link below:</p><br><a href="www.4Byte.hu/showroom/shop/reset.php?token_reset='.$token.'" class="button">Set new password</a></div></body></html>';

	querySQL("UPDATE users SET token='$token' WHERE email=".$email);
	sendMail("info@4Byte.hu", $email, "New password", $message);
	
	echo "<script>alert('Az új jelszó beállításának lehetőségét elküldtük az Ön által megadott Email címre (".$email."). Kérjük ellenőrizze posta fiókját a művelet folytatásához.');</script>";
}
if(isset($_POST['submit_new_pass'])){
	$token = $_POST['token'];
	$pass = $_POST['pass'];
	$pass2 = $_POST['pass2'];

	if($pass==$pass2){
		querySQL("UPDATE users SET active='1',token='' WHERE token=".$token);
	}
	else {
		echo "<script>alert('Password's do not match');</script>";
	}
}
if(isset($_POST['submit_login'])){
	$email = $_POST['email'];
	$pass = $_POST['pass'];
	$url = $_POST['url'];
	$remember = isset($_POST['remember']);
	$query = querySQL("SELECT active,password FROM users WHERE email = '$email'");
	$active = $query[0]["active"];
	$db_pass = $query[0]["password"];
	$isPasswordCorrect = password_verify($pass, $db_pass);
	
	if($isPasswordCorrect){
		if($active != 0){
			$query = querySQL("SELECT * FROM users WHERE email = '".$email."' AND password = '".$db_pass."'");
			$id = $query[0]['id'];
			$name = $query[0]['name'];
			$email = $query[0]['email'];
			$admin = $query[0]['admin'];
			
			if($remember) {
				setcookie("email",$email,time()+36000);
				setcookie("pass",$pass,time()+36000);
			}
			else {
				setcookie("email","");
				setcookie("pass","");
				unset($_COOKIE["email"]);
				unset($_COOKIE["pass"]);
			}
			if($url != "checkout.php"){
				$url = "user.php";
			}

			$_SESSION['logged_in'] = [$id, $name, $email, $admin];
			header("Refresh:0; url=".$url);
		}
		else {
			echo "<script>alert('Account is inactive. Please check your mail to activate.');</script>";
		}
	}
	else {
		echo "<script>alert('Hibás adatok');</script>";
	}
}
if(isset($_GET['logout'])){
	session_destroy();
	unset($_SESSION['logged_in']);
	header("Refresh:0; url=index.php");
	exit();
}
if(isset($_POST['submit_add_premise'])){
	$address = $_POST['address'];
	$openTimes=$_POST['m0']."-".$_POST['m1'].";".$_POST['t0']."-".$_POST['t1'].";".$_POST['w0']."-".$_POST['w1'].";".$_POST['th0']."-".$_POST['th1'].";".$_POST['f0']."-".$_POST['f1'].";".$_POST['s0']."-".$_POST['s1'].";".$_POST['su0']."-".$_POST['su1'];
	if(querySQL("INSERT INTO premises (address,openTimes) VALUES('$address','$openTimes')")) {
		echo "<script>alert('Üzlethelyiség sikeresen hozzáadva.');</script>";
	}
	header("Refresh:0; url=admin.php");
}
if(isset($_POST['submit_edit_premise'])){
	$id = $_POST['id'];
	$address = $_POST['address'];
	$openTimes=$_POST['m0']."-".$_POST['m1'].";".$_POST['t0']."-".$_POST['t1'].";".$_POST['w0']."-".$_POST['w1'].";".$_POST['th0']."-".$_POST['th1'].";".$_POST['f0']."-".$_POST['f1'].";".$_POST['s0']."-".$_POST['s1'].";".$_POST['su0']."-".$_POST['su1'];
	if(querySQL("UPDATE premises SET address='$address', openTimes='$openTimes' WHERE id=".$id)) {
	    echo "<script>alert('Üzlethelyiség sikeresen módosítva.')</script>";
	}
	header("Refresh:0; url=admin.php?tab=4");
}
if(isset($_GET['submit_remove_premise'])){
	$id = $_GET['submit_remove_premise'];
	
    $query = querySQL("DELETE FROM premises WHERE id = ".$id);
	if($query){
    	echo "<script>alert('Üzlethelyiség sikeresen eltávolítva.');</script>";
    	header("Refresh:0; url=admin.php?tab=4");
	}
}
if(isset($_POST['submit_add_service'])){
	$name = $_POST['name'];
	$description = $_POST['description'];
	$duration = $_POST['duration'];
	$price = $_POST['price'];
	$targetDir = "files/images/services/";
	$img = uploadFiles($_FILES['files'], $targetDir, 350);
	$query = querySQL("INSERT INTO services (name,description,duration,price,img) VALUES('$name','$description','$duration','$price','$img')");

	if ($query) {
		echo "<script>alert('new Service added');</>";
	}
	else {
		echo "<script>alert('ERROR: ".mysqli_error($db)."');</script>";
	}
    header("Refresh:0; url=admin.php");
	exit;
}
if(isset($_POST['submit_edit_service'])){
	$id = $_POST['id'];
	$name = $_POST['name'];
	$description = $_POST['description'];
	$duration = $_POST['duration'];
	$price = $_POST['price'];
	$img = $_POST['img'];
	$targetDir = "files/images/services/";
	$images_db = querySQL("SELECT img FROM services WHERE id = ".$id);
	$array_images_db = explode(",", $images_db[0]['img']);
	$array_images = explode(",", $img);
    $arrayDifference = arrayDifference($array_images, $array_images_db);
	$uploadedImg = uploadFiles($_FILES['files'], $targetDir, 350);
	
    if(count($arrayDifference)>0){
    	foreach($arrayDifference as $key=>$val){
    	    $array_img = explode(".", $val);
    	    $img_c = $array_img[0]."_c.".$array_img[1];
        	$filePath = $targetDir.$val;
        	$filePath_c = $targetDir."compressed/".$img_c;
            unlink($filePath);
            unlink($filePath_c);
    	}
    }

    $img .= strlen($img) && strlen($uploadedImg)>0 ? "," : "";
    $img .= $uploadedImg;

	if (querySQL("UPDATE services SET name='$name', description='$description', duration='$duration', price='$price', img='$img' WHERE id=".$id)) {
	    echo "<script>alert('Szolgáltatás sikeresen módosítva.')</script>";
	}
	header("Refresh:0; url=admin.php?tab=1");
}
if(isset($_POST['submit_add_reservation'])){
	$s_id = $_POST['s_id'];
	$name = $_POST['name'];
	$service = $_POST['service'];
	$email = $_POST['email'];
    $phone = $_POST['phone'];
	$dateTime = $_POST['dateTime'];
	$duration = $_POST['duration'];
	$query = querySQL("INSERT INTO reservations (s_id,name,email,phone,dateTime) VALUES('$s_id','$name','$email','$phone','$dateTime')");

	if($query) {
		echo "<script>alert('Sikeres foglalás');</script>";
        $message = '
        <div style="width:600px;display:block;margin:auto">
            <div style="padding:22px;background:rgb(0,0,0,0.1);display:flex;align-items:center;border-top-left-radius:4px;border-top-right-radius:4px;"><img style="height:44px;filter:brightness(0%) invert(1)" src="https://4byte.hu/files/images/logo.png"></div>
            <div style="padding:22px;border:1px solid rgba(0,0,0,0.12);border-bottom:none">
                <h3>Kedves '.$name.'.</h3>
                <p style="line-height:150%;color:rgba(0,0,0,0.8)">Foglalását rendszerünk rögzítette.</p>
                <p style="line-height:120%;color:rgba(0,0,0,0.8)">Szolgáltatás: '.$service.'</p>
                <p style="line-height:120%;color:rgba(0,0,0,0.8)">Dátum: '.$dateTime.'</p>
                <p style="line-height:120%;color:rgba(0,0,0,0.8)">Időtartam: '.$duration.'</p>
                <a href="www.'.$domain.'/showroom/shop/server.php?delete_res='.$dateTime.'" style="color:rgb(190,160,66)">Kattintson ide a foglalás törléséhez</a>
                <p>Üdvözlettel: '.$app_name.' csapata.</p>
            </div>
            <div style="padding:22px;background:rgba(0,0,0,0.1);font-size:14px;border-bottom-left-radius:4px;border-bottom-right-radius:4px">
                <div style="margin-bottom:10px">
                    <a href="www.'.$domain.'" style="color:rgb(190,160,66)">www.'.$domain.'</a>
                    <p>Kérdése van? <a href="'.$domain.'/shop/showroom/contact.php" style="color:rgb(190,160,66)">Kapcsolat</a></p>
                    <p>További információkért: <a href="'.$domain.'/shop/showroom/info.php" style="color:rgb(190,160,66)">Információk</a></p>
                </div>
                <p style="margin-bottom:10px;line-height:120%;color:rgba(0,0,0,0.5);">Copyright © 2022 '.$app_name.' Kft.</p>
            </div>
        </div>';

        sendMail("info@".$domain, $email, "Foglalás rögzítve", $message);
	}
	else {
		echo "<script>alert('ERROR: ".mysqli_error($db)."');</script>";
	}
    header("Refresh:0; url=services.php");
	exit;
}
if(isset($_GET['delete_res'])){
    $dateTime = $_GET['delete_res'].":00";	
    $query = querySQL("DELETE FROM reservations WHERE dateTime = '".$dateTime."'");
	if($query){
    	echo "<script>alert('Foglalás törölve.');</script>";
    	header("Refresh:0; url=index.php");
	}
	//exit;
}
if(isset($_POST['submit_edit_personal'])){
	$id = $_POST['id'];
	$name = $_POST['name'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$country = $_POST['country'];
	$city = $_POST['city'];
	$postcode = $_POST['postcode'];
	$address = $_POST['address'];
	$address = $country.", ".$city." ".$postcode.", ".$address;
	$password = "";
	$pass = $_POST['pass'];
	$pass_new = $_POST['pass_new'];
	$pass_comfirm = $_POST['pass_comfirm'];
	if(!empty($pass) && !empty($pass_new) && !empty($pass_comfirm)){
		if($pass_new==$pass_comfirm){
			$query = querySQL("SELECT password FROM users WHERE email = '$email'");
			$isPasswordCorrect = password_verify($pass, $query[0]["password"]);
			if($isPasswordCorrect){
				$password = ", password='".password_hash($pass_new, PASSWORD_DEFAULT)."'";
			}
		}
	}
	
	if (querySQL("UPDATE users SET name='$name', email='$email', phone='$phone', address='$address'".$password." WHERE id=".$id)) {
	    echo "Adatok sikeresen frissítve";
	}
	exit;
}
if(isset($_GET['submit_delete_service'])){
	$id = $_GET['submit_delete_service'];
	$images = querySQL("SELECT img FROM services WHERE id = ".$id);
	$array_images = explode(",", $images[0]['img']);
	foreach($array_images as $key=>$val){
	    $img = $array_images[$key];
	    $array_img = explode(".", $img);
	    $img_c = $array_img[0]."_c.".$array_img[1];
    	$filePath = "files/images/services/".$img;
    	$filePath_c = "files/images/services/compressed/".$img_c;
        unlink($filePath);
        unlink($filePath_c);
	}
	
    $query = querySQL("DELETE FROM services WHERE id = ".$id);
	if($query){
    	echo "<script>alert('Service removed');</script>";
    	header("Refresh:0; url=admin.php");
	}
	//exit;
}
if(isset($_POST['submit_add_product'])){
	$name = $_POST['name'];
	$category = $_POST['category'];
	$description = $_POST['description'];
	$price = $_POST['price'];
	$discount = $_POST['discount'];
	$stock = $_POST['stock'];
	$url = $_POST['url'];
	$targetDir = "files/images/products/";
	$img = uploadFiles($_FILES['files'], $targetDir, 350);

	$query = querySQL("INSERT INTO products (id_category,name,description,price,discount,img,url,stock) VALUES('$category','$name','$description','$price','$discount','$img','$url','$stock')");

	if($query) {
		echo "<script>alert('Termék sikeresen hozzáadva.');</script>";
	}
	//header("Refresh:0; url=admin.php");
}
if(isset($_POST['submit_edit_product'])){
	$id = $_POST['id'];
	$name = $_POST['name'];
	$category = $_POST['id_category'];
	$description = $_POST['description'];
	$price = $_POST['price'];
	$discount = $_POST['discount'];
	$stock = $_POST['stock'];
	$url = $_POST['url'];
	$img = $_POST['img'];
	$targetDir = "files/images/products/";
	$images_db = querySQL("SELECT img FROM products WHERE id = ".$id);
	$array_images_db = explode(",", $images_db[0]['img']);
	$array_images = explode(",", $img);
	$arrayDifference = arrayDifference($array_images, $array_images_db);
	$uploadedImg = uploadFiles(array_reverse($_FILES['files']), $targetDir, 350);
	
	if(count($arrayDifference)>0){
		foreach($arrayDifference as $key=>$val){
			$array_img = explode(".", $val);
			$img_c = $array_img[0]."_c.".$array_img[1];
			$filePath = $targetDir.$val;
			$filePath_c = $targetDir."compressed/".$img_c;
			unlink($filePath);
			unlink($filePath_c);
		}
	}

	$img .= strlen($img)>0 && strlen($uploadedImg
	)>0 ? "," : "";
	$img = $img.$uploadedImg;

	if (querySQL("UPDATE products SET id_category='$category', name='$name', description='$description', price='$price', discount='$discount', img='$img', url='$url', stock='$stock' WHERE id=".$id)) {
	    echo "<script>alert('Termék sikeresen módosítva.')</script>";
	}
	header("Refresh:0; url=admin.php");
}
if(isset($_GET['submit_delete_product'])){
	$id = $_GET['submit_delete_product'];
	$images = querySQL("SELECT img FROM products WHERE id = ".$id);
	$array_images = explode(",", $images[0]['img']);
	foreach($array_images as $key=>$val){
	    $img = $array_images[$key];
	    $array_img = explode(".", $img);
	    $img_c = $array_img[0]."_c.".$array_img[1];
    	$filePath = "files/images/products/".$img;
    	$filePath_c = "files/images/products/compressed/".$img_c;
        unlink($filePath);
        unlink($filePath_c);
	}
	//echo "<script>alert('".$images[0]['img']."');</script>";    
	
	if(querySQL("DELETE FROM products WHERE id = ".$id)){
    	echo "<script>alert('Termék sikeresen törölve.');</script>";
    	//header("Refresh:0; url=admin.php");
	}
}
if(isset($_POST['submit_add_coupon'])){
	$code = $_POST['code'];
	$discount = $_POST['discount'];
	$date = $_POST['date'];
	if(querySQL("INSERT INTO coupons (code,discount,expiryDate) VALUES('$code','$discount','$date')")) {
		echo "<script>alert('".$code." számú kupon sikeresen hozzáadva.');</script>";
	}
	header("Refresh:0; url=admin.php?tab=1");
}
if(isset($_POST['submit_upload_gallery'])){
	$targetDir = "files/images/gallery/";

    $img = uploadFiles($_FILES['files'], $targetDir, 350);
    $img_array = explode(",", $img);
    foreach($img_array as $key=>$val){
	    $query = querySQL("INSERT INTO gallery (name,img) VALUES('name','$val')");
		//echo "<script>alert('ERROR: ".mysqli_error($db)."');</script>";
    }
	echo "<script>alert('Képek sikeresen feltöltve');</script>";
	header("Refresh:0; url=admin.php");
	exit;
}
if(isset($_GET['submit_delete_image'])){
	$data = $_GET['submit_delete_image'];
    $data_array = explode(";", $data);
    $id = $data_array[0];
    $query = querySQL("DELETE FROM gallery WHERE id = ".$id);
    
	if($query){
        $img = $data_array[1];
        $array_img = explode(".", $img);
        $img_c = $array_img[0]."_c.".$array_img[1];
    	$filePath = "files/images/gallery/".$img;
    	$filePath_c = "files/images/gallery/compressed/".$img_c;
        unlink($filePath);
        unlink($filePath_c);
    	echo "<script>alert('Kép sikeresen törölve');</script>";
    	header("Refresh:0; url=admin.php");
	}
}
if(isset($_POST['submit_mail'])){
	$name = $_POST['name'];
	$email = $_POST['email'];
	$msg = $_POST['msg'];
	
	$message = '
	<html>
		<body>
			<h3>You have recieved a new message</h3>
			<p>Name: '.$name.'.</p>
			<p>email: '.$email.'.</p>
			<p>message: '.$msg.'.</p>
		</body>
	</html>';
	
	sendMail("info@4Byte.hu", "info@4Byte.hu", "New message", $message);
	
	echo "Message sent! We will reply as soon as possible";
	exit;
}
if(isset($_POST['querySQL'])){
	$sql = $_POST['querySQL'];
	$data = querySQL($sql);
	echo json_encode($data);
	exit;
}
if(isset($_POST['upload_file'])){
	/*
    	$img = "";
    	$array_file = array_filter($_FILES['files']['name']);
    	foreach($array_file as $key=>$val){
    		$file = basename($_FILES['files']['name'][$key]);  
    		$fileTemp = $_FILES['files']['tmp_name'][$key];
    		$img .= uploadFiles($file, $fileTemp, $targetDir, true);
    		if(count($array_file)-1 != $key){
    			$img.=",";
    		}
    	}
	*/
	$targetDir = "files/images/products/";
	$img = uploadFiles($_FILES['files'], $targetDir, 350);
	echo $img;
	exit;
}
if(isset($_POST['submit_checkout'])){
	$order_id = rand(100000, 999999);
	$name = $_POST['name'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$country = $_POST['country'];
	$city = $_POST['city'];
	$postcode = $_POST['postcode'];
	$address = $country." ".$city." ".$postcode." ".$_POST['address'];
	$note = $_POST['note'];
	$payment = $_POST['radio_pay'];
	$isPayed = false;
	$shipment = $_POST['radio_ship'];
	$cart = json_decode($_POST['cart'], true);
	$products = json_encode($cart);
    $coupon = $_POST['coupon'];
    $date = date("Y-m-d");
    $dateTime = date("Y-m-d H:i:s");
	$products_t="";
	$sum = 0;

	foreach($cart as $val){
        $data_product = querySQL("SELECT * FROM products WHERE id=".$val['id']);
        $pname = $data_product[0]["name"];
        $discount = $data_product[0]["discount"];
        $price = $data_product[0]["price"];
        $price -= $price/100*$discount;
        $img = $data_product[0]["img"];
        $quantity = $val['quantity'];
        for ($i=0; $i < $quantity; $i++) {
            $sum+=intval($price);
        }
		$products_t.='
		<table style="box-sizing:border-box;margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid rgba(0,0,0,0.1)">
			<tr>
				<td style="box-sizing:border-box;padding-right:10px">
					<img style="width:120px;height:80px;object-fit:cover" src="https://www.'.$domain.'/showroom/shop/files/images/products/'.$img.'">
				</td>
				<td>
					<h3>'.$pname.'</h3>
					<p>'.$quantity.' db</p>
					<h4>'.formatPrice($price).' Ft / db</h4>
				</td>
			</tr>
		</table>';
	}

    if(!empty($coupon)){
        $data_coupon = querySQL("SELECT * FROM coupons WHERE code='".$coupon."' AND expiryDate > '".$date."'");
        $sum -= ($sum/100)*$data_coupon[0]["discount"];
    }

	$query = querySQL("INSERT INTO orders (id_order,name,email,phone,address,note,payment,isPayed,products,dateTime) VALUES('$order_id', '$name','$email','$phone','$address','$note','$payment','$isPayed','$products','$dateTime')");

	if($query) {
		echo "Sikeres rendelés.";
		$payment = $payment=="cash" ? "Készpénz" : "Kártya";
        $message = '
        <div style="width:600px;display:block;margin:auto">
            <div style="padding:22px;background:rgb(0,0,0,0.1);display:flex;align-items:center;border-top-left-radius:4px;border-top-right-radius:4px;"><img style="height:40px;filter:brightness(0%) invert(1)" src="https://4byte.hu/files/images/logo.png"></div>
            <div style="padding:22px;border:1px solid rgba(0,0,0,0.12);border-bottom:none">
                <h3>Tisztelt '.$name.'!</h3>
                <p style="line-height:100%;color:rgba(0,0,0,0.8)">Rendelését rendszerünk rögzítette.</p>
				<h2 style="margin-top:20px">Személyes adatok:</h2>
				<div style="margin-bottom:20px">
					<span style="line-height:100%;color:rgba(0,0,0,0.8)">Rendelés száma: </span><b>'.$order_id.'</b><br>
					<span style="line-height:100%;color:rgba(0,0,0,0.8)">Rendelés dátuma: </span><b>'.$dateTime.'</b><br>
					<span style="line-height:100%;color:rgba(0,0,0,0.8)">Számlázási név: </span><b>'.$name.'</b><br>
					<span style="line-height:100%;color:rgba(0,0,0,0.8)">Fizetési mód: </span><b>'.$payment.'</b><br>
				</div>
				<h2>Tételek:</h2>
				'.$products_t.'
                <p style="font-size:22px"><b>Összeszen</b>: '.formatPrice($sum).' Ft</p>
				<br>
				<p>Ez egy automatikus levél, kérjük, ne válaszoljon rá! Amennyiben bármilyen, kérdése akad, keressen minket az info@'.$domain.' címen, vagy a +36 1 447 7888 telefonszámon!</p>
                <p>Üdvözlettel: '.$app_name.' csapata.</p>
            </div>
            <div style="padding:22px;background:rgba(0,0,0,0.1);font-size:14px;border-bottom-left-radius:4px;border-bottom-right-radius:4px">
                <div style="margin-bottom:10px">
                    <a href="www.'.$domain.'" style="line-height:100%;color:rgb('.$data_settings['main_color'].')">www.'.$domain.'</a>
                    <p style="line-height:100%">Kérdése van? <a href="'.$domain.'/shop/showroom/contact.php" style="color:rgb('.$data_settings['main_color'].')">Kapcsolat</a></p>
                    <p style="line-height:100%">További információkért: <a href="'.$domain.'/shop/showroom/info.php" style="color:rgb('.$data_settings['main_color'].')">Információk</a></p>
                </div>
                <p style="margin-top:40px;margin-bottom:10px;line-height:120%;color:rgba(0,0,0,0.5);">Copyright © 2022 '.$app_name.'.</p>
            </div>
        </div>';

        sendMail("info@".$domain, $email, $order_id." számú rendelés rögzítve", $message);
	}
	else {
		echo "ERROR: ".mysqli_error($db);
	}
	header("Refresh:0; url=admin.php");
	exit;
}
if(isset($_GET['submit_delete_order'])){
	$id = $_GET['submit_delete_order'];	
    $query = querySQL("DELETE FROM orders WHERE id = ".$id);
	if($query){
    	echo "<script>alert('Rendelés törölve.');</script>";
    	header("Refresh:0; url=admin.php");
	}
}

//css 31 147 219
echo "
<style>
	:root {
		--main-color: ".$data_settings['main_color'].";
		--second-color: ".$data_settings['second_color'].";
		--width: ".$data_settings['width'].";
		--radius: ".$data_settings['radius']."px;
	}
</style>";