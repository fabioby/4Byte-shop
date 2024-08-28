<?php 
    include "cart.php";
    include "login.php";
    include "register.php";

    $link = '';
    $loggenIn = isset($_SESSION['logged_in']);

    if($loggenIn){
        $link = '<a href="user.php"><i class="fa-solid fa-user"></i><span>Profilom</span></a>';
    } else {
        $link = '<span onclick="toggleModal(\'modal_login\')"><i class="fa-solid fa-user"></i><span>Belépés/Regisztráció</span></span>';
    }
?>

<header>
	<div>
		<div class="hl">						
			<div class="logo"><a href="index.php"><img src="files/images/logo.png?ver=2" alt="logo"></a></div>
			<div>
			    <div onclick="toggleModal('modal_search')"><i class="fa fa-search"></i></div>
			    <div onclick="toggleModal('modal_cart')"><i class="fa-solid fa-bag-shopping"></i><span v-cloak v-if="cart.length>0" class="circle">{{cart.length}}</span></div>
    			<div id="bars"><div class="bars"><span></span><span></span><span></span><span></span></div></div>
			</div>
		</div>
		<div class="hn">
			<div>
				<a href="products.php">Termékek</a>
				<a href="services.php">Szolgáltatások</a>
				<a href="gallery.php">Galéria</a>
				<a href="contact.php">Elérhetőség</a>
			</div>
			<div>
                <div onclick="toggleModal('modal_search')">
                    <i class="fa fa-search"></i>
                    <span>Keresés</span>
                </div>
				<div onclick="toggleModal('modal_cart')">
					<i class="fa-solid fa-bag-shopping"></i>
                    <span>Kosár<span v-cloak v-if="cart.length>0" class="circle">{{cart.length}}</span></span>
				</div>
                <div><?php echo $link ?></div>
			</div>
		</div>
	</div>
</header>


<div class="modal search" id="modal_search">
	<div class="modal_bg" onclick="toggleModal('modal_search')"></div>
	<div class="modal_window" style="background:none">
		<h2>Keresés</h2>
		<form class="input_button" action="products.php">
			<input type="text" placeholder="Search.." name="search" class="field" required>
			<input type="submit" value="Search" class="button">
			<a href="products.php?search="></a>
		</form>
	</div>
</div>