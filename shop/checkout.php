<!DOCTYPE html>
<html lang="hu">
    <?php include "server.php"; include "head.php" ?>

	<body>
		<div id="vue">
			<?php 
				include "header.php";
				$name="";$email="";$mobil="";$country="";$city="";$postcode="";$address="";
				if($loggenIn){
					$id = $_SESSION['logged_in'][0];
					$name = $_SESSION['logged_in'][1];
					$email = $_SESSION['logged_in'][2];
					$data = querySQL("SELECT * FROM users WHERE id = ".$id);
					$mobil = $data[0]['phone'];
					$address = explode(",",$data[0]['address']);
					$county = $address[0];
					$city = rtrim(ltrim(preg_replace('/\d/', '', $address[1]), ' '), ' ');
					$postcode = (int)filter_var($address[1], FILTER_SANITIZE_NUMBER_INT);
					//$address = ltrim($address[2], $address[2][0]);
                    $address = ltrim($address[2], $address[2][0]);
				}
			?>
			
			<div class="bg">
				<div class="layer"></div>
				<div>
					<h2>Pénztár</h2>
				</div>
			</div>

			<div class="checkout page" v-if="cart.length > 0">
			    <form method="POST" id="form_checkout" @submit.prevent="checkout()" enctype="multipart/form-data">
					<div class="data">
						<div>
							<h3>Számlázás és szállítás</h3>
							<?php if(!$loggenIn) echo "<p>Van már fiókja? <span onclick='toggleModal(\"modal_login\")'>Belépés</span></p>" ?>
						</div>
						<div>
							<div class="fl">
								<input type="text" class="field" name="name" value="<?php echo $name ?>" placeholder="Név *" required>
								<label>Név</label>
							</div>
							<div class="fl">
								<input type="email" class="field" name="email" value="<?php echo $email ?>" placeholder="Email *" required>
								<label>Email</label>
							</div>
							<div class="fl">
								<input type="text" class="field" name="phone" value="<?php echo $mobil ?>" placeholder="Mobil">
								<label>Mobil</label>
							</div>
							<div class="fl">
								<select name="country" class="field">
									<option value="Hungary">Hungary</option>
								</select>
								<label>Ország</label>
							</div>
							<div class="fl">
								<input type="text" class="field" name="city" value="<?php echo $city ?>" placeholder="Város *" required>
								<label>Város</label>
							</div>
							<div class="fl">
								<input type="text" class="field" name="postcode" value="<?php echo $postcode ?>" placeholder="Irányitószám *" required>
								<label>Irányitószám</label>
							</div>
							<div class="fl">
								<input type="text" class="field" name="address" value="<?php echo $address ?>" placeholder="Utca, házszám *" required>
								<label>Utca, házszám</label>
							</div>
							<div class="fl">
								<textarea name="note" class="field" placeholder="Megjegyzés (emelet, ajtó stb.)"></textarea>
								<label>Megjegyzés (emelet, ajtó stb.)</label>
							</div>
						</div>
					</div>
					<div>
						<div class="cart_box">
							<div>
								<h3>Kosár tartalma</h3>
								<span onclick="toggleModal('modal_cart')">Kosár szerkesztése</span>
							</div>
							<div class="cart">
								<div v-if="cart.length > 0" v-for="item in cart">
									<div v-cloak class="name">
										<img :src="'files/images/products/compressed/'+item.img">
										<a :href="'product.php?id='+item.id">{{item.name}}</a>
									</div>
									<div v-cloak class="quantity">x{{item.quantity}}</div>
									<div v-cloak class="price">{{formatPrice(item.price)}} Ft</div>
								</div>
							</div>
						</div>
						<div class="shipping pay">
							<h3>Szállítási Mód</h3>
							<div class="radio">
								<div for="radio1">
									<input type="radio" id="shipping1" name="radio_ship" class="selected" value="1" checked>
									<label for="shipping1">Normál szállítás (3-5 nap) <b>990Ft</b></label>
								</div>
								<div>
									<input type="radio" id="shipping2" name="radio_ship" value="2">
									<label for="shipping2">Express szállítás (1-3 days) <b>1.990Ft</b></label>
								</div>
							</div>
						</div>	
						<div class="pay">
							<h3>Fizetési mód</h3>
							<div class="radio">
								<div for="radio1">
									<input type="radio" id="radio1" name="radio_pay" class="selected" value="card" checked>
									<label for="radio1"><img src="files/images/payment.png"></label>
								</div>
								<div>
									<input type="radio" id="radio2" name="radio_pay" value="transfer">
									<label for="radio2">Manuális átutalás</label>
								</div>
								<div>
									<input type="radio" id="radio3" name="radio_pay" value="cash">
									<label for="radio3">Készpénzes fizetés átvételkor</label>
								</div>
							</div>
						</div>
						<div class="cupon">
							<h3>Kupon kód</h3>
                            <div class="input_button">
								<div class="fl">
									<input type="text" v-model="couponCode" name="coupon" class="field" placeholder="Kupon kód">
									<label>Kupon kód</label>
								</div>
								<div class="button" @click="checkCoupon()">Beváltás</div>
							</div>
                            <h4 v-cloak v-if="discount>0">{{discount}}%-os kedvezmény</h4>
						</div>
						<div class="sum">
							<span>Teljes összeg:</span>
							<span v-cloak><b>{{formatPrice(priceSum()-priceSum()/100*discount)}} Ft</b>(Áfa: {{formatPrice((priceSum()-priceSum()/100*discount)/100*27)}}Ft)</span>
						</div>
						<input type="hidden" name="products" :value="cart">	
						<input type="submit" name="submit_checkout" value="Megrendelés" class="button">	
					</div>
				</form>
			</div>
			<div class="cart_empty">
                <h2 v-if="cart.length==0">Üres a kosarad</h2>
                <a href="products.php" class="button">Összes termék</a>
			</div>
			
			<?php include "footer.php" ?>
		</div>
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				<?php
					if(isset($_GET['coupon'])){
						echo "window.vue.couponCode='".$_GET['coupon']."';";
					}
				?>
				window.vue.checkCoupon();
			});
		</script>
	</body>
</html>