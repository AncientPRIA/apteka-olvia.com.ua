// var nav = $('.Header_Fix');
var scroll_btn = $(".Btn_Scroll_Top");

$(window).scroll(function () {

    // if(window.location.pathname!='/'){
    //     $(this).scrollTop() > 200? nav.addClass("Show") : nav.removeClass("Show");
    // }

    $(this).scrollTop() > 300 ?  scroll_btn.addClass("scroll-to-top_active") : scroll_btn.removeClass("scroll-to-top_active");
});

var scroll_allowed = true;
scroll_btn.on("click",function(){
    if(!scroll_allowed){
        return;
    }
    scroll_allowed = false;

    $([document.documentElement, document.body]).animate({
        scrollTop: $("header").offset().top
    }, 1000, null, function () {
        scroll_allowed = true;
    });
});