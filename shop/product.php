<!DOCTYPE html>
<html lang="hu">
	<?php
        include "server.php";
        include "head.php";
    ?>

	<body>
		<div id="fb-root"></div>
		<script async defer crossorigin="anonymous" src="https://connect.facebook.net/hu_HU/sdk.js#xfbml=1&version=v14.0" nonce="Ysw8pG3a"></script>
		
		<div id="vue">
			<?php include "header.php" ?>
			
			<?php
				$id = $_GET['id'];
				$data = querySQL("SELECT products.id,id_category,products.name,img,description,price,discount,stock,url,categories.name AS cname FROM products INNER JOIN categories ON categories.id = products.id_category WHERE products.id = ".$id);
				if($data != null){
					$img = explode(",",$data[0]['img']);
					$discount = $data[0]["discount"];
					$price = $data[0]["price"];
					$price_discount = $data[0]["price"];
					if($discount==0){
						$price = "";
					}
					else {
                        $price_discount = $price - $price / 100 * $discount;
						$discount = "-".$discount."%";
                        $price = $price;
					}
            ?>	
			<div class="route">
				<div><a href="products.php">Termékek</a></div><div>/</div>
				<div><a href="products.php?category=<?php echo $data[0]["id_category"] ?>"><?php echo $data[0]["cname"] ?></a></div><div>/</div>
				<div><?php echo $data[0]["name"] ?><span>..</span></div>
			</div>

			<div class="product">
				<div>
					<div>
						<div>
							<img id="product_img" src="files/images/products/<?php echo $img[0] ?>">
							<?php if($discount!="0"){ ?><div class="percent_box"><?php echo $discount ?></div><?php } ?>
						</div>
						<div>
							<div class="swiper-button-next"></div>
							<div class="swiper swiper_gallery_bottom">
                                <div class="swiper-wrapper">
                                    <?php if (count($img) > 1){ for ($i = 0; $i < count($img); $i++) { ?>
                                        <div class="swiper-slide">
                                            <img src="files/images/products/<?php echo $img[$i] ?>" onclick="setImg(this)">
                                        </div>
                                    <?php } } ?>
                                </div>
							</div>
							<div class="swiper-button-prev"></div>
						</div>
					</div>
					<div>
						<h2><?php echo $data[0]["name"] ?></h2>
                        <?php if(empty($data[0]['stock'])){ ?>
                            <div class="unavailable"><div></div> Rendelésre, 2-3 hét.</div>
                        <?php } else { ?>
                            <div class="available"><div></div> Készleten <?php echo $data[0]["stock"] ?> db</div>
                        <?php } ?>
						<div class="pc">
							<div>
								<span v-cloak><?php echo strlen($price)>0 ? "{{formatPrice(".$price.")}} Ft" : "" ?></span>
								<span v-cloak><?php echo "{{formatPrice(".$price_discount.")}} Ft" ?></span>
							</div>
							<div class="input_button">
								<div class="fl">
									<input type="number" class="field" id="quantity" value="1" min="1" placeholder="Quantity *" required>
									<label>Mennyiség *</label>
								</div>
								<button v-on:click="addToCart(<?php echo $data[0]["id"] ?>)" onclick="toggleModal('modal_cart')" data-id="<?php echo $data[0]["id"] ?>" class="button"><i class="fa fa-shopping-bag"></i> Kosárba</button>
							</div>
						</div>
						<?php 
							if(!empty($data[0]['stock'])){
								//echo '<div class="eu"><img src="files/images/eu.svg"> A terméket az EU-ban található raktárból szállítják.</div>';
							}
						?>

						<div class="info_span">
							<span><i class='fas fa-lock'></i> <span>Garantáltan biztonságos vásárlás</span></span>
							<span><i class='fa-solid fa-money-bill'></i> <span>Fizetni szállításkor is lehet</span></span>
							<span><i class='fas fa-truck'></i> <span>14 napos visszaküldési lehetőség</span></span>
						</div>
						<div class="fb-share-button" data-href="https://4byte.hu/showroom/drop/product.php" data-layout="button" data-size="large">
							<a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2F4byte.hu%2Fshowroom%2Fdrop%2Fproduct.php&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share on Facebook</a>
						</div>
					</div>
				</div>
				
				<?php if(!empty($data[0]['url'])){ ?>
					<div class="video"><iframe src="<?php echo $data[0]['url'] ?>"></iframe></div>
				<?php } ?>

				<div class="tabs">
					<div>
						<div :class="{'selected':tab==0}" @click="tab=0">Termék leírás <i class='fas fa-angle-down'></i></div>
						<div :class="{'selected':tab==1}" @click="tab=1">Szállítás <i class='fas fa-angle-down'></i></div>
					</div>
					<div :class="{'selected':tab==0}"><p><?php echo $data[0]["description"] ?></p></div>
					<div :class="{'selected':tab==1}"><p>A saját fiókodban a “Korábbi rendelések” menüpontra kattintva követheted a termékek szállítási státuszát, valamint a csomagszámra (AWB-re) kattintva megtekintheted ezt a futárszolgálat oldalán is. Ugyanitt letöltheted elektronikus formában a rendeléshez tartozó számlát és garancialevelet.
						Nem kell felesleges utakat megtenned, mert mi házhoz szállítjuk Neked a megrendelt csomagot!
						Ha nem tudsz a megadott napon a szállítási címen tartózkodni és a futárra várni, vagy nem rendelheted munkahelyedre a csomagot, akkor a Személyes átvételi szolgáltatásunk segítségével kiszállítjuk azt a Hozzád legközelebbi Easyboxba / MPL Pontra.
						Amennyiben további információra van szükséged rendeléseddel kapcsolatban, keresd fel ügyfélszolgálatunkat.</p></div>
				</div>
			</div>
			<?php } include "footer.php" ?>
		</div>
		<script>        
			window.onload = function() {
				lightGallery(document.getElementById('lightgallery'), {
					selector: '.selector',
				});
				$("header").addClass("header");
			}
		</script>
	</body>
</html>