<!DOCTYPE html>
<html lang="hu">
    <?php 
        include "server.php";
        include "head.php";
    ?>
	<body>
		<div id="vue">
			<?php include "header.php" ?>
			
			<div class="bg">
				<div class="layer"></div>
				<div>
					<h2>Galéria</h2>
				</div>
			</div>
			
			<div class="gallery">
				<div>
                    <div class="page_num">
                        <a href="gallery.php?page=1"><i class="fa fa-angle-double-left"></i></a>
                        <?php
                            //$type = isset($_GET['type']) ? $_GET['type'] : 0;
                            $page = isset($_GET['page']) ? $_GET['page'] : 1;
                            $data_id = querySQL("SELECT id FROM gallery");
                            $maxItems = 10;
                            $pageCount = ceil(count($data_id)/$maxItems);
                            
                            for ($i=1; $i <= $pageCount; $i++) {
                                $selected = $i==$page ? " class='selected'" : "";
                                echo '<a href="gallery.php?page='.$i.'"'.$selected.'>'.$i.'</a>';
                            }
                        ?>
                        <a href="gallery.php?page=<?php echo $pageCount ?>"><i class="fa fa-angle-double-right"></i></a>
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
                                </div>';
                            }
                        ?>
                    </div>
                    <div class="page_num">
                        <a href="gallery.php?page=1"><i class="fa fa-angle-double-left"></i></a>
                        <?php
                            //$type = isset($_GET['type']) ? $_GET['type'] : 0;
                            $page = isset($_GET['page']) ? $_GET['page'] : 1;
                            $data_id = querySQL("SELECT id FROM gallery");
                            $maxItems = 10;
                            $pageCount = ceil(count($data_id)/$maxItems);
                            
                            for ($i=1; $i <= $pageCount; $i++) {
                                $selected = $i==$page ? " class='selected'" : "";
                                echo '<a href="gallery.php?page='.$i.'"'.$selected.'>'.$i.'</a>';
                            }
                        ?>
                        <a href="gallery.php?page=<?php echo $pageCount ?>"><i class="fa fa-angle-double-right"></i></a>
                    </div>
                </div>
			</div>
			<?php include "footer.php" ?>
		</div>
	</body>
    <script>        
        window.onload = function() {
            lightGallery(document.getElementById('lightgallery'), {
                selector: '.selector',
            });
        }
    </script>
</html>