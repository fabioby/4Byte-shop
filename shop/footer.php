<footer>
    <div>
        <div>
            <div class="infos">
                <p>Információk</p>
                <div>
                    <a href="contact.php">Elérhetőség</a>
                    <a href="info.php">Rólunk</a>
                    <a href="terms.php">Használati feltételek</a>
                    <a href="privacy.php">Adatvédelmi irányelvek</a>
                    <a href="transport.php">szállítási szabályzat</a>
                    <a href="refund.php">Pénzvisszatérítési eljárás</a>
                    <a href="track.php">Csomagkövetés</a>
                </div>
            </div>
            <?php
                $data_premises = querySQL("SELECT * FROM premises");
                if (!empty($data_premises)){
            ?>
            <div class="shops">
                <p>Üzletek</p>
                <div>
                    <?php
                        foreach ($data_premises as $val) {
                            echo '<a href="#">'.$app_name.' - '.$val['address'].'</a>';
                        }
                    ?>
                </div>
            </div>
            <?php } ?>
            <div class="pay">
                <p>Fizetési módok</p>
                <img src="files/images/payment.png">
            </div>
        </div>
    </div>
    <div>
        <div>
            <div>© 2022 <?php echo $app_name ?>. Minden jog fenntartva.</div>
            <div class="social">
                <a href="https://www.facebook.com/?locale=hu_HU"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="https://www.instagram.com"><i class="fa-brands fa-instagram"></i></a>
            </div>
        </div>
    </div>
</footer>