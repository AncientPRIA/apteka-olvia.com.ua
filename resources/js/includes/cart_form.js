// Include in document ready

import Validator from "./validator";


var validator_cart = new Validator($('#form_cart_order'));
validator_cart.watch();
var ajax_request;

$("body").on("click", '#form_cart_order .submit',function(e){

    e.preventDefault();
    validator_cart.general_error_hide();

    var result = validator_cart.validate();

    if($(".container-select").length > 0){
	    if($(".form-adr").hasClass("error_active")){
		    $(".select-value").addClass("select-error");
	    }else{
		    $(".select-value").removeClass("select-error");
	    }
    }

    if(result !== false){

        result.append('locale', locale);
        result.append('_token', $('meta[name="csrf-token"]').attr('content'));
        ajax_request = $.ajax({
            url:    	baseUrl + '/cart_submit',
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
                    //document.location.reload();
                    window.popup_set_type('Notification', 'Notf_Good');
                    window.popup_show({cls: "Notification", optional_text: js_strings['order_success'], callbacks: {after_close: function () {
                        document.location.reload();
                    }}})
                }else{
                    if(response['type'] === 'wrong') {
                        validator_cart.general_error_show(js_strings['error_wrong_credentials'])
                    }else if(response['type'] === 'shop_empty'){
                        validator_cart.general_error_show(js_strings['error_shop_empty'])
                    }else if(response['type'] === 'validation'){
                        console.log("error",response);
                    }else if(response['type'] === 'exists'){
                        validator_cart.general_error_show(js_strings['error_exists'])
                    }
                }
            },
            error:function(response) {
                console.log("error",response);
            }
        });

    }
});
