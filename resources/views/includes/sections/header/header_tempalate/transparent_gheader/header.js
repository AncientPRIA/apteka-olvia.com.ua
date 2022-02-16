$(".header-top-bar-phone").on("click",function(){

	if($(this).hasClass("active")){
		$(this).removeClass("active");
		$(".phone-list").removeClass("phone-list-active");
	}else{
		$(this).addClass("active");
		$(".phone-list").addClass("phone-list-active");
	}
})