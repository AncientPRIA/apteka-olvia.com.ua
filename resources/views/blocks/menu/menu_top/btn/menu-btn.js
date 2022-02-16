let menu = $(".menu");

$(".menu-open").on("click",function(){

	var btn_menu = $(this);

	if(btn_menu.hasClass('active')){
		btn_menu.removeClass("active");
		menu.slideUp("slow")
		//if(window_width < 996){
			page_scroll_position = $('html').scrollTop() || $('body').scrollTop();
			$('body').css('position', 'relative');
		//}
		$("body").css("overflow","");
	} else{

		///if(window_width < 996){
			page_scroll_position = $('html').scrollTop() || $('body').scrollTop();
			$('body').css('position', 'fixed');
		//}

		$("body").css("overflow","hidden");
		btn_menu.addClass("active");
		menu.slideDown("slow",function(){
			menu.css("display","flex")
		})
	}
});