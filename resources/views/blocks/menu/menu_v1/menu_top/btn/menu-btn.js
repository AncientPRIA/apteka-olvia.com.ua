let menu = $(".menu");

$(".menu-open").on("click",function(){

	var btn_menu = $(this);

	if(btn_menu.hasClass('active')){
		btn_menu.removeClass("active");
		menu.slideUp("slow")
	} else{
		btn_menu.addClass("active");
		menu.slideDown("slow")
	}
});