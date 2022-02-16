var $ = require("jquery");
require("../views/blocks/popup/popup")
import Validator from "./includes/validator"
require("./libs/jq_input_mas")
// require("./libs/jquery.scrollbar.min.js")
require("./includes/form.js")
//const {checkbox} = require("./includes/form_checkbox")
const Cart = require("./includes/cart")


$(document).ready(function(){

	/*  header */
		require("../views/blocks/menu/menu_bottom/menu-cat/menu_big")
		require("../views/includes/sections/header/header")
		require("../views/blocks/menu/menu_top/btn/menu-btn")
	/*END header*/

	/* validator */
	    var validator_reg = new Validator($('#form-reg'));
	    var validator_restore = new Validator($('#form-restore'));
	    validator_reg.watch();
	    validator_restore.watch();
	/* END validator */

    /* cart */
		Cart.openCart(".Basket","test",true);
		Cart.addCart(".btn_add_product",".product_item");
	/* END cart */

	/* modal aunif */
	checkbox("checkbox"); // remmember me

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
});

