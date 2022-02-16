let requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
const   toggle_active = "toggle-menu-aside_open",
        toggle = "toggle-menu-aside",
		lvl_2="menu-aside-sub-level-2",
		main_el = "menu-cat-aside";

if( window.innerWidth < 768){ // close full menu items on mobail
	$(`${toggle}`).removeClass(toggle_active);
	$(`.${main_el} ul[data-lvl='2']`).removeClass("show_lvl");
	$(`.${main_el} ul[data-lvl='2']`).removeClass("show_active");
}

$(`.${toggle}`).on("click",function (e) {
		let _this = $(this);
		let data_class = _this.data("menu");
		let lvl = _this.data("lvl");
		let el = $(`.${data_class}`); // open item

		if( parseInt(lvl) === 1){ // close all 1 lvl. Use one open 1 lvl
				if(!_this.hasClass(toggle_active)){ // open/close item
					el.slideDown("slow");
					_this.parent().addClass("active_item");
					requestAnimationFrame(function(){
						_this.addClass(toggle_active);
					})
				} else{
					el.slideUp("slow");
					_this.parent().removeClass("active_item");
					requestAnimationFrame(function(){
						_this.removeClass(toggle_active);
					})
				}

		} else { // lvl 2
				if (el.hasClass(lvl_2)  ) { // open/close lvl 2
					if(!el.hasClass("show_active")){
						el.slideDown("slow")
						el.addClass("show_active")
						requestAnimationFrame(function(){
							_this.addClass(toggle_active);
						})
					}else{
						el.slideUp("slow");
						el.removeClass("show_active")
						requestAnimationFrame(function(){
							_this.removeClass(toggle_active);
						})
					}
				}
		}

})


//
var menu_current = $('.menu-aside').find('li.current');
if(menu_current.length > 0){
    var flag = true;
    var li = menu_current;
    var bottom_ul = li.children('ul');
    if(bottom_ul.length > 0){
        bottom_ul.slideDown(0);
        bottom_ul.addClass('show_active');
	}
    while (flag){
        var ul = li.closest('ul');
        //console.log(ul, li);

        var arrow = li.children().children('.toggle-menu-aside');
        arrow.addClass('toggle-menu-aside_open');
        ul.slideDown(0);
        ul.addClass('show_active');

        li = ul.closest('li');

        if(ul.hasClass('menu-aside')){
            flag = false;
        }else{
        }
    }
}

var menu_item = $(".menu-aside-sub-item.current");

var current_item = menu_item.find('div.menu-aside-sub-item__nav').first().css( "background-color", "#FCE8E9" );
current_item.parent().removeClass("current")




