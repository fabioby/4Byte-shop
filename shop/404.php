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
					<h2>404-es hiba</h2>
				</div>
			</div>
			
			<div class="e404 page">
				<div>
                    <h2>A keresett oldal nem található</h2>
                </div>
			</div>
			<?php include "footer.php" ?>
		</div>
	</body>
</html>