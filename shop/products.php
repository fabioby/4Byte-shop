<!DOCTYPE html>
<html lang="hu">
    <?php 
        include "server.php";
        include "head.php";
    ?>
	<body onload="window.vue.fillProducts()">
		<div id="vue">
			<?php include "header.php" ?>
			
			<div class="bg">
				<div class="layer"></div>
				<div>
					<h2>Termékek</h2>
				</div>
			</div>
			
			<div class="products">
				<div>
                    <div class="filters">
                        <div class="fl">
                            <select id="select_type" class="field">
                                <option value="*" selected>Minden kategória</option>	
                                <?php
                                    //WHERE name LIKE '%"+search+"' AND id_category = '"+type+"' ORDER BY "+order+" LIMIT "+page+","+maxItems;
                                    $data = querySQL("SELECT DISTINCT categories.id,categories.name FROM products INNER JOIN categories ON products.id_category = categories.id");
                                    $category = isset($_GET['category']) ? $_GET['category'] : "*";
                                    $order = isset($_GET['order']) ? $_GET['order'] : "id+DESC";
                                    $search = isset($_GET['search']) ? $_GET['search'] : "";
                                    //$page = isset($_GET['page']) ? $_GET['page'] : 1;
                                    
                                    foreach ($data as $value) {
                                        $id = $value['id'];
                                        $name = $value['name'];
                                        $selected = isset($category) && $category == $id ? "selected" : "";
                                        echo '<option value="'.$id.'" '.$selected.'>'.$name.'</option>';
                                    }
                                ?>
                            </select>
                            <label>Kategória</label>
                        </div>
                        <div class="fl">
                            <select id="select_order" class="field">
                                <option value="id DESC" selected>Rendezés: Legújabb</option>
                                <option value="discount DESC" <?php if(isset($order) && $order == "discount DESC") echo 'selected' ?>>Rendezés: Kedvezmény</option>
                                <option value="price ASC" <?php if(isset($order) && $order == "price ASC") echo 'selected' ?>>Rendezés: Ár növekvő</option>
                                <option value="price DESC" <?php if(isset($order) && $order == "price DESC") echo 'selected' ?>>Rendezés: Ár csökkenő</option>
                            </select>
                            <label>Rendezés</label>
                        </div>
                        <div class="input_button">
                            <input id="search" type="text" class="field" value="<?php echo $search ?>" placeholder="Keresés..">
                            <button id="submit_search" class="button">Keresés</button>
                        </div>
                    </div>
                    <div class="page_num">          
                        <span @click="makeURL(''+1)"><i class="fa fa-angle-double-left"></i></span>
                        <span v-cloak v-for="index in pageCount" :key="index" @click="makeURL(''+index)" :class="{'selected':index==getPage()}">{{index}}</span>
                        <span @click="makeURL(''+pageCount)"><i class="fa fa-angle-double-right"></i></span>
                    </div>
                    <!--<div class="display">
                        <div class="fl">
                            <select id="select_order" class="field">
                                <option value="id DESC" selected>Rendezés: Legújabb</option>
                                <option value="discount DESC" <?php if(isset($order) && $order == "discount DESC") echo 'selected' ?>>Rendezés: Kedvezmény</option>
                                <option value="price ASC" <?php if(isset($order) && $order == "price ASC") echo 'selected' ?>>Rendezés: Ár növekvő</option>
                                <option value="price DESC" <?php if(isset($order) && $order == "price DESC") echo 'selected' ?>>Rendezés: Ár csökkenő</option>
                            </select>
                            <label>Rendezés</label>
                        </div>
                        <div class="fl">
                            <select id="select_layout" class="field">
                                <option value="0" selected>Négyzetrácsos elrendezés</option>
                                <option value="1">Soros elrendezés</option>
                            </select>
                            <label>Elrendezés</label>
                        </div>
                    </div>-->
                    <div v-cloak id="product_list">
                        <div v-for="item in products" class="product_box">
                            <a :href="'product.php?id=' + item.id">
                                <div>
                                    <img :src="'files/images/products/compressed/'+item.img">
                                    <div v-if="item.discount > 0" class="percent_box">-{{item.discount}}%</div>
                                </div>
                                <div>
                                    <div>
                                        <div class="type">{{item.category}}</div>
                                        <div class="title"><h2>{{item.name}}</h2></div>  
                                    </div>
                                    <div>
                                        <div class="stock">
                                            <span v-if="item.stock>0"><div></div>Raktáron {{item.stock}} db</span>
                                            <span v-if="item.stock==0"><div style="background:#DAC400"></div>Rendelésre</span>
                                        </div>
                                        <div class="price">
                                            <span class="p" v-if="item.discount > 0">{{formatPrice(item.price)}} Ft</span>
                                            <span class="pd">{{formatPrice(item.price-item.price/100*item.discount)}} Ft</span>
                                        </div>
                                    </div>
                                    <div class="button"><i class="fa fa-shopping-bag"></i> Kosárba</div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="page_num">          
                        <span @click="makeURL(''+1)"><i class="fa fa-angle-double-left"></i></span>
                        <span v-cloak v-for="index in pageCount" :key="index" @click="makeURL(''+index)" :class="{'selected':index==getPage()}">{{index}}</span>
                        <span @click="makeURL(''+pageCount)"><i class="fa fa-angle-double-right"></i></span>
                    </div>
                </div>
			</div>
			<?php include "footer.php" ?>
		</div>
	</body>
</html>