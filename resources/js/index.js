const $ = require("jquery");
const jQueryBridget = require("jquery-bridget");
const csrf = require("./includes/csrf");
const Flickity = require("flickity");
const Picture = require("./includes/picture_functions");
require("../views/blocks/popup/popup")
require("./libs/jq_input_mas")
require("./includes/popup.js")
const { detect } = require('detect-browser');
const browser = detect();
const WOW = require("./libs/wow/wow.min.js")
import { TweenLite, TimelineMax, Back } from "gsap/all";

// import "@babel/polyfill";
// import * as Sentry from '@sentry/browser';
//
// Sentry.init({
// 	dsn: 'https://94202b6bd3184618b3d4478c95dfda85@sentry.io/2293862'
// });

$(document).ready(function(){
    csrf.refresh();

    // if (document.body.webkitRequestFullScreen) {
    //     window.addEventListener('click', function(e) {
    //         if (e.target.type != 'text' && e.target.type != 'password') {
    //             document.body.webkitRequestFullScreen();
    //             window.setTimeout(function() {
    //                 document.webkitCancelFullScreen();
    //             }, 500);
    //         }
    //     }, false);
    // }

	/* wow animation */
		var wow = new WOW(
			{
				boxClass:     'wow',      // animated element css class (default is wow)
				animateClass: 'animated', // animation css class (default is animated)
				scrollContainer: ".body",
				offset:       0,          // distance to the element when triggering the animation (default is 0)
				mobile:       true,       // trigger animations on mobile devices (default is true)
				live:         true,       // act on asynchronously loaded content (default is true)
				callback:     function(box) {
					// the callback is fired every time an animation is started
					// the argument that is passed in is the DOM node being animated
				},
				resetAnimation: true,     // reset animation on end (default is true)
			}
		);
		wow.init();

	/* END wow animation */

    /*----- flickity wabpack init-----*/
    Flickity.setJQuery( $ );
    jQueryBridget( "flickity", Flickity, $ );
    /* ------------------------------*/

    /*----- slider --------*/
    var productCarusel = $(".cards_list_product"),
        pharmacyCarusel = $(".cards_list_pharmacy"),
        sellCarusel = $(".cards_list_sell"),
        citiesCarusel = $(".cards_list_cities"),
        flick_options = {
            freeScroll: true,
            freeScrollFriction: 0.10,
            selectedAttraction: 0.02,
            prevNextButtons: false,
            cellAlign: "left",
            contain: false,
            touchVerticalScroll: false,
            pageDots: false,
            autoPlay: false,
            //lazyLoad: true,
           // pauseAutoPlayOnHover: true
        },
        flick_options_mobile = {
            freeScroll: false,
            wrapAround: true,
            freeScrollFriction: 0.10,
            selectedAttraction: 0.02,
            prevNextButtons: false,
            cellAlign: "left",
            contain: false,
            touchVerticalScroll: false,
            pageDots: false,
            autoPlay: false,
            //lazyLoad: true,
            // pauseAutoPlayOnHover: true
        };
    /*----- END slider --------*/

    /*-----product slider --------*/
    if (browser && browser.name == "ios") {
        let version = browser.version.split(".");
        if(parseInt(version) <= 10 ){
            $(".product_item").addClass("product_item_ios-10");
           // alert(browser.name +" "+browser.version+" "+browser.os);
        }

    }
    productCarusel.flickity(flick_options);
    productCarusel.flickity('resize');

	// productCarusel.on( 'change.flickity', function( event, index ) {
	// 	console.log( '' + index )
	// });

    $(".slider-product-prev").on( "click", function() {
        productCarusel.flickity( "previous", true );
    });

    $(".slider-product-next").on( "click", function() {
        productCarusel.flickity( "next", true );
    });
    /*-----end product slider --------*/

    /*-----pharmacy slider --------*/
    if($(window).width() >= 996){
        pharmacyCarusel.flickity(flick_options);
        pharmacyCarusel.flickity('resize');
        console.log("desctop")
    }else{
        pharmacyCarusel.flickity(flick_options_mobile);
        pharmacyCarusel.flickity('resize');
        console.log("mobile")
    }

    $(".slider-pharmacy-prev").on( "click", function() {
        pharmacyCarusel.flickity( "previous", true );
    });

    $(".slider-pharmacy-next").on( "click", function() {
        pharmacyCarusel.flickity( "next", true );
    });
    /*-----end pharmacy slider --------*/

    /*-----sell slider --------*/
    sellCarusel.flickity(flick_options);
    sellCarusel.flickity('resize');

    $(".slider-sell-prev").on( "click", function() {
        sellCarusel.flickity( "previous", true );
    });

    $(".slider-sell-next").on( "click", function() {
        sellCarusel.flickity( "next", true );
    });
    /*-----end sell slider --------*/

    /*-----cities slider --------*/
    if($(window).width() <= 996){
        citiesCarusel.flickity(flick_options);
        citiesCarusel.flickity('resize');

        $(".slider-category-prev").on( "click", function() {
            citiesCarusel.flickity( "previous", true );
        });

        $(".slider-category-next").on( "click", function() {
            citiesCarusel.flickity( "next", true );
        });
    }else{
        $(".slider-category-next").fadeOut();
        $(".slider-category-prev").fadeOut();
    }

    /*-----end cities slider --------*/

    /*----- tabs address --------*/
    $(function () {
       let slide_pharmacy = $(".pharmacy-slider");
       let tab_length  = $(".tab_category");

       //первую кнопку делаем активной
        tab_length.first().addClass("active");

       //первому слайду ставим класс active
        slide_pharmacy.first().addClass("active");

       // slide_pharmacy.not('.active').hide();
    });

    $(".tab_category").click(function () {
        let data_id = $(this).data("id").toString();
        let slide_pharmacy = $(".pharmacy-slider");

        $(".tab_btn").removeClass("active");
        $(this).addClass("active");


        slide_pharmacy.removeClass("active").hide();

        let $slider_container = $(".section").find(".pharmacy-slider-" + data_id)
           .addClass("active")
           .show(0)
        $slider_container.find('.slider-list')
            .flickity('resize');
    });
    /*----- tabs address --------*/

    /*----- tabs product --------*/
    $(function () {
        let slide_product = $(".product-slider");

        //первому слайду ставим класс active
        slide_product.first().addClass("active");

        //slide_product.not('.active').hide();
    });

    $(".title_product_slider").click(function () {
        let data_product = $(this).data("product").toString();
        let slide_product = $(".product-slider");

        $(".title_product_slider").removeClass("active");
        $(this).addClass("active");


        slide_product.removeClass("active").hide(0);
        let $slider_container = $(".container").find(".cards_list_product-" + data_product);
        console.log("$slider_container_1 " + $slider_container);
        $slider_container.addClass("active").show(0);
        console.log("$slider_container_2 " + $slider_container);
        let $slider = $slider_container.find('.slider-list').data('flickity');
        console.log("$slider_container_3 " + $slider_container);
        console.log($slider, $slider_container, $slider_container.find('.slider-list'));
        $slider.resize();
    });
    /*----- tabs product --------*/


    /*----- popup basket --------*/
    //require("../views/blocks/modal_basket/modal_basket");
    /*----- end popup basket --------*/
	slider_icons_phone(
		5000,
		".Contact_Info_Icon_Slider",
		".Contact_Info_Icon_Slider_Item"
	);




	/*  header */
	require("../views/blocks/menu/menu_bottom/menu-cat/menu_big")
	require("../views/includes/sections/header/header")
	require("../views/blocks/menu/menu_top/btn/menu-btn")
	/*END header*/

	/* cart */
		// Cart.openCart(".Basket","get-produts-list",true,{position:true});
		// Cart.addCart(".btn_add_product",".Product_Item_Fn",function(){
		// 	$(".btn_add_product").fadeOut("slow",function(){
		// 		$(".btn_add_product").fadeIn("slow");
		// 	})
		// });
		// Cart.closeCart(".Hider_Modal_Def, .Cart_Close",true,{position:true});
		// Cart.plusCart();
		// Cart.minusCard();
		// Cart.delItemCart();
		// Cart.cartCustom(function(){
		// 	if (browser && browser.name == "ios") {
		// 		let version = browser.version.split(".");
		// 		if(parseInt(version) >= 12 ){
		// 			alert(version)
		// 			$(".modal_basket_top").css("margin-top","36px");
		// 		}
		//
		// 	}
		// });
	/* END cart */

    Picture.lazy_load_launch();
    Picture.background_is_picture_launch();

    require("./includes/global_auth");
    require("./includes/global_subscribe");
    require("./includes/global_callback");
    require("./includes/global");

    print_animate_text(["Высокий сервис обслуживания",
                            "Забота о каждом клиенте",
                            "Широкий ассортимент товарных позиций"],".header-desc", 0,0.3);

    const logo_anim = $(".logo_home");
	// let tl = new TimelineMax({repeat: -1, repeatDelay: 0.5});
	//
	// tl.staggerFrom(logo_anim, 0.8, {transform: "rotate3d"}, 0.06, "textEffect");
	let intervalId = null;
	let requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;

	setTimeout(()=>{
		requestAnimationFrame(()=>{
			logo_anim.addClass("active_rot");
		});

		intervalId = setInterval(()=>{

			console.log("animation",logo_anim);

			if(!logo_anim.hasClass("active_rot")){
				requestAnimationFrame(()=>{
					logo_anim.addClass("active_rot");
				});

			} else{
				requestAnimationFrame(()=>{
					logo_anim.removeClass("active_rot");
				});
			}

		},12000);

	},3000)



});

function slider_icons_phone(time,par_class,class_el){

	let sliders = $(par_class);
	for(var i = 0; i<sliders.length;i++){
		slides(sliders.eq(i));
	}

	function slides(item){
		var icon_list = item.children(class_el);
		var index_show = -1;
		setInterval(() => {
			index_show++;
			for(let i =0;i<icon_list.length;i++){
				requestAnimationFrame(function anim() {
					icon_list.css({"opacity":"0","visibility":"hidden"});
				});
			}

			if(index_show >= icon_list.length){
				index_show = 0;
			}
			requestAnimationFrame(function anim() {
				icon_list.eq(index_show).css({"opacity":"1","visibility":"visible"});
			});

		}, time);
	}

}

function print_animate_text(text,txtContainer,active=0,space=0){

   var  txtContainer = $(txtContainer),
        tl,
        txt,
        textIndex = active;

    function splitText(phrase) {
        var prevLetter,
            sentence = phrase.split("");

        txtContainer.html("");

        $.each(sentence, function(index, val) {
            if(val === " "){
                val = "&nbsp;";
            }
            var letter = $("<div/>", {
                id : "txt" + index
            }).addClass('txt').html(val).appendTo(txtContainer);

            if(prevLetter) {
                 $(letter).css("left", ($(prevLetter).position().left + $(letter).width()+space) + "px");
                //$(letter).css("left", ($(prevLetter).position().left + 18) + "px");
            }
            console.log(prevLetter,$(prevLetter).position(),$(letter).width(),letter);
            prevLetter = letter;
        });

        txt = $(".txt");
    }

    function buildTimeline() {
        TweenLite.set(txtContainer, {css:{perspective:500}});
        tl = new TimelineMax({repeat:0});
        tl.staggerFrom(txt, 0.4, {alpha:0}, 0.06, "textEffect");
        tl.staggerFrom(txt, 0.8, {rotationY:"-270deg", top:80, transformOrigin: "50% 50% -80", ease:Back.easeOut}, 0.06, "textEffect");
        tl.staggerTo(txt, 0.6, {rotationX:"360deg", color:"#fff", transformOrigin:"50% 50% 10"}, 0.02);
    }


    function init(text) {

        if(textIndex  >= text.length ){
            textIndex = 0;
        }
        splitText(text[textIndex]);
        textIndex++;
        buildTimeline();
        TweenLite.set($("#demoBackground"), {visibility:"visible"});
    }

    init(text);

    setInterval(()=>{

        tl.play()
          .reverse()
          .then(function(res) {
              console.log(textIndex,"index");
              init(text);
          })
          .catch(function (err) {
              console.log("error",err)
        })
    },10000)

}