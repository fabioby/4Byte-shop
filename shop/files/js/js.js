var current_page = location.href.split("/").slice(-1)+"";
var page_name = current_page.split(".")[0];
var isOpen = false;

var vue = new Vue({
	el: '#vue',
	components: {
		//'sv-lead-column': svLeadColumn,
		//'sv-customer-column': svCustomerColumn,
		draggable: window['vuedraggable']
	},
	data: {
		tab:0,
        couponCode:"",
        discount:0,
		pageCount: 1,
        monday:true,
        tuesday:true,
        thursday:true,
        wednesday:true,
        friday:true,
        saturday:false,
        sunday:false,
        res: "",
		selectedTime: "",
		cart: [],
		order: [],
		product: [],
		products: [],
		categories: [],
		service: [],
		services: [],
		reservation: [],
		premise: [],
		reservation_dates: [],
	},
	created(){
		var form_data = new FormData();
		form_data.append("querySQL", "SELECT * FROM categories");
		var data = ajax(form_data, "json");
		data.forEach(item => {
			this.categories.push({id:item['id'], name:item['name']})
		});

		if(current_page.includes("products.php")){
			//this.sortArray();
		}
		else if(current_page.includes("product.php")){
			//this.addToCart(24);
		}
		if (localStorage.getItem("cart") === null) {
			localStorage.setItem("cart",JSON.stringify([]))
		} else {
			this.cart = JSON.parse(localStorage.getItem("cart"));
		}
		
		this.fillServices();
	},
	methods: {
		sortArray(){
		},
		addToCart(id){
			var isInCart = false;
			
			this.cart.forEach(item => {
				if(item.id == id){
					isInCart = true;
					item.quantity++;
				}	
			});
			
			if(isInCart == false){
				form_data = new FormData()
				form_data.append("querySQL", "SELECT * FROM products WHERE id="+id);
				var data = ajax(form_data,'json');
				var quantity = $("#quantity").val();
				data.forEach(item => {
					if(item['id'] == id){
						var array = item['img'].split(",");
						var img = array[0].split(".");
						img_c = img[0]+"_c."+img[1];
						var price = item['price'] - item['price'] / 100 * item['discount']
						this.cart.push({id:item['id'], name:item['name'], type:item['id_category'], price:price, img:img_c, quantity:quantity})
					}
				});
				//toggleModal("modal_cart");
			}
			localStorage.setItem('cart', JSON.stringify(this.cart));
			//this.calcPrice();
			
		},
		clearCart(){
			toggleModal("modal_cart");
			this.cart = [];
			this.price = 0;	
			localStorage.setItem("cart",JSON.stringify([]))
		},
		removeFromCart(id){
			for (i=0; i < this.cart.length; i++) {
				if(this.cart[i].id == id){
					this.cart.splice(i, 1);
				}
			}
			if (this.cart.length === 0){
				toggleModal("modal_cart");
			}
			localStorage.setItem("cart",JSON.stringify(this.cart));
			this.priceSum();
		},
		changeQuantity(e){
			var id = e.target.getAttribute('data-id');
			var value = e.target.value;
			
			this.cart.forEach(item => {
				if(item.id == id){
					if(value > 0){
						item.quantity = value;
						localStorage.setItem('cart', JSON.stringify(this.cart));
					}
                    else {
						this.removeFromCart(item.id);
					}
				}
                this.priceSum();
			})
		},
		priceSum(){
			var price = 0;
			this.cart.forEach(item => {
				price += item.price * item.quantity;
			})
			this.price = price;
			return price;
		},
		format_number() {
			var e = 2000
			if (e > this.validation.max) {
			  return this.validation.max
			} else if (e < this.validation.min) {
			  return this.validation.min
			} else if (Math.round(e * this.validation.decimal) / this.validation.decimal != e) {
			  return this.last_value
			} else {
			  this.last_value = e
			  return e
			}
		},
	    toggleTabs(i) {
            this.tab=i;
			pushState("tab",i);
        },
        fillProduct(id){
            var form_data = new FormData();
            form_data.append("querySQL", "SELECT * FROM products WHERE id="+id);
            var data = ajax(form_data, "json");
            var item = data[0];
            var array_img = item['img'].split(",");
            var price = parseInt(item['price']);
            this.product = [];
            this.product.push({id:item['id'], category:parseInt(item['id_category']), name:item['name'], discount:item['discount'], stock:item['stock'], description:item['description'], price:price, url:item['url'], img:array_img})
        },
        fillProducts(){
        	var form_id = new FormData();
        	var form_data = new FormData();
        	var category = $("#select_type").val();
        	var order = $("#select_order").val();
        	var search = $("#search").val();
			var page = getState("page");page = page==undefined ? 1 : page;
			var maxItems = 10;
			
			var search_sql = search==undefined ? "" : search;
			var category_sql = category==undefined || category=="*" ? "" : " AND id_category="+category;
			var order_sql = order==undefined ? " ORDER BY id DESC" : " ORDER BY "+order;
			var sql_id = "SELECT id FROM products WHERE name LIKE '%"+search_sql+"%'"+category_sql+order_sql;
			var sql = "SELECT * FROM products WHERE name LIKE '%"+search_sql+"%'"+category_sql+order_sql+" LIMIT "+maxItems*(page-1)+","+maxItems;

			form_id.append("querySQL", sql_id);
			form_data.append("querySQL", sql);
			
        	var data = ajax(form_data, "json");
        	var data_id = ajax(form_id, "json");
			this.pageCount = Math.ceil(data_id.length/maxItems);

        	this.products = [];
			
        	data.forEach(item => {
				var array = item['img'].split(",");
        		var img = array[0].split(".");
        		img_c = img[0]+"_c."+img[1];
        		var num = item['price'].replace(/[^0-9]/g, ''); 
        		var price = parseInt(num);
        
        		var form_data = new FormData();
        		var sql = "SELECT name FROM categories WHERE id="+item['id_category'];
        		form_data.append("querySQL", sql);
        		var category = ajax(form_data, "json")[0];
        
        		this.products.push({id:item['id'], name:item['name'], description:item['description'], stock:item['stock'], category:category.name, price:price, discount:item['discount'], img:img_c})
        	});
        },
        fillOrder(id){
            var form_data = new FormData();
            form_data.append("querySQL", "SELECT * FROM orders WHERE id="+id);
            var data = ajax(form_data, "json");
            var item = data[0];
            this.order = [];
            this.order.push({id:item['id'], name:item['name'], email:item['email'], phone:item['phone'], address:item['address'], note:item['note'], payment:item['payment'], isPayed:item['isPayed'], dateTime:item['dateTime'], products:item['products']})
        },
        fillService(id){
            var form_data = new FormData();
            form_data.append("querySQL", "SELECT * FROM services WHERE id="+id);
            var data = ajax(form_data, "json");
            var item = data[0];
            var price = parseInt(item['price']);
            var array_img = item['img'].split(",");
            this.service = [];
            this.service.push({id:item['id'], name:item['name'], description:item['description'], duration:item['duration'], price:price, img:array_img});
        },
        fillServices(){
            var form_data = new FormData();
            form_data.append("querySQL", "SELECT * FROM services");
            var data = ajax(form_data, "json");
            this.services = [];
        	data.forEach(item => {
                this.services.push({id:item['id'], name:item['name']});
        	});
        },
        fillReservation(id){
            var form_data = new FormData();
            form_data.append("querySQL", "SELECT * FROM reservatoins WHERE id="+id);
            var data = ajax(form_data, "json");
            var item = data[0];
            this.reservation = [];
            this.reservation.push({s_id:item['s_id'], name:item['name'], email:item['email'], phone:item['phone'], dateTime:item['dateTime'], duration:item['duration']});
        },
        fillPremise(id){
            var form_data = new FormData();
            form_data.append("querySQL", "SELECT * FROM premises WHERE id="+id);
            const data = ajax(form_data, "json")[0];
			const arrayTimes = data['openTimes'].split(";");
			const openTimes = [];
			arrayTimes.forEach((item, index) => {
				const arrayTime = item.split("-");
        		openTimes.push(arrayTime);

				if(arrayTime[0].length==0){
					this.monday = index==0 ? false : this.monday;
					this.tuesday = index==1 ? false : this.tuesday;
					this.wednesday = index==2 ? false : this.wednesday;
					this.thursday = index==3 ? false : this.thursday;
					this.friday = index==4 ? false : this.friday;
					this.saturday = index==5 ? false : this.saturday;
					this.sunday = index==6 ? false : this.sunday;
				}
        	});
            this.premise = [];
            this.premise.push({id:data['id'], address:data['address'], openTimes:openTimes})
        },
        updateProduct(){
            var form = document.getElementById("form_edit_product");
            var form_data = new FormData(form);
            form_data.append("submit_update_product", 1);
            form_data.append("img", window.vue.product[0].img+"");
            $data = ajax(form_data,"text");
            alert($data)
            return false;
        },
        removeProduct(id){
        	form_data = new FormData()
        	form_data.append("submit_delete", id);
        	$data = ajax(form_data,"text");
        	alert($data);
        },
        updateService(){
            var form = document.getElementById("form_edit_service");
            var form_data = new FormData(form);
            form_data.append("submit_update_service", 1);
            form_data.append("img", window.vue.service[0].img+"");
            $data = ajax(form_data,"text");
            alert($data)
            return false;
        },
        updatePersonal(){
            var form = document.getElementById("form_personal");
            var form_data = new FormData(form);
            form_data.append("submit_edit_personal", 1);
            //form_data.append("img", window.vue.product[0].img+"");
            $data = ajax(form_data,"text");
            alert($data)
            return false;
        },
        removeService(id){
        	form_data = new FormData()
        	form_data.append("submit_delete", id);
        	$data = ajax(form_data,"text");
        	alert($data);
        },
        checkout(){
            var form = document.getElementById("form_checkout");
            var form_data = new FormData(form);
            /*var products="";
    		this.cart.forEach(item => {
			    products+=item['name']+","+item['price']+","+item['quantity']+";";
	        });
	        products = products.slice(0, -1);*/
            const cart_string = JSON.stringify(this.cart);

            form_data.append("submit_checkout", 1);
            form_data.append("cart", cart_string);
            $data = ajax(form_data,"text");
            this.cart=[];
            localStorage.setItem('cart', JSON.stringify(this.cart));
            alert($data);
            return false;
        },	
		makeURL(index){
			var category = getState("category"); category = category==undefined || category=="*" ? "" : "category="+category+"&";
			var order = getState("order"); order = order==undefined ? "" : "order="+order+"&";
			var page = "page="+index+"&";
			var search = getState("search"); search = search!=undefined && search.length>0 ? "&search="+search+"&" : "";
			pushState("page",index);
			this.fillProducts();
			//return "products.php?"+category+order+page+search;

		},
		getPage(){
			var page = getState("page");
			return page==undefined ? 1 : page;
		},
		setRes(){
            this.selectedTime="";
			const openTime = 10;
			const closeTime = 18;
			const currentDateTime = new Date();
			//const newTime = currentDateTime.getHours() >
			const date_a = $("#date").val().split("-");
			const dateTime  = new Date(date_a[0]+"-"+date_a[1]+"-"+date_a[2]+" "+openTime+":"+"00");
			this.reservation_dates=[];
			let formattedDateTime = formatDate(dateTime);
			const formattedDate = formattedDateTime.split(" ")[0];
			const sql = ("SELECT dateTime,services.duration FROM reservations INNER JOIN services ON services.id=reservations.s_id WHERE DATE(dateTime) = '"+formattedDate+"' ORDER BY dateTime ASC");
			const form_reservations = new FormData(); form_reservations.append("querySQL", sql);
			const data_reservations = ajax(form_reservations, "json");
			
			let day = dateTime.getDay(); day = day==0 ? "Vasárnap" : day==1 ? "Hétfő" : day==2 ? "Kedd" : day==3 ? "Szerda" : day==4 ? "Csütörtök" : day==5 ? "Péntek" : "Szombat";
			
			while(dateTime.getHours()<closeTime) {
				formattedDateTime = formatDate(dateTime);
				let formattedTime = formattedDateTime.split(" ")[1];
				let timeParts = formattedTime.split(':');
				let inArray = false;
				formattedTime = `${timeParts[0]}:${timeParts[1]}`;
				
				if(data_reservations.length>0){
					data_reservations.forEach(item => {
						if(formattedDateTime==item['dateTime']){
							const duration = item['duration'];
							const period = Math.ceil(duration/30);
							for (i=0; i<=period; i++) {
								formattedDateTime = formatDate(dateTime);
								formattedTime = formattedDateTime.split(" ")[1];
								timeParts = formattedTime.split(':');
								formattedTime = `${timeParts[0]}:${timeParts[1]}`;
								this.reservation_dates.push({time:formattedTime, duration:duration});
								dateTime.setMinutes(dateTime.getMinutes() + 30);
							}
							inArray = true;
						}
					});
				}
				if(!inArray){
					this.reservation_dates.push({time:formattedTime, duration:0});
					dateTime.setMinutes(dateTime.getMinutes() + 30);
				}
			}
		},
		setRes2(dateTime){
			const openTime = 10;
			const closeTime = 18;
			const currentDateTime = new Date();
			currentDateTime.setHours(12,30,0,0);
			this.reservation_dates=[];
			let dayOfWeek = dateTime.getDay();
			let time = dateTime.getHours();
			const daysLeftOfWeek = 7-dayOfWeek;
			const endDate = new Date(dateTime);;
			endDate.setDate(endDate.getDate()+daysLeftOfWeek);
			endDate.setHours(closeTime,0,0,0);
			let formattedDateTime = formatDate(dateTime)
			const formattedDateTimeEnd = formatDate(endDate)
			const form_reservations = new FormData();
			const sql = ("SELECT dateTime,duration FROM reservations WHERE dateTime >= '"+formattedDateTime+"' AND dateTime <= '"+formattedDateTimeEnd+"'");
			form_reservations.append("querySQL", sql);
			const data_reservations = ajax(form_reservations, "json");
			
			for(i=0; i<=daysLeftOfWeek; i++){
				formattedDateTime = formatDate(dateTime);
				const dateTime_a = formattedDateTime.split(" ");
				const formattedDate = dateTime_a[0];
				dateTime.setHours(10,0,0,0);
				const startTime = i==0 && currentDateTime>dateTime ? time : openTime
				dateTime.setHours(startTime,0,0,0);
				let day = dateTime.getDay();
				day = day==0 ? "Vasárnap" : day==1 ? "Hétfő" : day==2 ? "Kedd" : day==3 ? "Szerda" : day==4 ? "Csütörtök" : day==5 ? "Péntek" : "Szombat";
				let times = [];
				
				while(dateTime.getHours()<closeTime) {
					formattedDateTime = formatDate(dateTime);
					let formattedTime = formattedDateTime.split(" ")[1];
					let inArray = false;

					data_reservations.forEach(item => {
						const resDateTime = item['dateTime'];
						const duration = item['duration'];
						const period = Math.ceil(duration/30);
						if(formattedDateTime==resDateTime){
							for (i2=0; i2<=period; i2++) {
								formattedDateTime = formatDate(dateTime);
								formattedTime = formattedDateTime.split(" ")[1];
								times.push({time:formattedTime, duration:duration});
								dateTime.setMinutes(dateTime.getMinutes() + 30);
							}
							inArray = true;
						}
					});
					if(!inArray){
						times.push({time:formattedTime, duration:0});
						dateTime.setMinutes(dateTime.getMinutes() + 30);
					}
					//alert(JSON.stringify(times));
				}

				this.reservation_dates.push({dateTime:formattedDate, day:day, times:times});
				dateTime.setDate(dateTime.getDate()+1);
			}
			//alert(JSON.stringify(this.reservation_dates));
		},
		getPrevMonday() {
			let currentDateTime = new Date();
			let dayOfWeek = currentDateTime.getDay();
			let daysSincePreviousMonday = (dayOfWeek + 6) % 7; // Calculate days since previous Monday
			let previousMonday = new Date(currentDateTime.getTime() - daysSincePreviousMonday * 24 * 60 * 60 * 1000);
			return previousMonday;
		},
		getNextMonday() {
			let currentDateTime = new Date();
			let dayOfWeek = currentDateTime.getDay();
			let daysUntilNextMonday = 1 + (7 - dayOfWeek) % 7; // Calculate days until next Monday
			let nextMonday = new Date(currentDateTime.getTime() + daysUntilNextMonday * 24 * 60 * 60 * 1000);
			return nextMonday;
		},
		ceilToHalfHour(dateTime) {
			// Create a copy of the input date
			const roundedTime = new Date(dateTime);
		
			// Get the current minutes
			const minutes = roundedTime.getMinutes();
		
			// Calculate the difference to the next half-hour
			const diffToNextHalfHour = 30 - (minutes % 30);
		
			// Ceil the minutes to the next half-hour
			roundedTime.setMinutes(minutes + diffToNextHalfHour);
		
			// Reset seconds and milliseconds to zero
			roundedTime.setSeconds(0, 0);
		
			return roundedTime;
		},
		updateDate(index){
			const currentDate = new Date();
			const date_a = $("#date").val().split("-");
			const dateTime  = new Date(date_a[0]+"-"+date_a[1]+"-"+date_a[2]);
			let newDate = new Date(dateTime);
        	newDate.setDate(newDate.getDate() + index);
			newDate = newDate<currentDate ? currentDate : newDate;
			$("#date").val(formatDate(newDate).split(" ")[0]);
			swiper4.update();
			this.selectedTime = "";
			this.setRes();
		},
        getDate(){
            return date.value;
        },
		formatPrice(number) {
			// Convert the number to a string and get its length
			var number=number.toFixed(0);
			const numberString = number.toString();
			const length = numberString.length;
	  
			// Check the length and format accordingly
			if (length === 3) {
				return numberString; // No change for 3-digit numbers
			}
			else {
			  // Use toLocaleString to add commas and format the number
			  return Number(number).toLocaleString('en-US', {
				minimumFractionDigits: 0,
				maximumFractionDigits: 3,
			  }).replace(/,/g, '.');
			}
		},
        handleSelectChange(event){
            this.tab=event.target.value;
        },
		getOpenTime(index){
		},
		checkCoupon(){
            this.discount=0;
            var form_data = new FormData();
            form_data.append("querySQL", "SELECT * FROM coupons WHERE code='"+this.couponCode+"'");
            var data = ajax(form_data, "json");
            this.discount = data[0]["discount"];
		},
	},
})
var swiper = new Swiper(".swiper_index", {
    slidesPerView: 1,
    spaceBetween: 0,
    centeredSlides: true,
	loop:true,
    autoplay: {
        delay: 2600,
        disableOnInteraction: false,
    },
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
});
var swiper2 = new Swiper(".swiper_products", {
    slidesPerView: "auto",
    spaceBetween: 20,
    centeredSlides: true,
    loop: true,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    autoplay: {
        delay: 2000,
        disableOnInteraction: true,
    },
});
var swiper3 = new Swiper(".swiper_gallery_bottom", {
	loop: true,
	spaceBetween: 8,
	slidesPerView: 3,
	navigation: {
		nextEl: ".swiper-button-next",
		prevEl: ".swiper-button-prev",
	},
});
var swiper4 = new Swiper(".swiper_services", {
	slidesPerView: 1,
	spaceBetween: 0,
	centeredSlides: true,
	autoHeight: true,
	loop: true,
    allowTouchMove: false,
    allowMouseEvents: false,
	pagination: {
		el: ".swiper-pagination",
		clickable: false,
	},
});


//$("#sortable").sortable();
//$("#sortable").disableSelection();


$("#select_type").change(function() {
	$("#search").val("");
	pushState("page","1");
	pushState("category",$("#select_type").val());
	window.vue.fillProducts();
});
$("#select_order").change(function() {
	pushState("order",$("#select_order").val());
	window.vue.fillProducts()
});
$("#search").change(function() {
	$("#select_type").prop("selectedIndex", 0);
	pushState("search",$("#search").val());
	window.vue.fillProducts()
});
$(document).on('click', '#addToCart', function(){
	//var id = $(this).data('id');
	//window.vue.addToCart(id);
	//var id = e.target.getAttribute('data-id');
});
$(document).on('click', '#cart_clear', function(){
	window.vue.clearCart();
});
$(document).off().on("click", "#bars", function () {
	$(".hn").slideToggle(160).css("display", "flex");
	$(".bars").toggleClass("open");	
	
	if($(window).scrollTop() < 50){
        isOpen ? $("header").removeClass("header") : $("header").addClass("header")
	}
	isOpen = !isOpen;
});
$(document).on('click', function(event) {
    if (isOpen && !$(event.target).closest('header').length) {
        $(".hn").slideUp(160);
        $(".bars").toggleClass("open");	
        isOpen = false;
    }
});
$("#admin_tab").click(function(){
    alert("The paragraph was clicked.");
});
$(window).scroll(function () {
	if(!isOpen){
		if(page_name != "product"){
			if ($(window).scrollTop() > 50) {
				$("header").addClass("header");
			}
			else {
				$("header").removeClass("header");
			}
		}
	}
});
$(document).on("change", "#date", function () {
    var date = document.getElementById('date');
    var currentDate = new Date();
    var selectedDate = new Date(date.value);
    var formattedDate = currentDate.toISOString().split('T')[0];
    if(selectedDate<currentDate){
        date.value=formattedDate;
    }
	window.vue.setRes();
});

function ajax(data,responseType){
	var result;
	var form_data = data;
	//data = JSON.parse("{"+JSON.stringify(key)+":"+JSON.stringify(value)+"}");

	$.ajax({
		url: "server.php",
		type: "POST",
		async: false,
		cache: false,
		processData: false,
		contentType: false,
		dataType: responseType,
		data: form_data,
        success: function(response){
			result = response;
        },
		error: function (xhr, ajaxOptions, thrownError) {
			alert(xhr.status + " & " + thrownError + " & shr status: " + xhr.statusText);
		},
    });
	
	return result;
}
function toggleModal(modal_id) {
	$("body").toggleClass("body-scroll-disabled");	
	$("#"+modal_id).fadeToggle(200).css("display", "flex");
}
function getState(parameterName) {
	const urlParams = new URLSearchParams(window.location.search);
	const state = urlParams.get(parameterName);
	return state;
  }
function pushState(key,value) {
    const url = new URL(window.location.href);
    url.searchParams.set(key, value);
    window.history.replaceState(null, null, url);
}
function removeState(parameterName) {
	const urlSearchParams = new URLSearchParams(window.location.search);
	urlSearchParams.delete(parameterName);
	const updatedSearchString = urlSearchParams.toString();
	window.history.replaceState(null, null, updatedSearchString ? `?${updatedSearchString}` : window.location.pathname);
}
function register() {
	var form = document.getElementById("form_register");
	var form_data = new FormData(form);
	form_data.append("submit_register", 1);
	$data = ajax(form_data,"text");

	if($data == "1"){
		$("#form_register").css("display","none");
		$("#register_finish").css("display","flex");
	}
	else {
		alert($data);
	}
	return false;
}
function sendMail() {
	var form = document.getElementById("form_mail");
	var form_data = new FormData(form);
	form_data.append("submit_mail", 1);
	$data = ajax(form_data,"text");
	window.vue.modal = "modal_mail";
	alert($data);
	return false;
}
function uploadFile(){
	/*
	var files = document.getElementById('files');
	var count = files.files.length;
	if(count > 0){
		form_data = new FormData()
		form_data.append("upload_file", 1);
		for (var i = 0; i < count; i++) {
			form_data.append("files[]", files.files[i]);
		}
		$data = ajax(form_data,"text");
		var array_img = $data.split(",");
		array_img.forEach(item => {
			window.vue.product[0].img.push(item);
		});
		//alert(window.vue.product[0].img);
	}
	*/
}
function setImg(e){
	$("#product_img").attr("src",$(e).attr("src"))
}
function formatDate(date){
	const year = date.getFullYear();
	const month = String(date.getMonth() + 1).padStart(2, '0');
	const day = String(date.getDate()).padStart(2, '0');
	const hours = String(date.getHours()).padStart(2, '0');
	const minutes = String(date.getMinutes()).padStart(2, '0');
	const seconds = String(date.getSeconds()).padStart(2, '0');
	return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}