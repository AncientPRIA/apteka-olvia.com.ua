import Validator from "./includes/validator";

var $ = require("jquery");
require("./includes/form.js");
require("./libs/jq_input_mas");
require("../views/blocks/popup/popup")
//const {checkbox} = require("./includes/form_checkbox")
var Picture = require("./includes/picture_functions");
const { detect } = require('detect-browser');
const browser = detect();

$(document).ready(function(){


    /*  header */
    require("../views/blocks/menu/menu_bottom/menu-cat/menu_big")
    require("../views/includes/sections/header/header")
    require("../views/blocks/menu/menu_top/btn/menu-btn")
    /*END header*/


    if (browser && browser.name == "ios") {
        let version = browser.version.split(".");
        if(parseInt(version) <= 10 ){
            $(".product_item").addClass("product_item_ios-10");
            alert(browser.name +" "+browser.version+" "+browser.os);
        }

    }

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
                        $('.show-new-modal.Log_Link').click();
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

    Picture.lazy_load_launch();
    Picture.background_is_picture_launch();

    require("./includes/global_auth");
    require("./includes/global_subscribe");
    require("./includes/global_callback");
    require("./includes/global");

});