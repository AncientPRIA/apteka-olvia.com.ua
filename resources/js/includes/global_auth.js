// in document ready
import Validator from "./validator";
const { checkbox } = require("./form_checkbox");

var validator_login = new Validator($("#form-login"));
var validator_reg = new Validator($("#form-reg"));
var validator_restore = new Validator($("#form-restore"));
var validator_call_back = new Validator($("#form-call-back"));
validator_login.watch();
validator_reg.watch();
validator_restore.watch();
validator_call_back.watch();

var ajax_request;

// Register button
$(".btn-reg").on("click", function(e) {
    e.preventDefault();
    //var validator = new Validator($('#form-reg'));
    var result = validator_reg.validate();
    if (result !== false) {
        result.append("locale", locale);
        result.append("_token", $('meta[name="csrf-token"]').attr("content"));
        console.log(baseUrl);
        ajax_request = $.ajax({
            url: baseUrl + "/register",
            type: "POST",
            cache: false,
            data: result,
            processData: false,
            contentType: false,
            beforeSend: function() {},
            success: function(response) {
                if (response["status"] === "1") {
                    console.log("OK");
                    //document.location = response['redirect'];
                    window.popup_set_type("Notification", "Notf_Good");
                    window.popup_show({
                        cls: "Notification",
                        optional_text: js_strings["success_check_availability"]
                    });
                    console.log("go to  log", $(".show-new-modal.Log_Link"));
                    $(".show-new-modal.Log_Link").click();
                } else {
                    if (response["type"] === "wrong") {
                        validator_reg.general_error_show(
                            js_strings["error_wrong_credentials"]
                        );
                    } else if (response["type"] === "validation") {
                        console.log("error", response);
                    } else if (response["type"] === "exists") {
                        validator_reg.general_error_show(
                            js_strings["error_exists"]
                        );
                    }
                }
            },
            error: function(response) {
                console.log("error", response);
            }
        });
    }
});

// Register button
$(".btn-login").on("click", function(e) {
    e.preventDefault();
    //var validator = new Validator($('#form-reg'));
    var result = validator_login.validate();
    if (result !== false) {
        result.append("locale", locale);
        result.append("_token", $('meta[name="csrf-token"]').attr("content"));
        console.log(baseUrl);
        ajax_request = $.ajax({
            url: baseUrl + "/auth",
            type: "POST",
            cache: false,
            data: result,
            processData: false,
            contentType: false,
            beforeSend: function() {},
            success: function(response) {
                if (response["status"] === "1") {
                    console.log("OK");
                    //document.location = response['redirect'];
                    //window.popup_show("Notf_Good", svg_good, "Ваш заказ принят в обратботку")
                    document.location.reload();
                } else {
                    if (response["type"] === "wrong") {
                        validator_login.general_error_show(
                            js_strings["error_wrong_credentials"]
                        );
                    } else if (response["type"] === "validation") {
                        console.log("error", response);
                    } else if (response["type"] === "verification") {
                        console.log(js_strings["error_verification"]);
                        validator_login.general_error_show(
                            js_strings["error_verification"]
                        );
                    }
                }
            },
            error: function(response) {
                console.log("error", response);
            }
        });
    }
});

// Password restore button
$(".btn-restore").on("click", function(e) {
    e.preventDefault();
    var result = validator_restore.validate();
    if (result !== false) {
        result.append("locale", locale);
        result.append("_token", $('meta[name="csrf-token"]').attr("content"));
        console.log(baseUrl);
        ajax_request = $.ajax({
            url: baseUrl + "/password/email",
            type: "POST",
            cache: false,
            data: result,
            processData: false,
            contentType: false,
            beforeSend: function() {},
            success: function(response) {
                if (response["status"] === "1") {
                    console.log("OK");
                    window.popup_show({
                        cls: "Notification",
                        optional_text: js_strings["success_password_reset"]
                    });
                    //document.location = response['redirect'];
                } else {
                    console.log("error", response);
                    validator_restore.general_error_show(
                        js_strings["error_wrong_email"]
                    );
                    /*
                    if(response['type'] === 'wrong'){
                        validator.general_error_show(js_strings['error_wrong_credentials'])
                    }else if(response['type'] === 'validation'){
                        console.log("error",response);
                    }
                    */
                }
            },
            error: function(response) {
                console.log("error", response);
            }
        });
    }
});

/* END form auntif event */

checkbox("checkbox"); // remember me

/* modal aunif exchange*/
$(".show-new-modal").on("click", function() {
    let _this = $(this),
        data = _this.data("showModal"),
        arr_data = data.split("|");

    $(".hider_class").slideUp("slow", function() {
        setTimeout(function() {
            $(`.${arr_data[1]}`).fadeIn("slow");
            $(`.${arr_data[0]}`).fadeIn("slow");
            $(`#${arr_data[2]}`).slideDown("slow");
        }, 1000);
    });

    console.log(arr_data);
});
/* END modal aunif exchange*/
require("../../views/blocks/default/default");
require("./global_modal_politica");

require("../../views/blocks/menu/menu_mobail/menu.js");
// const Modal = require("./includes/global_modal_politica");
//
// Modal.global_modal_polit();

// width display  to server
