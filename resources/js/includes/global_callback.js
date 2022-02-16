
import Validator from "./validator";

var validator_callback = new Validator($('#form-call-back'));
validator_callback.watch();

var ajax_request;

$("body").on('click', '#form-call-back .btn-login', function (e) {
    console.log('#form-call-back submit');

    e.preventDefault();
    var result = validator_callback.validate();
    if(result !== false){

        result.append('locale', locale);
        result.append('_token', $('meta[name="csrf-token"]').attr('content'));
        console.log(baseUrl);
        ajax_request = $.ajax({
            url:    	baseUrl + '/ajax/callback_submit',
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
                    window.popup_set_type('Notification', "Notf_Good");
                    window.popup_show({cls: "Notification", optional_text: js_strings['success_callback_submit'], callbacks:{after_close: function () {
                            document.location.reload();
                        }}});
                }else{

                    if(response['type'] === 'mail_fail'){
                        window.popup_set_type('Notification', "Notf_Bad");
                        window.popup_show({cls: "Notification", optional_text:js_strings['error_mail_fail']})
                    }else{
                        console.log(response);
                    }
                }
            },
            error:function(response) {
                console.log("error",response);
            }
        });

    }
})