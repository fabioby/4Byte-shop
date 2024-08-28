<div class="modal login register" id="modal_register">
	<div class="modal_bg" onclick="toggleModal('modal_register')"></div>
	<form class="modal_window" method="POST" id="form_register" enctype="multipart/form-data" onsubmit="return register()">
		<div class="modal_header">
			<h3>Új fiók</h3>
			<i class="fa fa-times" aria-hidden="true" onclick="toggleModal('modal_register')"></i>
		</div>
		<div class="modal_body">
			<h3>Személyes adatok:</h3>
			<div class="fl">
				<input type="text" class="field" name="name" placeholder="Name *" required>
				<label>Név</label>
			</div>
			<div class="fl">
				<input type="email" class="field" name="email" placeholder="E-mail cím *" required>
				<label>E-mail cím</label>
			</div>
			<div class="fl">
				<input type="text" class="field" name="phone" placeholder="Telefonszám *" required>
				<label>Telefonszám</label>
			</div>
			<div class="fl">
				<input type="password" class="field" name="pass" value="" autocomplete="new-password" placeholder="Jelszó *" required>
				<label>Jelszó </label>
			</div>
			<div class="fl">
				<input type="password" class="field" name="pass2" placeholder="Jelszó megismétlése *" required>
				<label>Jelszó megismétlése</label>
			</div>

			<h3>Számlázási adatok:</h3>
			<div class="fl">
				<select name="country" class="field">
					<option value="Hungary" selected>Hungary</option>
					<option value="Germany">Germany</option>
				</select>
				<label>Ország</label>
			</div>
			<div class="fl">
				<input type="text" class="field" name="city" placeholder="Város *" required>
				<label>Város</label>
			</div>
			<div class="fl">
				<input type="text" class="field" name="postcode" placeholder="Irányítószám *" required>
				<label>Irányítószám</label>
			</div>
			<div class="fl">
				<input type="text" class="field" name="address" placeholder="Utca és házszám *" required>
				<label>Utca és házszám</label>
			</div>

			<div class="checkbox">
				<input type="checkbox" name="terms" id="terms" required>
				<div>
					<label for="terms">Elfogadom a felhasználói feltételeket *</label>
					<a href="terms.php" target="_blank">Elolvasom az Általános szerződési feltételeket</a>
				</div>
			</div>
			<div id="register_finish">
				<i class="fa fa-envelope"></i>
				<h2>Ellenőrizze postafiókját</h2>
				<p>A regisztrációs folyamat befejezéséhez erősítse meg a regisztrációt levelezési fiókján keresztül.</p>
			</div>
		</div>
		<div class="modal_footer">
			<input type="submit" name="submit_register" value="Regisztráció" class="button">
			<div onclick="toggleModal('modal_login');toggleModal('modal_register')" class="button button3"><i class="fa fa-sign-in"></i> Vissza</div>
		</div>
	</form>
</div>