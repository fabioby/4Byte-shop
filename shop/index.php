<!DOCTYPE html>
<html lang="hu">
	<?php
        include "server.php";
        include "head.php";
    ?>
	<body>
		<div id="vue">
			<?php include "header.php" ?>

			<div class="index">
				<div class="swiper swiper_index">
					<div class="swiper-wrapper">
						<div class="swiper-slide slide1">
							<div></div>
							<div>
								<h1>25%-os kupon!</h1>
								<p>Használd a <b>FG5HD1</b> kódot és vásárolj kedvezményesen!</p>
								<a href="checkout.php?coupon=FG5HD1" class="button">Beváltom</a>
							</div>
						</div>
						<div class="swiper-slide slide2">
							<div></div>
							<div>
								<h1>Szolgáltatások</h1>
								<p>Tekintse meg a mindennapokat megkönnyitő szolgáltatásainkat.</p>
								<a href="services.php" class="button">Szolgáltatások</a>
							</div>
						</div>
						<div class="swiper-slide slide2">
							<div></div>
							<div>
								<h1>Új évi leárazások!</h1>
								<p>Tekintse meg termékeinket akciósan! Minden kedvezmény 31.01.24-ig érvényes.</p>
								<a href="products.php?order=discount+DESC&page=1" class="button">Kedvezmények</a>
							</div>
						</div>
					</div>
					<div class="swiper-pagination"></div>
				</div>

				<div class="product_swiper">		
					<h2>Kedvezményes termékek</h2>
					<div class="swiper swiper_products">
						<div class="swiper-wrapper">
							<?php
								$data = querySQL("SELECT * FROM products WHERE discount>0 ORDER BY id DESC LIMIT 0,8");
								foreach ($data as $key => $value) {
									$img = explode(",",$value["img"]);
									$img = explode(".",$img[0]);
									$img = $img[0]."_c.".$img[1];
									$price = $value["price"];
									$discount = $value["discount"];
									$category = querySQL("SELECT name FROM categories WHERE id=".$value["id_category"])[0]["name"];
									$price_discount = $price;
									if($discount==0){
										$price = "";
									}
									else {
										$price_discount = ($price - $price / 100 * $discount);
										$discount = "-".$discount."%";
									}
							?>
							<div class="swiper-slide">
								<div class="product_box">
									<a href="product.php?id=<?php echo $value["id"] ?>">
										<div>
											<img src="files/images/products/compressed/<?php echo $img ?>">
											<?php if($discount!="0"){ ?><div class="percent_box"><?php echo $discount ?></div><?php } ?>
										</div>
										<div>
											<div>
												<div class="type"><?php echo $category ?></div>
												<div class="title"><h2><?php echo $value["name"] ?></h2></div>
											</div>
											<div>
												<div class="stock">
													<?php 
														if($value["stock"]>0){
															echo "<span><div></div>Raktáron ".$value["stock"]." db</span>";
														}
														else {
															echo "<span><div style='background:#DAC400'></div>Rendelésre</span>";
														}
													?>
												</div>
												<div class="price">
													<span class="p"><?php echo strlen($price)>0 ? "{{formatPrice(".$price.")}} Ft" : "" ?></span>
													<span class="pd"><?php echo "{{formatPrice(".$price_discount.")}} Ft" ?></span>
												</div>
											</div>
											<div class="button"><i class="fa fa-shopping-bag"></i> Tovább</div>
										</div>
									</a>
								</div>
							</div>
							<?php } ?>
						</div>
						<div class="swiper-pagination"></div>
					</div>	
				</div>
			
				<div class="info_row">
					<div>
						<div><i class="fas fa-truck"></i></div>
						<div><b>Ingyenes szállítás</b></div>
						<div><p>Ingyenes szállítás 10,000Ft-os vásárlás esetén</p></div>
					</div>
					<div>
						<div><i class="fa-solid fa-clock"></i></div>
						<div><b>Gyors szállítás</b></div>
						<div><p>Raktárkészlet esetén 1-3 napos expressz szállítási idő</p></div>
					</div>
					<div>
						<div><i class='fas fa-lock'></i></div>
						<div><b>Biztonság</b></div>
						<div><p>14 napos pénzvisszafizetési garancia!</p></div>
					</div>
				</div>
			</div>

			<?php include 'footer.php' ?>
		</div>
	</body>
</html>