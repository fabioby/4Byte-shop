<?php
	if(isset($_COOKIE["email"])){
		$email = $_COOKIE["email"];
		$pass = $_COOKIE["pass"];
		$check = "checked";
	} else {
		$email = "fabian.obersovszky@gmail.com";
		$pass = "123";
		$check = "";
	}
?>
<div class="modal login" id="modal_login">
	<div class="modal_bg" onclick="toggleModal('modal_login')"></div>
	<form class="modal_window" method="POST" enctype="multipart/form-data">
		<div class="modal_header">
			<h3>Bejelentkezés</h3>
			<i class="fa fa-times" aria-hidden="true" onclick="toggleModal('modal_login')"></i>
		</div>
		<div class="modal_body">
			<div class="fl">
				<input type="email" class="field" name="email" value="<?php echo $email ?>" placeholder="Email cím *" required>
				<label>Email cím</label>
			</div>
			<div class="fl">
				<input type="password" class="field" name="pass" value="<?php echo $pass ?>" placeholder="Jelszó *" required>
				<label>Jelszó</label>
			</div>	
			<div>
				<div class="checkbox">
					<input type="checkbox" id="save" name="remember" <?php echo $check ?>>
					<label for="save">Jelszó megjegyzés</label>
				</div>
				<div class="reset_link" onclick="toggleModal('modal_reset');toggleModal('modal_login')">Elfelejtett jelszó</div>
			</div>
			<input type="hidden" name="url" value="<?php echo basename($_SERVER['PHP_SELF']) ?>">
		</div>
		<div class="modal_footer">
			<button type="submit" name="submit_login" class="button"><i class="fa fa-sign-in"></i> Belépés</button>
			<div onclick="toggleModal('modal_register');toggleModal('modal_login')" class="button button3">Új fiók</div>
		</div>
	</form>
</div>

<div class="modal login" id="modal_reset">
	<div class="modal_bg" onclick="toggleModal('modal_reset')"></div>
	<div class="modal_window">
		<div class="modal_header">
			<h3>Új jelszó</h3>
			<i class="fa fa-times" aria-hidden="true" onclick="toggleModal('modal_reset')"></i>
		</div>
		<div class="modal_body">
			<form method="POST" enctype="multipart/form-data">
				<p>Kérjük adja meg a visszaállitandó fióhoz tartozó email címet, majd ellenőrizze posta fiókját a művelet folytatásához.</p>
				<div class="input_button">
					<input type="email" class="field" name="email" placeholder="Email cím *" required>
					<input type="submit" name="submit_reset" value="Küldés" class="button">
				</div>
			</form>
		</div>
		<div class="modal_footer">
			<div>
				<button onclick="toggleModal('modal_login');toggleModal('modal_reset')" class="button">Belépés</button>
			</div>
		</div>
	</div>
</div>