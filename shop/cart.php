<div class="modal" id="modal_cart">
	<div class="modal_bg" onclick="toggleModal('modal_cart')"></div>
	<div class="modal_window">
		<div class="modal_header">
			<h3>Kosár</h3>
			<i class="fa fa-times" onclick="toggleModal('modal_cart')"></i>
		</div>
		<div class="modal_body cart">
			<div v-if="cart.length > 0" v-for="item in cart">
				<div class="name">
					<img :src="'files/images/products/compressed/'+item.img">
					<a :href="'product.php?id='+item.id">{{item.name}}</a>
				</div>
				<div class="quantity"><input type="number" class="field" :value="item.quantity" :data-id="item.id" @change="changeQuantity" min="1"></div>
				<div class="price">{{formatPrice(item.price)}} Ft</div>
				<div class="remove" v-on:click="removeFromCart(item.id)">&times</div>
			</div>
			<div v-if="cart.length == 0">Kosarad üres</div>
		</div>
		<div class="modal_footer cart_footer" v-if="cart.length > 0">
			<div class="sum">Összeg: {{formatPrice(priceSum())}} Ft</div>
			<div>
				<button v-on:click="clearCart()" id="cart_clear" class="button button3">Kosár üritése</button>
				<a href="checkout.php" class="button">Tovább</a>
			</div>
		</div>
	</div>
</div>