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
					<h2>Elérhetőség</h2>
				</div>
			</div>

			<div class="contact">
                <div>     
                    <div class="chat">
                        <h3>A Facebook Messengeren keresztül biztosítjuk a leggyorsabb választ.</h3>
                        <p>Ügyfélszolgálati csapatunk szívesen meghallgatja Önt és válaszol minden kérdésére.
                        Hétfőtől péntekig 10-16 óráig állunk rendelkezésére.
                        Egyszerűen küldhet nekünk egy e-mailt, vagy indíthat élő csevegést a Facebook Messengeren.</p>
                        <a href="https://m.me/4byte.hu"><i class="fab fa-facebook-messenger"></i><b>Indítsa el az élő üzenetküldő csevegést</b></a>
                    </div>
                    <div>
                        <h3>E-mail kapcsolatfelvételi űrlap</h3>
                        <form method="POST" id="form_mail" onsubmit="return sendMail()">
                            <div class="fl">
                                <input type="text" name="name" class="field" placeholder="Név *" required>
                                <label>Név *</label>
                            </div>
                            <div class="fl">
                                <input type="text" name="email" class="field" placeholder="Email *" required>
                                <label>Email *</label>
                            </div>
                            <div class="fl">
                                <textarea name="msg" class="field" minlength="8" placeholder="Üzenet *"></textarea>
                                <label>Üzenet *</label>
                            </div>
                            <button type="submit" name="submit_mail" class="button"><i class="fa fa-envelope"></i> Küldés</button>
                        </form>		
                    </div>
                </div>
			</div>

			<div class="modal" id="modal_mail">
				<div class="modal_bg" onclick="toggleModal('modal_mail')"></div>
				<div class="modal_window">
					<div class="modal_header">
						<h3>Üzenet sikeresen elküldve!</h3>
						<i class="fa fa-times" aria-hidden="true" onclick="toggleModal('modal_mail')"></i>
					</div>
					<div class="modal_body">
						<p>Köszönjük üzenetét. Hamarosan jelentkezünk.</p>
					</div>
				</div>
			</div>
			
			<?php include "footer.php" ?>
		</div>
	</body>
</html>