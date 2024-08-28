<!DOCTYPE html>
<html lang="hu">
	<?php
    	include "server.php";
		include "head.php";
		if(isset($_SESSION['logged_in'])){
			$s_id = $_SESSION['logged_in'][0];
			$s_name = $_SESSION['logged_in'][1];
			$s_email = $_SESSION['logged_in'][2];
			$s_admin = $_SESSION['logged_in'][3];
		}
		else {
			header("Refresh:0; url=index.php");
		}
	?>

	<body>
		<div id="vue">
			<?php include "header.php"; ?>

			<div class="bg">
				<div class="layer"></div>
				<div>
					<p><?php echo $s_name ?></p><br>
					<a href="server.php?logout" class="button"><i class="fa fa-sign-out"></i> Kijelentkezés</a>
					<?php if($s_admin==1) { ?>
					<a href="admin.php" class="button"><i class="fa fa-sign-in"></i> Admin</a>
					<?php } ?>
				</div>
			</div>
			
			<div class="user">
                <div class="select">
                    <select class="field" @change="handleSelectChange">
                        <option value="0" selected>Saját adataim</option>
                        <option value="1">Rendeléseim</option>
                        <option value="2">Foglalásaim</option>
                        <option value="3">Számláim</option>
                        <option value="4">Megfigyelt termékek</option>
                        <option value="5">Kupomjaim</option>
                    </select>
                </div>
				<div v-if="tab==0">
					<?php
						$data = querySQL("SELECT * FROM users WHERE id = ".$s_id);
						foreach ($data as $key => $value) {
							$address = explode(",",$value['address']);
							$county = $address[0];
							$city = rtrim(ltrim(preg_replace('/\d/', '', $address[1]), ' '), ' ');
							$post = (int)filter_var($address[1], FILTER_SANITIZE_NUMBER_INT);
							$address = ltrim($address[2], $address[2][0]);
							//$street = rtrim(ltrim(preg_replace('/\d/', '', $address[2]), ' '),' ');
							//$number = (int)filter_var($address[2], FILTER_SANITIZE_NUMBER_INT);
					?>
					<form id="form_personal" @submit.prevent="updatePersonal()">
                        <div>
                            <div>
                                <h2>Számlázási adataim</h2>
                                <div class="fl">
                                    <input type="text" class="field" name="name" id="name" value="<?php echo $value['name'] ?>" placeholder="Név *" required>
                                    <label>Név *</label>
                                </div>
                                <div class="fl">
                                    <input type="email" class="field" name="email" id="email" value="<?php echo $value['email'] ?>" placeholder="Emil cím *" required>
                                    <label>Emil cím *</label>
                                </div>
                                <div class="fl">
                                    <input type="text" class="field" name="phone" id="mobil" value="<?php echo $value['phone'] ?>" placeholder="Mobil *" required>
                                    <label>Mobil *</label>
                                </div>
                            </div>
                            <div>
                                <h2>Szállítási cím</h2>
                                <div class="fl">
                                    <select name="country" id="country" class="field">
                                        <option value="Magyarország">Magyarország</option>
                                    </select>
                                    <label>Ország</label>
                                </div>
                                <div class="fl">
                                    <input type="text" class="field" name="city" id="city" value="<?php echo $city ?>" placeholder="Város *" required>
                                    <label>Város</label>
                                </div>
                                <div class="fl">
                                    <input type="text" class="field" name="postcode" id="postcode" value="<?php echo $post ?>" placeholder="Irányító szám *" required>
                                    <label>Irányító szám</label>
                                </div>
                                <div class="fl">
                                    <input type="text" class="field" name="address" id="address" value="<?php echo $address ?>" placeholder="Utca, házszám *" required>
                                    <label>Utca, házszám</label>
                                </div>
                            </div>
                            <div>
                                <h2>Jelszó módosítása</h2>
                                <div class="fl">
                                    <input type="password" class="field" name="pass" autocomplete="new-password" placeholder="Régi jelszó *">
                                    <label>Régi jelszó</label>
                                </div>
                                <div class="fl">
                                    <input type="password" class="field" name="pass_new" placeholder="Új jelszó *">
                                    <label>Új jelszó</label>
                                </div>
                                <div class="fl">
                                    <input type="password" class="field" name="pass_comfirm" placeholder="Új jelszó még egyszer *">
                                    <label>Új jelszó még egyszer</label>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div>
                                <p>A változtatások mentéséhez kattints a "Beállítások mentése" gombra!</p>
                                <input type="hidden" name="id" value="<?php echo $value['id'] ?>">
                                <input type="submit" class="button" name="submit_data" value="Beállítások mentése">
                            </div>
                        </div>
					</form>
					<?php } ?>
				</div>
				<div v-if="tab==1" id="orders">
					<h2>Korábbi rendeléseim (<?php $data_orders = querySQL("SELECT * FROM orders WHERE email = '".$s_email."' ORDER BY id DESC"); if (!empty($data_orders)) echo count($data_orders) ?>):</h2>
					<div class="orders">
						<?php
							if(!empty($data_orders)){
								foreach ($data_orders as $order) {
                                    $products = json_decode($order['products'], true);
                				    //$products = explode(";",$order['products']);
                        ?>
						<div>
                            <div>
                                <h2>Adatok:</h2>
                                <div>Bizonylatszám: <b><?php echo $order['id_order'] ?></b></div>
                                <div>Rendelés dátuma: <b><?php echo $order['dateTime'] ?></b></div>
                                <div>Vásárló neve: <b><?php echo $order['name'] ?></b></div>
                                <div>Fizetési mód: <b><?php echo $order['payment']=='cash' ? "Készpénz" : "Kártya" ?></b></div>
                                <div>Fizetési állapot: <b><?php echo $order['isPayed']==0 ? "Kifizetetlen" : "Fizetve" ?></b></div>
                                <!--<div><i class="fa fa-angle-down"></i></div>-->
                            </div>
                            <div>
                                <h2>Tételek:</h2>
                                <?php foreach ($products as $product) { ?>
                                <div>
                                    <img src="files/images/products/compressed/<?php echo $product['img'] ?>" alt="">
                                    <div><?php echo $product['name'] ?> &times <?php echo $product['quantity'] ?></div>
                                    <div>{{formatPrice(<?php echo $product['price'] ?>)}} Ft</div>
                                </div>
                                <?php } ?>
                            </div>
						</div>
						<?php } } ?>	
					</div>
				</div>
				<div v-if="tab==2" id="orders">
                    <?php 
                        $data_res = querySQL("SELECT *,reservations.id AS rid, reservations.name AS rname FROM reservations INNER JOIN services ON services.id = reservations.s_id WHERE email = '".$s_email."'");
                    ?>
					<h2>Korábbi foglalásaim (<?php if (!empty($data_res)) echo count($data_res) ?>):</h2>
					<div class="orders">
						<?php
							if(!empty($data_res)){
								foreach ($data_res as $res) {
                        ?>
						<div>
                            <div>
                                <h2>Adatok:</h2>
                                <div>Foglalás szám: <b><?php echo $res['rid'] ?>F25</b></div>
                                <div>Név: <b><?php echo $res['rname'] ?></b></div>
                                <div>Foglalás dátuma: <b><?php echo $res['dateTime'] ?></b></div>
                                <div>Időtartam: <b><?php echo $res['duration'] ?> perc</b></div>
                                <!--<div><i class="fa fa-angle-down"></i></div>-->
                            </div>
                            <div>
                                <h2>Szolgáltatás:</h2>
                                <div>
                                    <img src="files/images/services/<?php echo $res['img'] ?>" alt="">
                                    <div><?php echo $res['name'] ?></div>
                                    <div>{{formatPrice(<?php echo $res['price'] ?>)}} Ft</div>
                                </div>
                            </div>
						</div>
						<?php } } ?>	
					</div>
				</div>
			</div>
			
			<?php include "footer.php"; ?>
		</div>
	</body>
</html>