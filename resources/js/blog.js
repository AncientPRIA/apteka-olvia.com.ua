import Validator from "./includes/validator";
const Flickity = require("flickity");
const jQueryBridget = require("jquery-bridget");
const $ = require("jquery");
require("./includes/form.js");
require("./libs/jq_input_mas");
// const Modal = require("./includes/global_modal_politica");
require("../views/blocks/popup/popup")
//const {checkbox} = require("./includes/form_checkbox")
const Picture = require("./includes/picture_functions");
const WOW = require("./libs/wow/wow.min.js")

$(document).ready(function(){

    require("./includes/global_auth");
    require("./includes/global_subscribe");
    require("./includes/global_callback");
    require("./includes/global");

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

    // Modal.global_modal_polit();

    Picture.lazy_load_launch();
    Picture.background_is_picture_launch();

    /*----- flickity wabpack init-----*/
    Flickity.setJQuery( $ );
    jQueryBridget( "flickity", Flickity, $ );
    /* ------------------------------*/

    /*  header */
    require("../views/blocks/menu/menu_bottom/menu-cat/menu_big")
    require("../views/includes/sections/header/header")
    require("../views/blocks/menu/menu_top/btn/menu-btn")
    /*END header*/


    /*----- slider --------*/
    if($(window).width() <= 996){
        var categorytCarusel = $(".cards_list_category"),
            flick_options = {
                freeScroll: true,
                freeScrollFriction: 0.10,
                selectedAttraction: 0.02,
                prevNextButtons: false,
                cellAlign: "left",
                contain: false,
                touchVerticalScroll: true,
                pageDots: false,
                autoPlay: false,
                //lazyLoad: true,
                // pauseAutoPlayOnHover: true
            };


    }
    /*----- END slider --------*/

    /*-----category slider --------*/
    if($(window).width() <= 996){
        categorytCarusel.flickity(flick_options);
        categorytCarusel.flickity('resize');

        $(".slider-arrow-prev").on( "click", function() {
            categorytCarusel.flickity( "previous", true );
        });

        $(".slider-arrow-next").on( "click", function() {
            categorytCarusel.flickity( "next", true );
        });
    }else{
        $(".slider-category-next").fadeOut();
        $(".slider-category-prev").fadeOut();
    }

    /*-----end category slider --------*/

    $(function () {
      let category_length = $(".tab_category")

        category_length.first().addClass("active");
    });

    /*----- Load more -----*/
    var is_loading = false;
    var ajaxed_request = null;
    $(".product_load_more").on("click",function () {
        const load_svg = $(this).children(".product_load_more_svg");
        const load_active_class = 'Load_More_Active';
        const url = window.baseUrl+"/ajax/blog/load_more";
        const items_container = $('.blog_content');

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
            data:   	{'page': page, 'category_id': category_id, "_token":$('meta[name="csrf-token"]').attr('content')},
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
    /*----- Load more END -----*/


    // Register button
    $(".btn-reg").on("click",function(e){

        e.preventDefault();
        //var validator = new Validator($('#form-reg'));
        var result = validator_reg.validate();
        if(result !== false){

            result.append('locale', locale);
            result.append('_token', $('meta[name="csrf-token"]').attr('content'));
            console.log(baseUrl);
            ajax_request = $.ajax({
                url:    	baseUrl + '/register',
                type:		'POST',
                cache: 	    false,
                data:   	result,
                processData: false,
                contentType: false,
                beforeSend: function() {

                },
                success: function(response) {
                    if(response['status'] === '1'){
                        console.log('OK');
                        //document.location = response['redirect'];
                        window.popup_show({cls: "Notf_Good", optional_title: svg_good, optional_text: "Ваш заказ принят в обратботку"})
                    }else{
                        if(response['type'] === 'wrong'){
                            validator.general_error_show(js_strings['error_wrong_credentials'])
                        }else if(response['type'] === 'validation'){
                            console.log("error",response);
                        }
                    }
                },
                error:function(response) {
                    console.log("error",response);
                }
            });

        }
    });

    // Password restore button

    $(".btn-restore").on("click",function(e){

        e.preventDefault();
        var result = validator_restore.validate();
        if(result !== false){

            result.append('locale', locale);
            result.append('_token', $('meta[name="csrf-token"]').attr('content'));
            console.log(baseUrl);
            ajax_request = $.ajax({
                url:    	baseUrl + '/password/email',
                type:		'POST',
                cache: 	    false,
                data:   	result,
                processData: false,
                contentType: false,
                beforeSend: function() {

                },
                success: function(response) {
                    if(response['status'] === '1'){
                        console.log('OK');
                        //document.location = response['redirect'];
                    }else{
                        console.log("error",response);
                        /*
                        if(response['type'] === 'wrong'){
                            validator.general_error_show(js_strings['error_wrong_credentials'])
                        }else if(response['type'] === 'validation'){
                            console.log("error",response);
                        }
                        */
                    }
                },
                error:function(response) {
                    console.log("error",response);
                }
            });

        }
    });
    /* END form auntif event */

    /* validator */
    var validator_reg = new Validator($('#form-reg'));
    var validator_restore = new Validator($('#form-restore'));
    var validator_call_back = new Validator($('#form-call-back'));
    validator_reg.watch();
    validator_restore.watch();
    validator_call_back.watch();
    /* END validator */

    /* modal aunif */
    //checkbox("checkbox"); // remmember me

    $(".show-new-modal").on("click",function(){
        let _this = $(this),
            data = _this.data("showModal"),
            arr_data = data.split("|");

        $(".hider_class").slideUp("slow",function(){
            setTimeout(function(){
                $(`.${arr_data[1]}`).fadeIn("slow");
                $(`.${arr_data[0]}`).fadeIn("slow");
                $(`#${arr_data[2]}`).slideDown("slow");
            },1000)

        });

        console.log(arr_data);
    });
    /* END modal aunif */

});