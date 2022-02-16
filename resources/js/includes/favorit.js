var favorites_list = [];
const name_localstore = 'favorites_list'; // key cookies
const url_fv = "ajax/favorite";
var ajax_allowed = true;
// const openFavorit = () => {
// //открыть корзину
// 	$(".Heart").click(function () {
//
// 		$(".content-favorites-items").children(".item").remove();
//
// 		$.ajax({
// 			url: baseUrl + "favorit-list",
// 			type: 'POST',
// 			cache: false,
// 			data: {
// 				'favorit': JSON.stringify(favorites_list),
// 				"_token": $('meta[name="csrf-token"]').attr('content')
// 			},
// 			success: function success(response) {
// 				if (response['status'] === '1') {
// 					$(".content-favorites-items").prepend(response['content']);
// 					lazy_load_launch();
// 					console.log("OK:", response['content']);
// 				} else {
// 					console.error("ERROR ", response);
//
// 				}
// 			},
// 			error: function error(data) {
// 				console.log("error", data);
// 			}
// 		});
//
// 		$(".modal-overlay-favorites").addClass("active");
// 		$(".modal-favorites").addClass("active");
// 		$("body").css("overflow", "hidden");
//
// 		if (window_width > 767) {
// 			$("body").css('padding-right', scroll_width + 'px');
// 		}
//
// 		empty_favorites();
//
// 	});
// }

// const clodeFavorit = () => {
// 	//закрытие корзины
// 	$(".btn-back").click(function () {
// 		$(".modal-overlay-favorites").removeClass("active");
// 		$(".modal-favorites").removeClass("active");
// 		$("body").css("overflow", "auto")
//
// 	});
// }


	const changeFavorit = (btn=".Add_Favorites",class_container=".Product_Item_Fn",ajax=true) => {

		$("body").on("click", `${btn}`, function () {
			if(!ajax_allowed){
				return;
			}
            ajax_allowed = false;
			var id = $(this).data("productId");
			var _this = $(this);
			var price = $(`${class_container}[data-product-id='${id}'] .price-item-product_fn`).eq(0).text();

			if(ajax === true){
				ajax_favorite_upt(id,url_fv,price,_this);
			} else{

				if (!_this.hasClass("Active_Favorit")) {
					_this.addClass("Active_Favorit");
					add_favorites_in_list(favorites_list, id, parseInt(price));

				} else {
					_this.removeClass("Active_Favorit");
					delete_favorites_in_list(favorites_list, id);
				}

				reset_counters_favorites();

				set_cookie(name_localstore, JSON.stringify(favorites_list));
			}
		});
	}

	const favorit_check_active = (callback) => {

		for (var i = 0; i < favorites_list.length; i++) {
			$(`.Add_Favorites[data-product-id='${favorites_list[i].id}'] `).addClass("Active_Favorit");
		}

		if (callback !== undefined) {
			callback();
		}

	}

	const delFavorit = (btn=".Del_Favorites",class_container=".favorit_basket_item.Product_Item_Fn") => {

		$("body").on("click", btn, function () {
			if(!ajax_allowed){
				return;
			}
			ajax_allowed = false;

			var id = $(this).data("productId");
			var add_favorite = $(`.Add_Favorites[data-product-id='${id}'] `);
			delete_favorites_in_list(favorites_list, id);



			reset_counters_favorites();

			if(add_favorite.lenght > 0){
				add_favorite.removeClass("Active_Favorit");
			}
			let item = $(`${class_container}[data-product-id='${id}'] `);

			item.fadeOut("slow",function(){
				$(`${class_container}[data-product-id='${id}'] `).remove();
			})

			set_cookie(name_localstore, JSON.stringify(favorites_list));

            $.ajax({
                url: baseUrl +"/"+url_fv,
                type: 'POST',
                cache: false,
                data: {
                    'favorit': JSON.stringify(favorites_list),
                    "_token": $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {

                },
                success: function success(response) {

                    if (response['status'] === '1') {

                    } else {
                        console.error("ERROR ", response);
                    }
                },
                error: function error(data) {
                    console.log("error", data);
                }
            }).done(function () {
                ajax_allowed = true;
            });;
		});

	}

function ajax_favorite_upt(id,url,price,item){
    item.fadeOut("slow", function(){
        if (!item.hasClass("Active_Favorit")) {
            item.addClass("Active_Favorit");
            add_favorites_in_list(favorites_list, id, parseInt(price));

        } else {
            item.removeClass("Active_Favorit");
            delete_favorites_in_list(favorites_list, id);
        }

        reset_counters_favorites();

        set_cookie(name_localstore, JSON.stringify(favorites_list));

        item.fadeIn("slow")

		console.log('ajax_favorite_upt', favorites_list);
        $.ajax({
            url: baseUrl +"/"+url,
            type: 'POST',
            cache: false,
            data: {
                'favorit': JSON.stringify(favorites_list),
                "_token": $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {

            },
            success: function success(response) {

                if (response['status'] === '1') {

                } else {
                    console.error("ERROR ", response);
                }
            },
            error: function error(data) {
                console.log("error", data);
                item.fadeIn("slow")
            }
        }).done(function () {
            ajax_allowed = true;
        });
	});


}

function empty_favorites(){

	if(favorites_list.length <= 0){
		$(".content-favorites-empty").fadeIn("slow");
		$(".content-favorites-list").fadeOut("slow");
	} else {
		$(".content-favorites-empty").fadeOut("slow");
		$(".content-favorites-list").fadeIn("slow");
	}
}

function favorites_list_get(name){
	const getCookies = getCookie(name);
	console.log(getCookies)
	if(getCookies !== null && getCookies !== undefined && getCookies !== ''){
		favorites_list = JSON.parse(getCookies);
		reset_counters_favorites();
		favorit_check_active();
		set_cookie(name_localstore, JSON.stringify(favorites_list));
	} else {favorites_list = [];empty_favorites();}
}


function reset_counters_favorites(){
	let favorete_counter = $(".Heart_Counter_Fn");

	if( favorete_counter.length > 0){
		favorete_counter.text(favorites_list.length);
	}
	empty_favorites();
}

function add_favorites_in_list(list_favorit,favorit_id,price){
	list_favorit.push({id:favorit_id,count:1,price:price});
}

function delete_favorites_in_list(list_favorit,favorit_id){

	for(var i = 0; i < list_favorit.length; i++){
		if(list_favorit[i].id === favorit_id){
			list_favorit.splice(i, 1);
			break;
		}
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


favorites_list_get(name_localstore);


module.exports = {
	favorites_list: favorites_list,
	changeFavorit: changeFavorit,
	favoritCheckActive: favorit_check_active,
	delFavoritItem: delFavorit

}