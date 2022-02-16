import Validator from "./includes/validator";
const $ = require("jquery");
const Picture = require("./includes/picture_functions");
require("../views/blocks/popup/popup");
require("./libs/jq_input_mas");
require("./includes/form.js");
const WOW = require("./libs/wow/wow.min.js");
var ajax_request;

$(document).ready(function() {
    /* wow animation */
    var wow = new WOW({
        boxClass: "wow", // animated element css class (default is wow)
        animateClass: "animated", // animation css class (default is animated)
        offset: 0, // distance to the element when triggering the animation (default is 0)
        mobile: true, // trigger animations on mobile devices (default is true)
        live: true, // act on asynchronously loaded content (default is true)
        callback: function(box) {
            // the callback is fired every time an animation is started
            // the argument that is passed in is the DOM node being animated
        },
        scrollContainer: ".body", // optional scroll container selector, otherwise use window,
        resetAnimation: true // reset animation on end (default is true)
    });
    wow.init();

    /* END wow animation */

    /*----- end popup basket --------*/
    // slider_icons_phone(
    // 	5000,
    // 	".Contact_Info_Icon_Slider",
    // 	".Contact_Info_Icon_Slider_Item"
    // );

    /*  header */
    require("../views/blocks/menu/menu_bottom/menu-cat/menu_big");
    require("../views/includes/sections/header/header");
    require("../views/blocks/menu/menu_top/btn/menu-btn");
    /*END header*/

    // Contact form submit
    var validator_contact = new Validator($("#contact_form"));
    validator_contact.watch();
    $("body").on("click", "#contact_form .btn-submit", function(e) {
        e.preventDefault();
        var result = validator_contact.validate();
        if (result !== false) {
            result.append("locale", locale);
            result.append(
                "_token",
                $('meta[name="csrf-token"]').attr("content")
            );
            console.log(baseUrl);
            ajax_request = $.ajax({
                url: baseUrl + "/ajax/contact_submit",
                type: "POST",
                cache: false,
                data: result,
                processData: false,
                contentType: false,
                beforeSend: function() {},
                success: function(response) {
                    if (response["status"] === "1") {
                        console.log("OK");
                        window.popup_set_type("Notification", "Notf_Good");
                        window.popup_show({
                            cls: "Notification",
                            optional_text: js_strings["success_contact_submit"],
                            callbacks: {
                                after_close: function() {
                                    document.location.reload();
                                }
                            }
                        });
                    } else {
                        if (response["type"] === "mail_fail") {
                            window.popup_set_type("Notification", "Notf_Bad");
                            window.popup_show({
                                cls: "Notification",
                                optional_text: js_strings["error_mail_fail"]
                            });
                        } else {
                            console.log(response);
                        }
                    }
                },
                error: function(response) {
                    console.log("error", response);
                }
            });
        }
    });

    Picture.lazy_load_launch();
    Picture.background_is_picture_launch();

    require("./includes/global_auth");
    require("./includes/global_subscribe");
    require("./includes/global_callback");
    require("./includes/global");

    if ($(".jobs-list").length > 0) {
        let links = $(".jobs-list a");
        console.log("links: ", links);
        for (let i = 0; i < links.length; i++) {
            let href = links[i].getAttribute("href");
            console.log("href: ", href, href.indexOf("+"));

            if (href.indexOf("+") !== -1) {
                links[i].setAttribute("href", `tel:${href}`);
            }
        }
    }

    if ($(".partners-list").length > 0) {
        let ul = $(".partners-list ul");
        for (let i = 0; i < ul.length; i++) {
            if (i % 2 === 0) {
                ul.eq(i).addClass("order");
            }
        }
    }
});

function slider_icons_phone(time, par_class, class_el) {
    let sliders = $(par_class);
    for (var i = 0; i < sliders.length; i++) {
        slides(sliders.eq(i));
    }

    function slides(item) {
        var icon_list = item.children(class_el);
        var index_show = -1;
        setInterval(() => {
            index_show++;
            for (let i = 0; i < icon_list.length; i++) {
                requestAnimationFrame(function anim() {
                    icon_list.css({ opacity: "0", visibility: "hidden" });
                });
            }

            if (index_show >= icon_list.length) {
                index_show = 0;
            }
            requestAnimationFrame(function anim() {
                icon_list
                    .eq(index_show)
                    .css({ opacity: "1", visibility: "visible" });
            });
        }, time);
    }
}
