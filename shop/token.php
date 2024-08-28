<!DOCTYPE html>
<html>
	<?php include "head.php"; ?>

	<body>
		<?php 
		include "header.php";

		if(isset($_GET['token'])){

			$token = $_GET['token'];
			$sql = "SELECT active,token FROM users WHERE token=".$token;
			$data = querySQL($sql);
			$active = $data[0]['active'];
			$token = $data[0]['token'];

			if($active == 0){
				querySQL("UPDATE users SET active='1'");
				header("Refresh:0; url=index.php?login");
				echo "<script>alert('Account succesfully activated. You may now login.');</script>";
			}
			else {
		?>

		<div>
			<h2>Set new password</h2>
			<form method="POST" enctype="multipart/form-data">
				<div class="fl">
					<input type="text" class="field" name="name" placeholder="New password *" required>
					<label>New password *</label>
				</div>
				<div class="fl">
					<input type="text" class="field" name="name" placeholder="Comfirm new password *" required>
					<label>Comfirm new password *</label>
				</div>
				<input type="submit_reset" class="button" value="Reset">
			</form>
		</div>

		<?php } } include 'footer.php' ?>
	</body>
</html>