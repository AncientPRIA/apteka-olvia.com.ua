
import Validator from "./validator";

var validator_subscribe = new Validator($('#form-subscribe'));
validator_subscribe.watch();

var ajax_request;

$(".btn-subscribe-submit").on("click",function(e){

    e.preventDefault();
    //var validator = new Validator($('#form-reg'));
    var result = validator_subscribe.validate();
    if(result !== false){

        result.append('locale', locale);
        result.append('_token', $('meta[name="csrf-token"]').attr('content'));
        console.log(baseUrl);
        ajax_request = $.ajax({
            url:    	baseUrl + '/subscribe',
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
                    window.popup_set_type('Notification', "Notf_Good");
                    window.popup_show({cls: "Notification", optional_text: "Вы подписались на рассылку"})
                }else{
                    if(response['type'] === 'wrong'){
                        validator_reg.general_error_show(js_strings['error_wrong_credentials'])
                    }else if(response['type'] === 'validation'){
                        console.log("error",response);
                    }else if(response['type'] === 'exists'){
                        window.popup_set_type('Notification', "Notf_Good");
                        window.popup_show({cls: "Notification", optional_text: "Вы уже подписаны"})
                        //validator_reg.general_error_show(js_strings['error_exists'])
                    }
                }
            },
            error:function(response) {
                console.log("error",response);
            }
        });

    }
});