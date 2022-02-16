const {background_is_picture_launch, lazy_load_launch} = require("./picture_functions")
/*
* const class:
* 0) Product_Item_Fn - класс для входа в карточку
* 1) price-item-product_fn - класс для цены
* 2) Item_Product_Counter - счетчик одиночного товара
* 3) modal_basket - класс модального окна корзины
* 4) modal_basket_items_list - контейнер для вывода списка продуктов
* 5) modal_basket_items_empty - контейнер "Товаров нет"
* 6) Total_Card_Counter - общ. кол. товаров
* 7) Total_Price - общ. цена
* 8) open_close - закрытие/открытие одной кнопкой
* def data atr
* 1)data-product-id - id товара. Ставить на: контейнер товара, кнопки удаления/ добавления, счетчик товара
*/

var product_list = [], // массив объектов [{id:int,counter:int,price:int}]
	total_price = 0; // Общ. стоимость товаров
const name_cookies = "product_list"; // key cookies

let window_width = $(window).width();


	product_list_get(name_cookies);
	$("body").append("<div style='display: none' class='Hider_Modal_Def'></div>");

	// добавление товара. op1 - классы кнопок для добавления, op2 - класс карточки товара
	let addCart = (class_btn_add=".btn_add_product",card_item_product=".Product_Item_Fn",callback_fn) =>{
		$("body").on("click",`${class_btn_add}`,function(){

			var id = $(this).data("productId"); // получаем id товара для поска в дом
			var price = $(`${card_item_product}[data-product-id='${id}'] .price-item-product_fn`).eq(0).text() // получаем цену

			add_product_in_list(product_list,id,parseInt(price),card_item_product); // добавление\изменение товара
			total_price_fn(); // считаем новую цену
			reset_counters(); // обновляем цену и кол. товаров
			set_cookie(name_cookies, JSON.stringify(product_list ),34); // сохр. в куки список товаров

			$(`${class_btn_add}[data-product-id='${id}']`).fadeOut("slow",function(){
					$(`${class_btn_add}[data-product-id='${id}']`).fadeIn("slow");
			})

			if(callback_fn !== undefined){ // callback function
				callback_fn();
			}
		});
	}

	// op1 - клыссы для открытия, op2 - url для ajax запроса, op3 -
	const openCart = (btn_open,ajax_url,hider=true,fixed=false,off_ajax = false) => {
		$("body").on("click",`${btn_open}`,function (e) {

			if(fixed !== false){

				if(window_width < 996){
					if(fixed.position === true){
						$("body").css("position","fixed");
					}
					$("body").css("overflow","hidden");
				}
			}

			e.preventDefault()
			let modal_basket = $(".modal_basket"),
				modal_basket_items_list = $(".modal_basket_items_list")

			if( $(btn_open).hasClass("open_close") === true && modal_basket.hasClass("active") === true ){
				closeCart(null,true)
			} else{
				modal_basket_items_list.children(".modal_basket_item").remove();
				if(off_ajax === false){
					$.ajax({
						url: window.baseUrl +"/"+ajax_url,
						type: 'POST',
						cache: false,
						data: {
							'cart': JSON.stringify(product_list),
							"_token": $('meta[name="csrf-token"]').attr('content')
						},
						success: function success(response) {
							response = JSON.parse(response);

							if (response['status'] === '1') {
								modal_basket_items_list.prepend(response['content']);
								//console.log(response['content']);
								lazy_load_launch();
                                background_is_picture_launch()
							} else {
								console.log("ERROR ", response);

							}
						},
						error: function error(data) {
							console.log("error", data);
						}
					});
				}


				modal_basket.addClass("active");
				if(hider === true){
					$(".Hider_Modal_Def").fadeIn("slow");
				}
				total_price_fn();
				reset_counters();
			}


		});
	}


	const closeCart = (el_close=null,overlay_close=false,fixed=false) =>{
		//закрытие корзины


		if(el_close === null){
			$(".modal_basket").removeClass("active");
			if (overlay_close === true) {
				$(".Hider_Modal_Def").fadeOut("slow");
			}
		} else {
			$("body").on("click", `${el_close}`, function (e) {

				if (overlay_close === true) {
					$(".Hider_Modal_Def").fadeOut("slow");
				}
				$(".modal_basket").removeClass("active");

				if(fixed !== false){
					if(window_width < 996){
						console.log("close 1");
						if(fixed.position === true){
							console.log("close 2")
							$("body").css("position","");
						}
						$("body").css("overflow","");
					}
				}

			});
		}
	}
	const  plusCart=(btn=".Cart_Plus_Product",card_item_product=".Product_Item_Fn")=>{
		$("body").on("click",`${btn}`,function(){

			var id = $(this).data("productId");
			//var price = $(`.item[data-product-id='${id}'] .total-price`).text();
			var price = $(`${card_item_product}[data-product-id='${id}'] .price-item-product_fn`).eq(0).text()
			add_product_in_list(product_list,id,parseInt(price),card_item_product);
			total_price_fn();
			reset_counters(true,id);
			set_cookie(name_cookies, JSON.stringify(product_list ));
		});
	}


	const  minusCard=(btn=".Cart_Minus_Product",card_item_product=".modal_basket_item.Product_Item_Fn")=>{
		$("body").on("click",btn,function(){

			var id = $(this).data("productId");
			//var price = $(`.item[data-product-id='${id}'] .total-price`).text();
			var flag_del = delete_product_in_list(product_list,id,1);

			total_price_fn();

			if(flag_del===true){
				$(`${card_item_product}[data-product-id='${id}']`).fadeOut("slow",function(){
					$(`${card_item_product}[data-product-id='${id}']`).remove();
				});
				reset_counters();
			} else{
				reset_counters(true,id);
			}

			set_cookie(name_cookies, JSON.stringify(product_list ));
		});
	}

	const delItemCart = (btn=".Cart_Del_Product",card_item_product=".modal_basket_item.Product_Item_Fn")=>{
		$("body").on("click",`${btn}`,function(){
			var id = $(this).data("productId");

			delete_product_in_list(product_list,id,0,card_item_product);

			total_price_fn();
			reset_counters();

			$(`${card_item_product}[data-product-id='${id}']`).fadeOut("slow",function(){
				$(`${card_item_product}[data-product-id='${id}']`).remove();
			});

			set_cookie(name_cookies, JSON.stringify(product_list));
		});
	}

	const cartCustom = (callback) =>{
		callback();
	}

	// добавление в корзину. op1 - продукты [{id:int,counter:int,price:int}], op2 - цена
	function add_product_in_list(list_product,product_id,price,card_item_product){
		var flag_search = false;
		var buf = -1;

		for(var i = 0; i < list_product.length; i++){  //  поиск товара в корзине
			if(list_product[i].id === product_id){ // товар найден, обновляем счетчик в массиве
				//list_product[i].price += list_product[i].price;
				list_product[i].count += 1; // обновления счетчика
				flag_search = true; // уст. чтобы не добавило, как новый товар
				buf = i; // индекс для обновления сounter в клиенте
				break;
			}
		}

		if(buf !== -1){ // если товар есть, то обновить сounter нужного товара
			$(`body ${card_item_product}[data-product-id='${product_id}'] .Item_Product_Counter`).text(list_product[buf].count) // исправить на общий
		}

		if(flag_search === false){ // добавляем новый товар
			product_list.push({id:product_id,count:1,price:price});
		}
	}

	function delete_product_in_list(list_product,product_id,flag_delete_item,card_item_product){
		var flag_del = false;
		var buf = -1;
		for(var i = 0; i < list_product.length; i++){

			if(list_product[i].id === product_id){

				if(flag_delete_item === 0){
					list_product[i].count = 0;
				} else{
					list_product[i].count -= 1;
				}
				if(list_product[i].count === 0){
					list_product.splice(i, 1);
					flag_del = true;
				} else{
					buf = i;
				}
				break;
			}
		}

		if(buf !== -1){
			console.log(product_id);
			//$(`body .item[data-product-id=${product_id}] .Cart_Counter`).text(list_product[buf].count)
			$(`body ${card_item_product}[data-product-id='${product_id}'] .Item_Product_Counter`).text(list_product[buf].count)
		}

		return flag_del;

	}

function product_list_get(name){
		const getCookies = getCookie(name);
		if(getCookies !== null && getCookies !== undefined && getCookies !== ''){
			product_list = JSON.parse(getCookies);
			total_price_fn();
			reset_counters();

		} else {product_list = []}

	}

function total_price_fn(){

	total_price = parseInt(0);
	for(var i = 0; i < product_list.length; i++ ){
		total_price += parseInt(product_list[i].count)*parseInt(product_list[i].price);
	}
    if (isNaN(total_price)){
        total_price = parseInt(0);
    }
	console.log(total_price)
}

function reset_counters(upt_prod_counter = false,id=null){
	var counter_cart = 0;
	for(var i = 0; i<product_list.length; i++){
		counter_cart += product_list[i].count;
	}

	$(".Total_Card_Counter").text(counter_cart);
	$(".Total_Price").text(total_price);

	if(upt_prod_counter === true){
		const count_item = product_list.find(x => x.id === id).count;
		$(`.Cart_Counter_Product[data-product-id='${id}']`).text(count_item);
	}

	empty_cart();
}

function empty_cart(){
	if(product_list.length <= 0){
		$(".modal_basket_empty").fadeIn("slow");
		$(".modal_basket_info").fadeOut("slow");
	} else {
		$(".modal_basket_empty").fadeOut("slow");
		$(".modal_basket_info").fadeIn("slow");
	}
}

function set_cookie(cookie_name, cookie_val, expires_hours){
	if(typeof expires_hours === 'undefined'){
		expires_hours = -1;
	}
	var expires;
	var path;
	path = '; path=/';
	if(expires_hours !== -1){
		var d = new Date();
		d.setTime(d.getTime() + (expires_hours * 60 * 60 * 1000));
		expires = "; expires="+d.toUTCString();
	}else{
		expires = ";";
	}
	document.cookie = cookie_name+"="+cookie_val+path+expires;
}

function getCookie(name) {

	var matches = document.cookie.match(new RegExp(
		"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	))
	return matches ? decodeURIComponent(matches[1]) : undefined
}

module.exports = {
	product_list: product_list,
	total_price: total_price,
	addCart: addCart,
	openCart: openCart,
	delItemCart: delItemCart,
	closeCart: closeCart,
	plusCart: plusCart,
	minusCard: minusCard,
	cartCustom: cartCustom
}