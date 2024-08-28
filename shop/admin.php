<!DOCTYPE html>
<?php 
	include "server.php";
	if(isset($_SESSION['logged_in'])){
		$s_id = $_SESSION['logged_in'][0];
		$s_name = $_SESSION['logged_in'][1];
		$s_email = $_SESSION['logged_in'][2];
		$s_admin = $_SESSION['logged_in'][3];
	    
	    if($s_admin!=1) {
	        header('Location: index.php');
	        //header("Refresh:0; url=index.php");
        }
        else{
			$tab = isset($_GET['tab']) ? $_GET['tab'] : 0;
?>

<html lang="hu">
    <?php include "head.php" ?>
	<body>
		<div id="vue">
            <div class="admin">
    			<div class="admin_header">
    			    <div>
    			        <a href="user.php"><?php echo $s_name ?></a>
    			    </div>
    			    <div>
                        <div @click="toggleTabs(0)" :class="{'selected':tab==0}">Termékek</div>
                        <div @click="toggleTabs(1)" :class="{'selected':tab==1}">Kuponok</div>
                        <div @click="toggleTabs(2)" :class="{'selected':tab==2}">Szolgáltatások</div>
                        <div @click="toggleTabs(3)" :class="{'selected':tab==3}">Galéria</div>
                        <div @click="toggleTabs(4)" :class="{'selected':tab==4}">Beállítások</div>
                        <div @click="toggleTabs(5)" :class="{'selected':tab==5}">Üzletek</div>
    			    </div>
    			</div>
    			<div class="admin_body">
                	<div v-if="tab==0" class="admin_product">
                		<?php
                            $data_products = querySQL("SELECT * FROM products");
                            $data_orders = querySQL("SELECT * FROM orders ORDER BY id DESC");
                		?>
                        <div class="admin_body_header">
                            <h2>Termékek (<?php if (!empty($data_products)) echo count($data_products) ?>)</h2>
                            <button title="Új termék hozzáadása" class="button" onclick="toggleModal('modal_product_add')"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        </div>
                		<div class="item_list">
                			<?php if(!empty($data_products)){ foreach ($data_products as $key => $value) { ?>
                    			<div>
                    				<div><a href="product.php?id=<?php echo $value["id"] ?>"><?php echo $value["name"] ?></a></div>
                    				<div>
                    					<span class="button" @click="fillProduct(<?php echo $value['id'] ?>)" onclick="toggleModal('modal_product_edit')"><i class="fas fa-edit"></i></span>
                    					<a href="user.php?submit_delete_product=<?php echo $value["id"] ?>" class="button" onclick="return confirm('Biztos törlös a terméket?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    					<!--<span class="button" onclick="confirm('Are you sure ?') && removeProduct(<?php //echo $value['id'] ?>)">Delete</a>-->
                    				</div>
                    			</div>
                			<?php } } ?>
                		</div>
                		<!--orders-->
                		<div class="admin_body_header">
                		    <h2>Rendelések (<?php if (!empty($data_orders)) echo count($data_orders) ?>)</h2>
                		</div>
                		<div class="item_list">
                			<?php if(!empty($data_orders)){ foreach ($data_orders as $key => $value) { ?>
                    			<div>
                    				<div><a href="#"><?php echo $value["name"] ?></a></div>
                    				<div>
                    					<span class="button" @click="fillOrder(<?php echo $value['id'] ?>)" onclick="toggleModal('modal_order_edit')"><i class="fas fa-edit"></i></span>
                    					<a href="admin.php?submit_delete_order=<?php echo $value["id"] ?>" class="button" onclick="return confirm('Biztos törlöd a foglalást?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    				</div>
                    			</div>
                			<?php } } ?>
                		</div>
                	</div>
                	<div v-if="tab==1" class="admin_coupon">
                		<?php
                            $data_coupons = querySQL("SELECT * FROM coupons");
                		?>
                		<div class="admin_body_header">
                		    <h2>Kuponok (<?php if (!empty($data_coupons)) echo count($data_coupons) ?>)</h2>
                		    <button title="Új kupon hozzáadása" class="button" onclick="toggleModal('modal_coupon_add')"><i class="fa fa-plus" aria-hidden="true"></i></button>
                		</div>
                		<div class="item_list">
                			<?php if(!empty($data_products)){ foreach ($data_coupons as $key => $value) { ?>
                    			<div>
                    				<div><a href="#"><?php echo $value["code"] ?></a></div>
                    				<div>
                    					<span class="button" @click="fillProduct(<?php echo $value['id'] ?>)" onclick="toggleModal('modal_product_edit')"><i class="fas fa-edit"></i></span>
                    					<a href="user.php?submit_delete_coupon=<?php echo $value["id"] ?>" class="button" onclick="return confirm('Biztos törlös a kupont?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    				</div>
                    			</div>
                			<?php } } ?>
                		</div>
                	</div>
                	<div v-if="tab==2" class="admin_services">
                		<?php
                		    $data_services = querySQL("SELECT * FROM services");
                		    $data_reservations = querySQL("SELECT * FROM reservations");
                		?>
                		<!--services-->
                		<div>
                		    <h2>Szolgáltatások (<?php if (!empty($data_services)) echo count($data_services) ?>)</h2>
                		    <button title="Új szolgáltatás hozzáadása" class="button" onclick="toggleModal('modal_services_add')"><i class="fa fa-plus" aria-hidden="true"></i></button>
                		</div>
                		<div class="item_list">
                			<?php if(!empty($data_services)){ foreach ($data_services as $key => $value) { ?>
                			<div>
                				<div><a href="#"><?php echo $value["name"] ?></a></div>
                				<div>
                					<span class="button" @click="fillService(<?php echo $value['id'] ?>)" onclick="toggleModal('modal_service_edit')"><i class="fas fa-edit"></i></span>
                					<a href="user.php?submit_delete_service=<?php echo $value["id"] ?>" class="button" onclick="return confirm('Biztos törlöd a terméket?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                					<!--<span class="button" onclick="confirm('Are you sure ?') && removeProduct(<?php //echo $value['id'] ?>)">Delete</a>-->
                				</div>
                			</div>
                			<?php } } ?>
                		</div>
                		<!--reservations-->
                		<div class="admin_body_header">
                		    <h2>Foglalások (<?php if (!empty($data_reservations)) echo count($data_reservations) ?>)</h2>
                		</div>
                		<div class="item_list">
                			<?php if(!empty($data_reservations)){ foreach ($data_reservations as $key => $value) { ?>
                    			<div>
                    				<div><a href="#"><?php echo $value["name"] ?></a></div>
                    				<div>
                    					<span class="button" @click="fillReservation(<?php echo $value['id'] ?>)" onclick="toggleModal('modal_reservation_edit')"><i class="fas fa-edit"></i></span>
                    					<a href="user.php?submit_delete_reservation=<?php echo $value["id"] ?>" class="button" onclick="return confirm('Biztos törlöd a foglalást?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    				</div>
                    			</div>
                			<?php } } ?>
                		</div>
                	</div>
                	<div v-if="tab==3" class="admin_gallery">
                		<?php $data = querySQL("SELECT * FROM gallery") ?>
                		<div>
                		    <h2>Galéria (<?php if (!empty($data)) echo count($data) ?>)</h2>
                		    <button title="Új kép hozzáadása" class="button" onclick="toggleModal('modal_gallery_add')"><i class="fa fa-plus" aria-hidden="true"></i></button>
                		</div>
                        <div class="gallery">
                            <div>
                                <div class="page_num">
                                    <a href="admin.php?page=1"><i class="fa fa-angle-double-left"></i></a>
                                    <?php
                                        $type = isset($_GET['type']) ? $_GET['type'] : 0;
                                        $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                        $data_id = querySQL("SELECT id FROM gallery");
                                        $maxItems = 10;
                                        $pageCount = ceil(count($data_id)/$maxItems);
                                        
                                        for ($i=1; $i <= $pageCount; $i++) { 
                                            $selected = $i==$page ? " class='selected'" : "";
                                            echo '<a href="admin.php?page='.$i.'"'.$selected.'>'.$i.'</a>';
                                        }
                                    ?>
                                    <a href="admin.php?page=<?php echo $pageCount ?>"><i class="fa fa-angle-double-right"></i></a>
                                </div>
                                <div id="lightgallery">
                                    <?php
                                        $limit = ($page-1)*$maxItems.",".$maxItems;
                                        $data_images = querySQL("SELECT * FROM gallery ORDER BY id DESC LIMIT $limit");
                                        foreach ($data_images as $value) {
                                            $array_img = explode(".", $value['img']);
                                            $img_c = $array_img[0]."_c.".$array_img[1];
                                            echo '
                                                <div class="selector" data-src="files/images/gallery/'.$value['img'].'">
                                                    <img src="files/images/gallery/compressed/'.$img_c.'"></img>
                                                    <a href="admin.php?submit_delete_image='.$value['id'].';'.$value['img'].'" onclick="return confirm(\'Biztos törlöd a képet?\')">&times</a>
                                                </div>
                                            ';
                                        }
                                    ?>
                                </div>
                                <div class="page_num">
                                    <a href="admin.php?page=1"><i class="fa fa-angle-double-left"></i></a>
                                    <?php                            
                                        for ($i=1; $i <= $pageCount; $i++) { 
                                            $selected = $i==$page ? " class='selected'" : "";
                                            echo '<a href="admin.php?page='.$i.'"'.$selected.'>'.$i.'</a>';
                                        }
                                    ?>
                                    <a href="admin.php?page=<?php echo $pageCount ?>"><i class="fa fa-angle-double-right"></i></a>
                                </div>
                            </div>
                        </div>
                	</div>
                	<div v-if="tab==4" class="admin_settings">
                		<?php
            				$data = querySQL("SELECT * FROM settings WHERE id = 1");
            				foreach ($data as $key => $value) {
            					$main_rgb = explode(",",$value["main_color"]);
            					$second_rgb = explode(",",$value["second_color"]);
            					$main_hex = RgbToHex($main_rgb[0],$main_rgb[1],$main_rgb[2]);
            					$second_hex = RgbToHex($second_rgb[0],$second_rgb[1],$second_rgb[2]);
                		?>
        				<div>
        				    <h2>Weboldal beállítások</h2>
        				</div>
        				<div>
        					<form method="POST" enctype="multipart/form-data">
        						<div class="fl">
        							<input type="text" class="field" name="name" placeholder="Név *" value="<?php echo $value["name"] ?>" required>
        							<label>Név *</label>
        						</div>
        						<div class="fl">
        							<textarea class="field" name="description" placeholder="Leírás *" required><?php echo $value["description"] ?></textarea>
        							<label>Leírás *</label>
        						</div>
        						<div class="fl">
        							<input type="text" class="field" name="ver" placeholder="Szélesség *" value="<?php echo $value["ver"] ?>" required>
        							<label>Verzió *</label>
        						</div>
        						<div class="fl">
        							<input type="text" class="field" name="width" placeholder="Szélesség *" value="<?php echo $value["width"] ?>" required>
        							<label>Szélesség *</label>
        						</div>
        						<div class="fl">
        							<input type="number" class="field" name="radius" placeholder="Elem görbület *" value="<?php echo $value["radius"] ?>" required>
        							<label>Elem görbület *</label>
        						</div>
        						<div class="fl">
        							<input type="color" class="field" name="hex1" placeholder="Fő szín *" value="<?php echo $main_hex ?>" required>
        							<label>Fő szín *</label>
        						</div>
        						<div class="fl">
        							<input type="color" class="field" name="hex2" placeholder="Másodlagos szín *" value="<?php echo $second_hex ?>" required>
        							<label>Másodlagos szín *</label>
        						</div>		
        						<input type="submit" name="submit_settings" value="Mentés" class="button">			
        					</form>
        				</div>
                		<?php } ?>		
                	</div>
                	<div v-if="tab==5" class="premises">
                		<?php $data_premises = querySQL("SELECT * FROM premises") ?>
                		<div>
                		    <h2>Üzletek (<?php if (!empty($data_premises)) echo count($data_premises) ?>)</h2>
                		    <button title="Új üzlet hozzáadása" class="button" onclick="toggleModal('modal_premises_add')"><i class="fa fa-plus" aria-hidden="true"></i></button>
                		</div>
                        <div class="item_list">
                        <?php if(!empty($data_reservations)){ foreach ($data_premises as $key => $value) { ?>
                            <div>
                                <div><a href="#"><?php echo $value["address"] ?></a></div>
                                <div>
                                    <span class="button" @click="fillPremise(<?php echo $value['id'] ?>)" onclick="toggleModal('modal_premises_edit')"><i class="fas fa-edit"></i></span>
                                    <a href="admin.php?submit_remove_premise=<?php echo $value["id"] ?>" class="button" onclick="return confirm('Biztos eltávolítod a üzletet?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        <?php } } ?>
                        </div>
                	</div>
    			</div>

				<!--product add modal-->
				<div class="modal" id="modal_product_add">
					<div class="modal_bg" onclick="toggleModal('modal_product_add')"></div>
					<form class="modal_window" method="POST" enctype="multipart/form-data">
						<div class="modal_header">
							<h3>Új termék</h3>
							<i class="fa fa-times" aria-hidden="true" onclick="toggleModal('modal_product_add')"></i>
						</div>
						<div class="modal_body">
							<div class="fl">
								<input type="text" class="field" name="name" placeholder="Megnevezés *" required>
								<label>Megnevezés</label>
							</div>
							<div class="fl">
								<select name="category" class="field">
									<?php
										$data = querySQL("SELECT * FROM categories");
										foreach ($data as $key => $value) {
											echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
										}
									?>
								</select>
								<label>Kategória</label>
							</div>
							<div class="fl">
								<textarea name="description" class="field" placeholder="Leírás *" required></textarea>
								<label>Leírás</label>
							</div>
							<div class="fl">
								<input type="number" class="field" name="price" placeholder="Ár *" required>
								<label>Ár</label>
							</div>
							<div class="fl">
								<input type="number" class="field" name="discount" min="0" max="100" value="0" placeholder="Kedvezmény *" required>
								<label>Kedvezmény</label>
							</div>
							<div class="fl">
								<input type="number" class="field" name="stock" min="0" value="1" placeholder="Készlet *" required>
								<label>Készlet</label>
							</div>
							<div class="fl">
								<input type="url" class="field" name="url" placeholder="Video link">
								<label>Video link</label>
							</div>
							<div class="fl">
								<input class="field" type="file" name="files[]" multiple required>
								<label>Kép(ek)</label>
							</div>
							<input type="hidden" name="date" value="<?php echo date('Y-m-d');?>">
						</div>
						<div class="modal_footer">
							<input type="submit" name="submit_add_product" value="Hozzáadás" class="button">			
						</div>
					</form>
				</div>
				<!--product edit modal-->
				<div v-for="item in product" class="modal" id="modal_product_edit">
					<div class="modal_bg" onclick="toggleModal('modal_product_edit')"></div>
					<form class="modal_window" method="POST" enctype="multipart/form-data">
						<div class="modal_header">
							<h3>{{item.name}}</h3>
							<i class="fa fa-times" aria-hidden="true" onclick="toggleModal('modal_product_edit')"></i>
						</div>
						<div class="modal_body">
							<div class="fl">
								<input type="text" name="name" class="field" placeholder="Megnevezés *" :value="item.name" required>
								<label>Megnevezés</label>
							</div>
							<div class="fl">
								<select name="id_category" class="field">
									<option v-for="category in categories" :selected="category['id'] == item.category" :value="category['id']">{{category['name']}}</option>
								</select>
								<label>Kategória</label>
							</div>
							<div class="fl">
								<textarea name="description" class="field" placeholder="Leírás *" required>{{item.description}}</textarea>
								<label>Leírás</label>
							</div>
							<div class="fl">
								<input type="text" class="field" name="price" placeholder="Ár *" :value="item.price" required>
								<label>Ár</label>
							</div>
							<div class="fl">
								<input type="number" class="field" name="discount" min="0" max="100" :value="item.discount" placeholder="Kedvezmény *" required>
								<label>Kedvezmény</label>
							</div>
							<div class="fl">
								<input type="number" class="field" name="stock" min="0" :value="item.stock" placeholder="Készlet *" required>
								<label>Készlet</label>
							</div>
							<div class="fl">
								<input type="text" class="field" name="url" :value="item.url" placeholder="Video link">
								<label>Video link</label>
							</div>
							<div class="fl">
								<input class="field" type="file" name="files[]" multiple>
								<label>Kép(ek)</label>
							</div>
							<draggable id="sortable" class="field" :list="item.img">
								<div v-for="(img, index) in item.img">
									<img :src="'files/images/products/'+img">
									<div @click="product[0].img.splice(index,1);">&times</div>
								</div>
							</draggable>
							<input type="hidden" name="id" :value="item.id">		
							<input type="hidden" name="img" :value="item.img">		
						</div>
						<div class="modal_footer">						
							<input type="submit" name="submit_edit_product" value="Mentés" class="button">			
						</div>
					</form>
				</div>
				<!--order edit modal-->
				<div v-for="item in order" class="modal" id="modal_order_edit">
					<div class="modal_bg" onclick="toggleModal('modal_order_edit')"></div>
					<form class="modal_window" method="POST" enctype="multipart/form-data">
						<div class="modal_header">
							<h3>{{item.name}}</h3>
							<i class="fa fa-times" aria-hidden="true" onclick="toggleModal('modal_order_edit')"></i>
						</div>
						<div class="modal_body">
							<div class="fl">
								<input type="text" name="name" class="field" placeholder="Név *" :value="item.name" required>
								<label>Név</label>
							</div>
							<div class="fl">
								<input type="text" name="email" class="field" placeholder="Email *" :value="item.email" required>
								<label>Email</label>
							</div>
							<div class="fl">
								<input type="text" name="phone" class="field" placeholder="Mobil *" :value="item.phone" required>
								<label>Mobil</label>
							</div>
							<div class="fl">
								<input type="text" name="address" class="field" placeholder="Szállítási cím *" :value="item.address" required>
								<label>Szállítási cím</label>
							</div>
							<div class="fl">
								<textarea name="note" class="field" placeholder="Megjegyzés">{{item.note}}</textarea>
								<label>Megjegyzés</label>
							</div>
							<div class="fl">
								<select name="payment" class="field">
									<option value="cash" :selected="item.payment=='cash'">Készpénz</option>
									<option value="card" :selected="item.payment=='card'">Kártya</option>
								</select>
								<label>Fizetési mód</label>
							</div>
							<div class="fl">
								<select name="isPayed" class="field">
									<option value="0" :selected="item.isPayed==0">KIFIZETETLEN</option>
									<option value="1" :selected="item.isPayed==1">FIZETVE</option>
								</select>
								<label>Fizetve</label>
							</div>
							<div class="fl">
								<input type="datetime-local" class="field" name="datetime" :value="item.dateTime" placeholder="Dátum *" required>
								<label>Dátum</label>
							</div>
							<div v-for="item in products">
								<div class="name">
									<img :src="'files/images/products/compressed/'+item.img">
									<a :href="'product.php?id='+item.id">{{item.name}}</a>
								</div>
								<div class="quantity"><input type="number" class="field" :value="item.quantity" :data-id="item.id" @change="changeQuantity" min="1"></div>
								<div class="price">{{item.price}} Ft</div>
							</div>
							<input type="hidden" name="id" :value="item.id">
						</div>
						<div class="modal_footer">						
							<input type="submit" name="submit_update_order" value="Mentés" class="button">			
						</div>
					</form>
				</div>
				<!--coupon add modal-->
				<div class="modal" id="modal_coupon_add">
					<div class="modal_bg" onclick="toggleModal('modal_coupon_add')"></div>
					<form class="modal_window" method="POST" enctype="multipart/form-data">
						<div class="modal_header">
							<h3>Új termék</h3>
							<i class="fa fa-times" aria-hidden="true" onclick="toggleModal('modal_coupon_add')"></i>
						</div>
						<div class="modal_body">
							<div class="fl">
								<input type="text" class="field" name="code" placeholder="Kód *" required>
								<label>Kód</label>
							</div>
							<div class="fl">
								<input type="number" class="field" name="discount" placeholder="Kedvezmény (%) *" required>
								<label>Kedvezmény (%)</label>
							</div>
							<div class="fl">
								<input type="date" min="<?php echo date('Y-m-d') ?>" class="field" name="date" placeholder="Lejárati dátum *" required>
								<label>Lejárati dátum</label>
							</div>
						</div>
						<div class="modal_footer">
							<input type="submit" name="submit_add_coupon" value="Hozzáadás" class="button">			
						</div>
					</form>
				</div>
				<!--service add modal-->
				<div class="modal" id="modal_services_add">
					<div class="modal_bg" onclick="toggleModal('modal_services_add')"></div>
					<form class="modal_window" method="POST" enctype="multipart/form-data">
						<div class="modal_header">
							<h3>Új szolgáltatás</h3>
							<i class="fa fa-times" aria-hidden="true" onclick="toggleModal('modal_services_add')"></i>
						</div>
						<div class="modal_body">
							<div class="fl">
								<input type="text" class="field" name="name" placeholder="Név *" required>
								<label>Név</label>
							</div>
							<div class="fl">
								<textarea name="description" class="field" placeholder="Leírás *" required></textarea>
								<label>Leírás</label>
							</div>
							<div class="fl">
								<input type="number" class="field" name="duration" placeholder="Időtartam *" required>
								<label>Időtartam</label>
							</div>
							<div class="fl">
								<input type="number" class="field" name="price" placeholder="Ár *" required>
								<label>Ár</label>
							</div>
							<div class="fl">
								<input class="field" type="file" name="files[]" multiple required>
								<label>Kép(ek)</label>
							</div>
						</div>
						<div class="modal_footer">						
							<input type="submit" name="submit_add_service" value="Hozzáadás" class="button">			
						</div>
					</form>
				</div>
				<!--service edit modal-->
				<div v-for="item in service" class="modal" id="modal_service_edit">
					<div class="modal_bg" onclick="toggleModal('modal_service_edit')"></div>
					<form class="modal_window" method="POST" enctype="multipart/form-data">
						<div class="modal_header">
							<h3>{{item.name}}</h3>
							<i class="fa fa-times" aria-hidden="true" onclick="toggleModal('modal_service_edit')"></i>
						</div>
						<div class="modal_body">
							<div class="fl">
								<input type="text" name="name" class="field" placeholder="Név *" :value="item.name" required>
								<label>Név</label>
							</div>
							<div class="fl">
								<textarea name="description" class="field" placeholder="Leírás *" required>{{item.description}}</textarea>
								<label>Leírás</label>
							</div>
							<div class="fl">
								<input type="number" class="field" name="duration" min="5" :value="item.duration" placeholder="Időtartam *" required>
								<label>Időtartam</label>
							</div>
							<div class="fl">
								<input type="number" class="field" name="price" :value="item.price" placeholder="Ár *" required>
								<label>Ár</label>
							</div>
							<div class="fl">
								<input class="field" type="file" name="files[]" multiple>
								<label>Kép(ek)</label>
							</div>
							<draggable id="sortable" class="field" :list="item.img">
								<div v-for="(img, index) in item.img">
									<img :src="'files/images/services/'+img">
									<div @click="service[0].img.splice(index,1);">&times</div>
								</div>
							</draggable>
							<input type="hidden" name="id" :value="item.id">		
						</div>
						<div class="modal_footer">			
							<input type="submit" name="submit_edit_service" value="Mentés" class="button">
						</div>
					</form>
				</div>
				<!--reservation edit modal-->
				<div v-for="item in reservation" class="modal" id="modal_reservation_edit">
					<div class="modal_bg" onclick="toggleModal('modal_reservation_edit')"></div>
					<form class="modal_window" method="POST" enctype="multipart/form-data">
						<div class="modal_header">
							<h3>{{item.name}}</h3>
							<i class="fa fa-times" aria-hidden="true" onclick="toggleModal('modal_reservation_edit')"></i>
						</div>
						<div class="modal_body">
							<div class="fl">
								<input type="text" name="name" class="field" placeholder="Név *" :value="item.name" required>
								<label>Név</label>
							</div>
							<div class="fl">
								<textarea name="email" class="field" placeholder="Email *" required>{{item.email}}</textarea>
								<label>Email</label>
							</div>
							<div class="fl">
								<input type="datetime-local" class="field" name="datetime" :value="`${item.date}T${item.time}`" placeholder="Dátum *" required>
								<label>Dátum</label>
							</div>
							<div class="fl">
								<input type="number" class="field" name="duration" min="5" :value="item.duration" placeholder="Időtartam *" required>
								<label>Időtartam</label>
							</div>
							<div class="fl">
								<select name="id_service" class="field">
									<option v-for="service in services" :selected="service['id'] == item.s_id" :value="service['id']">{{service['name']}}</option>
								</select>
								<label>Szolgáltatás</label>
							</div>
							<input type="hidden" name="id" :value="item.id">		
							<input type="hidden" name="img" :value="item.img">		
						</div>
						<div class="modal_footer">			
							<input type="submit" name="submit_edit_reservation" value="Mentés" class="button">			
						</div>
					</form>
				</div>
				<!--gallery add modal-->
				<div class="modal" id="modal_gallery_add">
					<div class="modal_bg" onclick="toggleModal('modal_gallery_add')"></div>
					<form class="modal_window" method="POST" enctype="multipart/form-data">
						<div class="modal_header">
							<h3>Képfeltöltés</h3>
							<i class="fa fa-times" aria-hidden="true" onclick="toggleModal('modal_gallery_add')"></i>
						</div>
						<div class="modal_body">
							<div class="fl">
								<input class="field" type="file" name="files[]" multiple required>
								<label>Kép(ek)</label>
							</div>
						</div>
						<div class="modal_footer">			
							<input type="submit" name="submit_upload_gallery" value="Feltöltés" class="button">		
						</div>
					</form>
				</div>
				<!--premise add modal-->
				<div class="modal modal_premise" id="modal_premises_add">
					<div class="modal_bg" onclick="toggleModal('modal_premises_add')"></div>
					<form class="modal_window" method="POST" enctype="multipart/form-data">
						<div class="modal_header">
							<h3>Új üzlet</h3>
							<i class="fa fa-times" aria-hidden="true" onclick="toggleModal('modal_premises_add')"></i>
						</div>
						<div class="modal_body">
							<div class="fl">
								<input type="text" class="field" name="address" placeholder="Cím *" required>
								<label>Cím</label>
							</div>
							<div class="opentimes">
								<div>
									<div><input type="checkbox" v-model="monday" checked>Hétfő:</div>
									<div>
										<b v-if="!monday">Zárva</b>
										<input v-if="monday" class="field" type="time" name="m0" value="10:00">
										<input v-if="monday" class="field" type="time" name="m1" value="20:00">
									</div>
								</div>
								<div>
									<div><input type="checkbox" v-model="tuesday" checked>Kedd:</div>
									<div>
										<b v-if="!tuesday">Zárva</b>
										<input v-if="tuesday" class="field" type="time" name="t0" value="10:00">
										<input v-if="tuesday" class="field" type="time" name="t1" value="20:00">
									</div>
								</div>
								<div>
									<div><input type="checkbox" v-model="wednesday" checked>Szerda:</div>
									<div>
										<b v-if="!wednesday">Zárva</b>
										<input v-if="wednesday" class="field" type="time" name="th0" value="10:00">
										<input v-if="wednesday" class="field" type="time" name="th1" value="20:00">
									</div>
								</div>
								<div>
									<div><input type="checkbox" v-model="thursday" checked>Csütörtök:</div>
									<div>
										<b v-if="!thursday">Zárva</b>
										<input v-if="thursday" class="field" type="time" name="w0" value="10:00">
										<input v-if="thursday" class="field" type="time" name="w1" value="20:00">
									</div>
								</div>
								<div>
									<div><input type="checkbox" v-model="friday" checked>Péntek:</div>
									<div>
										<b v-if="!friday">Zárva</b>
										<input v-if="friday" class="field" type="time" name="f0" value="10:00">
										<input v-if="friday" class="field" type="time" name="f1" value="20:00">
									</div>
								</div>
								<div>
									<div><input type="checkbox" v-model="saturday" checked>Szombat:</div>
									<div>
										<b v-if="!saturday">Zárva</b>
										<input v-if="saturday" class="field" type="time" name="s0" value="10:00">
										<input v-if="saturday" class="field" type="time" name="s1" value="18:00">
									</div>
								</div>
								<div>
									<div><input type="checkbox" v-model="sunday" checked>Vasárnap:</div>
									<div>
										<b v-if="!sunday">Zárva</b>
										<input v-if="sunday" class="field" type="time" name="su0" value="10:00">
										<input v-if="sunday" class="field" type="time" name="su1" value="18:00">
									</div>
								</div>
							</div>
						</div>
						<div class="modal_footer">
							<input class="button" type="submit" name="submit_add_premise" value="Mentés">
						</div>
					</form>
				</div>
				<!--premise edit modal-->
				<div v-for="item in premise" class="modal modal_premise" id="modal_premises_edit">
					<div class="modal_bg" onclick="toggleModal('modal_premises_edit')"></div>
					<form class="modal_window" method="POST" enctype="multipart/form-data">
						<div class="modal_header">
							<h3>{{item.address}}</h3>
							<i class="fa fa-times" aria-hidden="true" onclick="toggleModal('modal_premises_edit')"></i>
						</div>
						<div class="modal_body">
							<div class="fl">
								<input type="text" class="field" name="address" :value="item.address" placeholder="Cím *" required>
								<label>Cím</label>
							</div>
							<div class="opentimes">
								<div>
									<div><input type="checkbox" v-model="monday" checked>Hétfő:</div>
									<div>
										<b v-if="!monday">Zárva</b>
										<input v-if="monday" class="field" type="time" name="m0" :value="item.openTimes[0][0]">
										<input v-if="monday" class="field" type="time" name="m1" :value="item.openTimes[0][1]">
									</div>
								</div>
								<div>
									<div><input type="checkbox" v-model="tuesday" checked>Kedd:</div>
									<div>
										<b v-if="!tuesday">Zárva</b>
										<input v-if="tuesday" class="field" type="time" name="t0" :value="item.openTimes[1][0]">
										<input v-if="tuesday" class="field" type="time" name="t1" :value="item.openTimes[1][1]">
									</div>
								</div>
								<div>
									<div><input type="checkbox" v-model="wednesday" checked>Szerda:</div>
									<div>
										<b v-if="!wednesday">Zárva</b>
										<input v-if="wednesday" class="field" type="time" name="th0" :value="item.openTimes[2][0]">
										<input v-if="wednesday" class="field" type="time" name="th1" :value="item.openTimes[2][1]">
									</div>
								</div>
								<div>
									<div><input type="checkbox" v-model="thursday" checked>Csütörtök:</div>
									<div>
										<b v-if="!thursday">Zárva</b>
										<input v-if="thursday" class="field" type="time" name="w0" :value="item.openTimes[3][0]">
										<input v-if="thursday" class="field" type="time" name="w1" :value="item.openTimes[3][1]">
									</div>
								</div>
								<div>
									<div><input type="checkbox" v-model="friday" checked>Péntek:</div>
									<div>
										<b v-if="!friday">Zárva</b>
										<input v-if="friday" class="field" type="time" name="f0" :value="item.openTimes[4][0]">
										<input v-if="friday" class="field" type="time" name="f1" :value="item.openTimes[4][1]">
									</div>
								</div>
								<div>
									<div><input type="checkbox" v-model="saturday" checked>Szombat:</div>
									<div>
										<b v-if="!saturday">Zárva</b>
										<input v-if="saturday" class="field" type="time" name="s0" :value="item.openTimes[5][0]">
										<input v-if="saturday" class="field" type="time" name="s1" :value="item.openTimes[5][1]">
									</div>
								</div>
								<div>
									<div><input type="checkbox" v-model="sunday" checked>Vasárnap:</div>
									<div>
										<b v-if="!sunday">Zárva</b>
										<input v-if="sunday" class="field" type="time" name="su0" :value="item.openTimes[6][0]">
										<input v-if="sunday" class="field" type="time" name="su1" :value="item.openTimes[6][1]">
									</div>
								</div>
							</div>
						</div>
						<div class="modal_footer">
							<input type="hidden" name="id" :value="item.id">
							<input class="button" type="submit" name="submit_edit_premise" value="Mentés">
						</div>
					</form>
				</div>
            </div>		
		</div>     
        <script>
            window.onload = function() {
                lightGallery(document.getElementById('lightgallery'), {
                    selector: '.selector',
                });

                //$('#lightgallery').lightGallery({
                    //selector: '.selector'
					var tab = <?php echo json_encode($tab); ?>;
					window.vue.toggleTabs(tab);
                }
        </script>
	</body>
</html>

<?php } } else { header("Refresh:0; url=index.php"); } ?>
