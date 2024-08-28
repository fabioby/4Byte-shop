<!DOCTYPE html>
<html>
	<?php include "head.php"; ?>

	<body>
		<?php 
			if(isset($_GET['token_reset'])){

				$token = $_GET['token_reset'];
				$data = querySQL("SELECT active,token FROM users WHERE token=".$token);
				if(!empty($data)){
					$token = $data[0]['token'];
		?>
		<div class="reset">
			<h2>Új jelszó beállítás</h2>
			<form method="POST" enctype="multipart/form-data">
				<div class="fl">
					<input type="text" class="field" name="pass" placeholder="Új jelszó" required>
					<label>Új jelszó *</label>
				</div>
				<div class="fl">
					<input type="text" class="field" name="pass2" placeholder="Új jelszó megerősítése" required>
					<label>Új jelszó megerősítése *</label>
				</div>
				<input type="hidden" name="token" value="<?php echo $token ?>">
				<input type="submit" name="submit_new_pass" class="button" value="Mentés">
			</form>
		</div>
		<?php } } include 'footer.php' ?>
	</body>
</html>