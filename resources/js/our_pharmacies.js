var $ = require("jquery");
var jQueryBridget = require("jquery-bridget");
var Flickity = require("flickity");
var Picture = require("./includes/picture_functions");
require("../views/blocks/popup/popup");
import Validator from "./includes/validator";
require("./libs/jq_input_mas");
require("./includes/form.js");
const WOW = require("./libs/wow/wow.min.js");
const { checkbox } = require("./includes/form_checkbox");

$(document).ready(function() {
    Picture.lazy_load_launch();
    Picture.background_is_picture_launch();

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

    /*----- flickity webpack init-----*/
    Flickity.setJQuery($);
    jQueryBridget("flickity", Flickity, $);
    /* ------------------------------*/

    /*  header */
    require("../views/blocks/menu/menu_bottom/menu-cat/menu_big");
    require("../views/includes/sections/header/header");
    require("../views/blocks/menu/menu_top/btn/menu-btn");
    /*END header*/

    /*----- slider --------*/
    var categorytCarusel = $(".cards_list_cities"),
        pharmacyCarusel = $(".cards_list_pharmacy"),
        flick_options = {
            freeScroll: true,
            freeScrollFriction: 0.1,
            selectedAttraction: 0.02,
            prevNextButtons: false,
            cellAlign: "left",
            contain: false,
            touchVerticalScroll: true,
            pageDots: false,
            autoPlay: false
            //lazyLoad: true,
            // pauseAutoPlayOnHover: true
        },
        flick_options_mobile = {
            freeScroll: false,
            wrapAround: true,
            freeScrollFriction: 0.1,
            selectedAttraction: 0.02,
            prevNextButtons: false,
            cellAlign: "left",
            contain: false,
            touchVerticalScroll: false,
            pageDots: false,
            autoPlay: false
            //lazyLoad: true,
            // pauseAutoPlayOnHover: true
        };
    /*----- END slider --------*/

    /*-----pharmacy slider --------*/
    if ($(window).width() >= 996) {
        pharmacyCarusel.flickity(flick_options);
        pharmacyCarusel.flickity("resize");
        console.log("desctop");
    } else {
        pharmacyCarusel.flickity(flick_options_mobile);
        pharmacyCarusel.flickity("resize");
        console.log("mobile");
    }
    $(".slider-pharmacy-prev").on("click", function() {
        pharmacyCarusel.flickity("previous", true);
    });

    $(".slider-pharmacy-next").on("click", function() {
        pharmacyCarusel.flickity("next", true);
    });
    /*-----end pharmacy slider --------*/

    /*-----category slider --------*/
    if ($(window).width() <= 996) {
        categorytCarusel.flickity(flick_options);
        categorytCarusel.flickity("resize");

        $(".slider-arrow-prev").on("click", function() {
            categorytCarusel.flickity("previous", true);
        });

        $(".slider-arrow-next").on("click", function() {
            categorytCarusel.flickity("next", true);
        });
    } else {
        $(".slider-category-next").fadeOut();
        $(".slider-category-prev").fadeOut();
    }

    /*-----end category slider --------*/

    /*----- tabs address --------*/
    $(function() {
        let slide_pharmacy = $(".pharmacy-slider");
        let tab_length = $(".tab_category");

        //первую кнопку делаем активной
        tab_length.first().addClass("active");

        //первому слайду ставим класс active
        slide_pharmacy.first().addClass("active");

        slide_pharmacy.not(".active").hide();
    });

    $(".tab_btn").click(function() {
        let data_id = $(this)
            .data("id")
            .toString();
        let slide_pharmacy = $(".pharmacy-slider");

        $(".tab_btn").removeClass("active");
        $(this).addClass("active");

        slide_pharmacy.removeClass("active").hide();

        let constainer = $(".section")
            .find(".pharmacy-slider-" + data_id)
            .addClass("active")
            .show(0);
        constainer.find(".slider-list").flickity("resize");
    });
    /*----- tabs address --------*/

    /* modal aunif */
    //checkbox("checkbox"); // remmember me

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
    /* END modal aunif */

    /* form auntif event */
    var ajax_request;

    // Register button
    $(".btn-reg").on("click", function(e) {
        e.preventDefault();
        //var validator = new Validator($('#form-reg'));
        var result = validator_reg.validate();
        if (result !== false) {
            result.append("locale", locale);
            result.append(
                "_token",
                $('meta[name="csrf-token"]').attr("content")
            );
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
                        window.popup_show({
                            cls: "Notf_Good",
                            optional_title: svg_good,
                            optional_text: "Ваш заказ принят в обратботку"
                        });
                    } else {
                        if (response["type"] === "wrong") {
                            validator.general_error_show(
                                js_strings["error_wrong_credentials"]
                            );
                        } else if (response["type"] === "validation") {
                            console.log("error", response);
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
            result.append(
                "_token",
                $('meta[name="csrf-token"]').attr("content")
            );
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
                        //document.location = response['redirect'];
                    } else {
                        console.log("error", response);
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

    /* map */

    $.ajax({
        url: baseUrl + "/ajax/cities_shops",
        type: "POST",
        cache: false,
        data: {
            test: "",
            _token: $('meta[name="csrf-token"]').attr("content")
        },
        //data:   	//JSON.stringify({"test":"",'_token': $('meta[name="csrf-token"]').attr('content')}),
        //processData: false,
        //contentType: false,
        success: function(response) {
            if (response["status"] === "1") {
                //window.shops = response['shops'];
                let cities = response["cities"];
                console.log(cities);
                window.cities = {};
                window.shops = [];
                for (let i = 0; i < cities.length; i++) {
                    let buf = cities[i]["center"]
                        .replace(/\[|\]/g, "")
                        .split(",");
                    for (let j = 0; j < cities[i].shops.length; j++) {
                        let shop = cities[i].shops[j];
                        if (shop.coord !== null) {
                            let buf = shop.coord
                                .replace(/\[|\]/g, "")
                                .split(",");
                            console.log(buf);
                            window.shops.push({
                                coord: [parseFloat(buf[0]), parseFloat(buf[1])],
                                phone: shop.phone_1,
                                address: shop.address
                            });
                        }
                    }
                    //console.log(buf)
                    window.cities[cities[i].name] = [
                        parseFloat(buf[0]),
                        parseFloat(buf[1])
                    ];
                    //console.log(cities[i].);
                }
                console.log(window.cities);
                console.log(window.shops);
                //Дождёмся загрузки API и готовности DOM.
                ymaps.ready(init);
            } else {
                console.log("error", response);
            }
        },
        error: function(response) {
            console.log("error", response);
        }
    });

    // if($(window).width() < 1024){
    $("#map_hider").on("click.hider touch", function() {
        console.log("map fadeout");
        $(this).fadeOut(300);
    });
    // }

    /* END map */

    require("./includes/global_auth");
    require("./includes/global_subscribe");
    require("./includes/global_callback");
    require("./includes/global");
});

function init() {
    var shops = window.shops,
        destinations = window.cities;

    var myMap = new ymaps.Map("map", {
            // При инициализации карты обязательно нужно указать
            // её центр и коэффициент масштабирования.
            center: destinations["Донецк"],
            zoom: 10
        }),
        myCollection = new ymaps.GeoObjectCollection(
            {},
            {
                preset: "twirl#redIcon", //все метки красные
                draggable: false // и их можно перемещать
            }
        );

    // if($(window).width() < 1024){
    myMap.events.add("click", function(e) {
        console.log("map fadein");
        $("#map_hider").fadeIn(300);
    });

    // }

    for (var i = 0; i < shops.length; i++) {
        myCollection.add(
            new ymaps.Placemark(shops[i].coord, {
                hintContent: shops[i].address
                //balloonContent: 'А эта — новогодняя',
                //iconContent: '12'
            })
        );
    }

    myMap.geoObjects.add(myCollection);
    //.add(myPlacemarkWithContent)

    // куда скакать
    function clickGoto() {
        // город
        var pos = this.textContent;
        myMap.panTo(destinations[pos], {
            flying: 1
        });

        return false;
    }

    // навешиваем обработчики
    var col = document.getElementsByClassName("tab_category");
    for (var i = 0, n = col.length; i < n; ++i) {
        col[i].onclick = clickGoto;
    }
}
