let requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
let menu_cat_big = $(".menu-cat-big");

$(document).mouseup(function (e){
	console.log("hide",menu_cat_big.hasClass("menu-cat-big_hide"));
	if (!menu_cat_big.is(e.target)
		&& menu_cat_big.has(e.target).length === 0 && menu_cat_big.hasClass("menu-cat-big_hide") === false) {
		requestAnimationFrame(function(){
			$('body').css("overflow",'');
			setTimeout(()=>{
				menu_cat_big.css({"z-index": -1})
			},410)
			menu_cat_big.addClass("menu-cat-big_hide");
		});
	}
});


$(".menu-cat-btn").on("click",function(e){
		//let _this = $(this);

		if(menu_cat_big.hasClass("menu-cat-big_hide")){
			$('body').css("overflow",'hidden')
			requestAnimationFrame(function(){
				menu_cat_big.css({"z-index": 9999999})
				menu_cat_big.removeClass("menu-cat-big_hide");
			});
		}else{
			requestAnimationFrame(function(){
				$('body').css("overflow",'');
				setTimeout(()=>{
					menu_cat_big.css({"z-index": -1})
				},410)
				menu_cat_big.addClass("menu-cat-big_hide");
			});
		}
})

if( window.innerWidth < 768){ // close full menu items on mobail
	$(".toggle-menu-big[data-lvl='1']").removeClass("toggle-menu-big_open");
	$(".toggle-menu-big[data-lvl='2']").removeClass("toggle-menu-big_open");
	$("ul[data-lvl='2']").removeClass("show_lvl_1");
	$("ul[data-lvl='2']").removeClass("show_active");
 }
 //else{
// 	$(".menu-sub-item__not-sub").css("max-height","40px")
// }

$(".toggle-menu-big").on("click",function (e) {
		let _this = $(this);
		let data_class = _this.data("menu");
		let lvl = _this.data("lvl");
		let el = $(`.${data_class}`); // open item

		if( parseInt(lvl) === 1){ // close all 1 lvl. Use one open 1 lvl

			if( window.innerWidth > 768){ // desctop menu open
				requestAnimationFrame(function(){
					$(".toggle-menu-big[data-lvl='1']").removeClass("toggle-menu-big_open");
					_this.addClass("toggle-menu-big_open");
				})

				$("ul[data-lvl='1']").removeClass("show_lvl_1");
				setTimeout(function () {
					el.addClass("show_lvl_1")
				},210);
			} else{ // mobail menu open
				console.log("open-1 mob")
				if(!_this.hasClass("toggle-menu-big_open")){ // open/close item
					el.slideDown("slow");
					requestAnimationFrame(function(){
						_this.addClass("toggle-menu-big_open");
					})
				} else{
					console.log("cose mob")
					el.slideUp("slow");
					requestAnimationFrame(function(){
						_this.removeClass("toggle-menu-big_open");
					})
				}


			}

		} else { // lvl 2
			//if (window.innerWidth < 768) { // work on mobail

				if (el.hasClass("menu-sub-level-2")  ) { // open/close lvl 2
					if(!el.hasClass("show_active")){
						console.log("par",el.parent(".menu-sub-item-level-2"))
						el.slideDown("slow")
						el.addClass("show_active")
						requestAnimationFrame(function(){
							//el.parent(".menu-sub-item-level-2").css('max-height', '');
							_this.addClass("toggle-menu-big_open");
						})
					}else{
						el.slideUp("slow");
						el.removeClass("show_active")
						requestAnimationFrame(function(){
							//let elem_sub_menu_2 = el.parent(".menu-sub-item-level-2");
								//elem_sub_menu_2.css('max-height',elem_sub_menu_2.height()+'px');
								_this.removeClass("toggle-menu-big_open");
						})
					}
				}
			//}
		}

})