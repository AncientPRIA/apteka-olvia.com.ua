var $ = require("jquery");
require("../views/blocks/popup/popup")
require("./includes/form.js");
var Picture = require("./includes/picture_functions");
const cookies = require("./includes/cookies");
const { detect } = require('detect-browser');
const browser = detect();
const WOW = require("./libs/wow/wow.min.js")
$(document).ready(function(){

    /* wow animation */
    var wow = new WOW(
        {
            boxClass:     'wow',      // animated element css class (default is wow)
            animateClass: 'animated', // animation css class (default is animated)
            offset:       0,          // distance to the element when triggering the animation (default is 0)
            mobile:       true,       // trigger animations on mobile devices (default is true)
            live:         true,       // act on asynchronously loaded content (default is true)
            callback:     function(box) {
                // the callback is fired every time an animation is started
                // the argument that is passed in is the DOM node being animated
            },
            scrollContainer: ".body",    // optional scroll container selector, otherwise use window,
            resetAnimation: true,     // reset animation on end (default is true)
        }
    );
    wow.init();


    /* END wow animation */

    if (browser && browser.name == "ios") {
        let version = browser.version.split(".");
        if(parseInt(version) <= 10 ){
            $(".product_item").addClass("product_item_ios-10");
            // alert(browser.name +" "+browser.version+" "+browser.os);
        }

    }

	/*  sidebar toggle */
		require("../views/blocks/sidebar/cat/cat-toggle")
	/*END */

    /*  header */
	    require("../views/blocks/menu/menu_bottom/menu-cat/menu_big")
	    require("../views/includes/sections/header/header")
	    require("../views/blocks/menu/menu_top/btn/menu-btn")
    /*END header*/

    Picture.lazy_load_launch();
    Picture.background_is_picture_launch();

    //open sidebar
    var modal_sidebar_open = false;
    $(".section_sidebar_mobileBtn").on("click", function () {

        if(modal_sidebar_open === false){
            $(".section_sidebar").addClass("active");
            $(".section_sidebar_mobileBtn").addClass("active");
            modal_sidebar_open = true
	        $("body").css("overflow","hidden");
        }else{
            $(".section_sidebar").removeClass("active");
            $(".section_sidebar_mobileBtn").removeClass("active");
	        $("body").css("overflow","");
            modal_sidebar_open = false
        }

    });

    // ----- Product sorting ----- //
    $('.product_select').on('change', function () {
        var new_sorting = $(this).val();
        if(new_sorting === sorting){
            return;
        }
 
        cookies.set_cookie('products_sort', new_sorting, -1);
        document.location.reload();

    });
    // ----- Product sorting END ----- //

    var is_loading = false;
    var ajaxed_request = null;

    $(".product_load_more").on("click",function () {
        const load_svg = $(this).children(".product_load_more_svg");
        const load_active_class = 'Load_More_Active';
        const url = window.baseUrl+"/ajax/products/load_more";
        const items_container = $('.product_list');

        if (is_loading === true) {
            load_svg.removeClass(load_active_class);
            if (ajaxed_request !== null) {
                ajaxed_request.abort();
            }
        }
        is_loading = true;

        ajaxed_request = $.ajax({
            url:    	url,
            type:		'POST',
            cache: 	    false,
            data:   	{'page': page, 'category_id': category_id, 'sorting': sorting, "_token":$('meta[name="csrf-token"]').attr('content')},
            beforeSend: function() {
                load_svg.addClass(load_active_class);
            },
            success: function(response) {
                if(response['status'] === '1'){

                    if(response['content'].length > 0){
                        items_container.append(response['content']);
                        page++;
                        Picture.lazy_load_launch();
                        Picture.background_is_picture_launch();
                    }else{
                        //$('.Vacancies_Container').append(`<div style="text-align: center">That's all!</div>`);
                        // Nothing more
                    }
                    load_svg.removeClass(load_active_class);
                    is_loading = false;

                }else{
                    console.log('ERROR!', response);
                    load_svg.removeClass(load_active_class);
                    is_loading = false
                }
            },
            error:function(response) {
                console.log("error",response);
                load_svg.removeClass(load_active_class);
                is_loading = false
            }
        });

    });

	require("./includes/global_auth");
    require("./includes/global_subscribe");
    require("./includes/global_callback");
    require("./includes/global");



});
