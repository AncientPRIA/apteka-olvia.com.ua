console.log(window.innerWidth);
if(window.innerWidth < 996){
	const nav = $('.menu-mobail-container');
	const single_nav_fix = $('.single-nav-fixed');
	nav.css("position","fixed");

	$(window).scroll(function () {
		console.log($(this).scrollTop())
		if ($(this).scrollTop() > 50) {
			nav.addClass("f-nav");
			//$('.header-content').css('padding-top',"122px");
			$(".menu-cat-big").addClass("menu-cat-big_fix");
			single_nav_fix.addClass('single-nav-fixed_active');
		} else {
			nav.removeClass("f-nav");
			//$('.header-content').css('padding-top',"0px");
			$(".menu-cat-big").removeClass("menu-cat-big_fix");
			single_nav_fix.removeClass('single-nav-fixed_active');
		}
	});

	$(".search-btn-menu").on("click",function(){
		let self = $(this)

		if(!self.hasClass("active")){

			self.addClass("active");
			if($(".single-nav-fixed").length > 0){
				$(".single-nav-fixed").fadeOut("slow",function(){
					$(".mobail_form_container").slideDown("slow");
				})
			} else {
				$(".mobail_form_container").slideDown("slow");
			}

		}else{
				$(".mobail_form_container").slideUp("slow",function () {
					if($(".single-nav-fixed").length > 0){
						$(".single-nav-fixed").fadeIn("slow");
					}
				});
			self.removeClass("active");

		}
	})
}
