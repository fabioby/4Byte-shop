<!DOCTYPE html>
<html lang="hu">
    <?php 
        include "server.php";
        include "head.php";
    ?>
	<body>
        <div style="width:600px;display:block;margin:auto">
            <div style="padding:22px;background:rgb(0,0,0,0.1);display:flex;align-items:center;border-top-left-radius:4px;border-top-right-radius:4px;">
                <img style="height:40px;filter:brightness(0%) invert(1)" src="https://'.$domain.'/files/images/logo.png">
            </div>
            <div style="padding:22px;border:1px solid rgba(0,0,0,0.12);border-bottom:none">
                <h3>Tisztelt '.$name.'.</h3>
                <br>
                <p style="color:rgba(0,0,0,0.8)">Rendelését rendszerünk rögzítette.</p>
                <br>
                <div style="margin-bottom:20px">
                    <b style="line-height:160%">A rendelés adatai:</b>
                    <p>Rendelés száma: '.$val['id'].'</p>
                    <p>Számlázási név: '.$val['name'].'</p>
                    <p>Rendelés dátuma: '.$val['dateTime'].'</p>
                    <p>Fizetési mód: '.$val['payment'].'</p>
                    <p>Fizetés állapota: '.$val['isPayed'].'</p>
                    <p></p>
                    <p></p>
                </div>
                <table style="box-sizing:border-box;margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid rgba(0,0,0,0.1)">
                    <b style="line-height:160%">Termékek:</b>
                    <tr>
                        <td style="box-sizing:border-box;padding-right:10px">
                            <img style="width:120px;height:80px;object-fit:cover" src="https://www.'.$domain.'/showroom/shop/files/images/products/compressed/'.$val['img'].'">
                        </td>
                        <td>
                            <h3>'.$val['name'].'</h3>
                            <p>'.$val['quantity'].' db</p>
                            <h4>'.$val['price'].' Ft</h4>
                        </td>
                    </tr>
                </table>
                <p><b>Összeszen</b>: '.$sum.' Ft</p>
				<br>
                <p>Ez egy automatikus levél, kérjük, ne válaszolj rá!<br>
                Kérjük, a megrendelés átvételéhez ezt a levelet mutasd meg kollégánknak!<br>
                Amennyiben bármi kérésed, kérdésed akad, keress minket az info@bestbyte.hu címen, vagy a +36 1 447 7888 telefonszámon!</p>
                <br>              
                <p>Üdvözlettel: '.$app_name.' csapata.</p>
            </div>
            <div style="padding:22px;background:rgba(0,0,0,0.1);font-size:14px;border-bottom-left-radius:4px;border-bottom-right-radius:4px">
                <div style="margin-bottom:10px">
                    <a href="www.'.$domain.'/showroom/shop/" style="color:rgb(190,160,66)">www.'.$domain.'</a>
                    <p>Kérdése van? <a href="'.$domain.'/shop/showroom/contact.php" style="color:rgb(190,160,66)">Kapcsolat</a></p>
                    <p>További információkért: <a href="'.$domain.'/shop/showroom/info.php" style="color:rgb(190,160,66)">Információk</a></p>
                </div>
                <p style="margin-bottom:10px;line-height:120%;color:rgba(0,0,0,0.5);">Copyright © 2022 '.$app_name.' Kft.</p>
            </div>
        </div>
	</body>
</html>