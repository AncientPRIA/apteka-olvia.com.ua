$(".header-top-bar-phone").on("click",function(){

	if($(this).hasClass("active")){
		$(this).removeClass("active");
		$(".phone-list").removeClass("phone-list-active");
		//$(".body").css("overflow","");
		scroll_on($(this));
	}else{
		$(this).addClass("active");
		$(".phone-list").addClass("phone-list-active");
		//$(".body").css("overflow","hidden");
        scroll_off($(this));
	}
})

if($(".header_transparent").length > 0){
	$("main").addClass("transparent_header")
}