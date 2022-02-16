// Include in document ready

import Validator from "./validator";


var validator_profile = new Validator($('#form-profile'));
validator_profile.watch();
var ajax_request;

$("body").on("click", '#form-profile .btn-upt',function(e){

    e.preventDefault();
    validator_profile.general_error_hide();

    var result = validator_profile.validate();
    if(result !== false){

        result.append('locale', locale);
        result.append('_token', $('meta[name="csrf-token"]').attr('content'));
        ajax_request = $.ajax({
            url:    	baseUrl + '/profile_update',
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
                    window.popup_set_type('Notification', 'Notf_Good');
                    window.popup_show({cls: "Notification", optional_text: js_strings['profile_update_success']})
                }else{
                    if(response['type'] === 'wrong'){
                        validator_profile.general_error_show(js_strings['error_wrong_credentials'])
                    }else if(response['type'] === 'validation'){
                        console.log("error",response);
                    }else if(response['type'] === 'exists'){
                        validator_profile.general_error_show(js_strings['error_exists'])
                    }
                }
            },
            error:function(response) {
                console.log("error",response);
            }
        });

    }
});
