var $ = require("jquery");
var Picture = require("./includes/picture_functions");
require("../views/blocks/popup/popup")
import Validator from "./includes/validator"
require("./libs/jq_input_mas")
require("./includes/form.js")
//const {checkbox} = require("./includes/form_checkbox")


$(document).ready(function(){

    Picture.lazy_load_launch();
    Picture.background_is_picture_launch();

    /*  header */
    require("../views/blocks/menu/menu_bottom/menu-cat/menu_big")
    require("../views/includes/sections/header/header")
    require("../views/blocks/menu/menu_top/btn/menu-btn")
    /*END header*/

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

    /* form auntif event */
    var ajax_request;

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


    require("./includes/cart_form.js")

    require("./includes/global_auth");
    require("./includes/global_subscribe");
    require("./includes/global_callback");
    require("./includes/global");

    /* select */
		let select_list = $(".select-list");
		$(".select-value").on("click",function(){

			if(!$(this).hasClass("active")){
				$(this).addClass("active");
				select_list.slideDown("slow");
			}else{
				$(this).removeClass("active");
				select_list.slideUp("slow");
			}

		});

		const input_adress = $(".street-input");

		// input_adress.trigger("change",function(){
		// 	console.log("change");
		// 	if($(".form-adr").hasClass("error_active")){
		// 		$(".select-value").addClass("select-error");
		// 	}else{
		// 		$(".select-value").removeClass("select-error");
		// 	}
		// });

		$(".select-item__street").on("click",function(){
			let city = $(this).data("city").split("|");
			let street = $(this).data("id").split("|");
			input_adress.val(street[1]);
			$(".select-value").text(city[0]+" "+street[0]);

			$(".select-value").removeClass("active");
			select_list.slideUp("slow");

			$(".select-value").removeClass("select-error");

		});

	/* END select */

});

