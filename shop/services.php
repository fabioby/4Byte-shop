<!DOCTYPE html>
<html lang="hu">
	<?php include "server.php"; include "head.php" ?>
	<body onload="window.vue.setRes(new Date())">
		<div id="vue">
			<?php include "header.php" ?>
			
			<div class="bg">
				<div class="layer"></div>
				<div>
					<h2>Szolgáltatások</h2>
				</div>
			</div>
			
			<div class="services">
                <div class="swiper-pagination"></div>
                <div class="swiper swiper_services">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide slide1">
                            <div>
                                <?php
                                    $data_services = querySQL("SELECT * FROM services");
                                    
                                    foreach ($data_services as $value) {
                                        $img_array = explode(",",$value['img']);
                                        $img = explode(".",$img_array[0]);
                                        $img_c = $img[0]."_c.".$img[1];
                                ?>
                                <div class="service">
                                    <div><img src="files/images/services/<?php echo $img_array[0] ?>"></img></div>
                                    <div>
                                        <h2><?php echo $value['name'] ?></h2>
                                        <span><?php echo $value['duration'] ?> perc</span>
                                        <p><?php echo $value['description'] ?></p>
                                        <button class="button" onclick="swiper4.slideNext();$('html, body').animate({ scrollTop: 120 }, 600);" @click="reservation[0]='<?php echo $value['id'] ?>';reservation[1]='<?php echo $value['name'] ?>';reservation[2]='<?php echo $value['img'] ?>';reservation[3]='<?php echo formatPrice($value['price']); ?>';reservation[4]='<?php echo $value['duration'] ?>'">Időpont foglalás</button>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="swiper-slide slide2">
                            <h3>Válassz időpontot</h3>
                            <div class="reservation">
                                <div>
                                    <span @click="updateDate(-1)"><i class='fas fa-angle-left'></i></span>
                                    <input type="date" id="date" min="<?php echo date('Y-m-d') ?>" value="<?php echo date('Y-m-d') ?>">
                                    <span @click="updateDate(1)"><i class='fas fa-angle-right'></i></span>
                                </div>
                                <div class="times">
                                    <div v-for="item in reservation_dates" @click="selectedTime=item.time" v-if="item.duration==0" :value="item.duration" :class="{'selected':item.time==selectedTime}">{{item.time}}</div>
                                </div>
                                <div :class="{'deactivated':selectedTime.length==0}" class="button" onclick="swiper4.slideNext();$('html, body').animate({ scrollTop: 120 }, 600);" @click="reservation[5]=selectedTime">Tovább <i class='fas fa-angle-right'></i></div>
                            </div>
                        </div>
                        <div class="swiper-slide slide3">
                            
                            <h3>Fogalalás véglegesítése</h3>

                            <div class="data">
                                <div>
                                    <div>
                                        <img v-if="selectedTime!=''" :src="'files/images/services/'+reservation[2]">
                                    </div>
                                    <div>
                                        <div>
                                            <div><b>{{reservation[1]}}</b></div>
                                            <div onclick="swiper4.slideNext()" @click='selectedTime="";reservation=[]'>Módosítás</div>
                                        </div>
                                        <div>
                                            <div>
                                                <span>Dátum:</span>
                                                <span><b>{{getDate()}} {{selectedTime}}</b></span>
                                            </div>
                                            <div>
                                                <span>Időtartam:</span>
                                                <span><b>{{reservation[4]}} perc</b></span>
                                            </div>
                                            <div>
                                                <span>Ár:</span>
                                                <span><b>{{reservation[3]}} Ft</b></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="fl">
                                        <input type="text" class="field" name="name" placeholder="Név *" required>
                                        <label>Név</label>
                                    </div>
                                    <div class="fl">
                                        <input type="email" class="field" name="email" placeholder="Email *" required>
                                        <label>Email</label>
                                    </div>
                                    <div class="fl">
                                        <input type="text" class="field" name="phone" placeholder="Mobil *" required>
                                        <label>Mobil</label>
                                    </div>
                                    <div class="fl">
                                        <textarea class="field" name="note" placeholder="Megjegyzés"></textarea>
                                        <label>Megjegyzés</label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" name="terms" id="terms2" required>
                                        <label for="terms2">Elfogadom a felhasználói feltételeket *</label>
                                    </div>	
                                    <input type="hidden" name="s_id" :value="reservation[0]">		
                                    <input type="hidden" name="service" :value="reservation[1]">		
                                    <input type="hidden" name="dateTime" :value="getDate()+' '+selectedTime">		
                                    <input type="hidden" name="duration" :value="reservation[4]">		
                                    <input type="submit" name="submit_add_reservation" value="Foglalás" class="button">			
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
			<?php include "footer.php" ?>
		</div>
	</body>
</html>