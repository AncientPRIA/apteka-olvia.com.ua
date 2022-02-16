import Validator from "./includes/validator";
import btnShare from "./includes/shareWebApi";
var jQueryBridget = require("jquery-bridget");
var Flickity = require("flickity");
var PhotoSwipe = require("photoswipe");
var PhotoSwipeUI_Default = require("photoswipe/dist/photoswipe-ui-default");
var $ = require("jquery");
require("../views/blocks/popup/popup");
require("./libs/jq_input_mas");
require("./includes/form.js");
import(/*webpackChunkName: 'chunck/photoswipe' */ "photoswipe");

var Picture = require("./includes/picture_functions");

var is_loading = false;
var ajax_request;
$(document).ready(function() {
    Picture.lazy_load_launch();
    Picture.background_is_picture_launch();

    btnShare({
        title: "СЕТЬ АПТЕК ОЛЬВИЯ",
        text:
            "Низкие цены, специальные акции и дисконтная система делают наши аптеки доступными абсолютно для каждого!"
    });

    /*----- flickity wabpack init-----*/
    Flickity.setJQuery($);
    jQueryBridget("flickity", Flickity, $);
    /* ------------------------------*/

    /*----- slider --------*/
    var productCarusel = $(".cards_list_product_rel"),
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
        };

    var productCarusel2 = $(".cards_list_product_analog");

    var image_Carusel = $(".slider_product_img"),
        image_flick_options = {
            freeScroll: false,
            freeScrollFriction: 0.1,
            selectedAttraction: 0.02,
            prevNextButtons: false,
            cellAlign: "left",
            contain: false,
            touchVerticalScroll: true,
            pageDots: false,
            autoPlay: false,
            lazyLoad: true
            // pauseAutoPlayOnHover: true
        };
    /*----- END slider --------*/

    /*-----product slider --------*/
    image_Carusel.flickity(image_flick_options);
    image_Carusel.flickity("resize");

    $(".slider-image-prev").on("click", function() {
        image_Carusel.flickity("previous", true);
    });

    $(".slider-image-next").on("click", function() {
        image_Carusel.flickity("next", true);
    });

    if ($(".image_item").lenght > 1) {
        $(".slider-image-prev").fadeIn("slow");
        $(".slider-image-next").fadeIn("slow");
    }
    /*-----end product slider --------*/

    /*-----product slider --------*/
    productCarusel.flickity(flick_options);
    productCarusel.flickity("resize");

    productCarusel2.flickity(flick_options);
    productCarusel2.flickity("resize");

    if (window.innerWidth < 996) {
        let fixBtn = $(".single-nav-fixed");
        $(window).scroll(function() {
            if ($(this).scrollTop() > 56) {
                fixBtn.addClass("active");
            } else {
                fixBtn.removeClass("active");
            }
        });

        $(".btn-back").on("click", function() {
            window.history.back();
        });

        $(".btn_product_share").on("click", function() {
            let self = $(this);

            if (!self.hasClass("active")) {
                self.addClass("active");
                $(".modal-share").addClass("active");
            } else {
                self.removeClass("active");
                $(".modal-share").removeClass("active");
            }
        });

        /* mobail slider adress */
        let cityItems = $(".city-items"),
            cityItemActive = 0;
        cityItems.eq(0).addClass("active");
        for (let i = 0, length = cityItems.length; i < length; i++) {
            if (cityItems.eq(i).children(".product_address_item").length > 1) {
                cityItems.eq(i).flickity(flick_options);
                productCarusel.flickity("resize");
            }
        }

        $("#select_city_mobail").on("change", function() {
            let self = $(this);
            console.log(self.val());
            $(".city-items.active").fadeOut("slow", function() {
                $(".city-items.active").removeClass("active");
                $(`.city-items[data-city="${self.val()}"]`).addClass("active");
                $(`.city-items[data-city="${self.val()}"]`).fadeIn("slow");
                cityItemActive = parseInt(self.val()) - 1;
            });
        });

        $(".slider-city-prev").on("click", function() {
            console.log("c");
            if (
                $(
                    `.city-items[data-city="${cityItemActive +
                        1}"] .product_address_item`
                ).length > 1
            ) {
                console.log("p");
                cityItems.eq(cityItemActive).flickity("previous", true);
            }
        });

        $(".slider-city-next").on("click", function() {
            if (
                $(
                    `.city-items[data-city="${cityItemActive +
                        1}"] .product_address_item`
                ).length > 1
            ) {
                console.log("P1");
                cityItems.eq(cityItemActive).flickity("next", true);
            }
        });

        /* END mobail slider adress */
    }

    $(".slider-product-prev-1").on("click", function() {
        productCarusel2.flickity("previous", true);
    });

    $(".slider-product-next-1").on("click", function() {
        productCarusel2.flickity("next", true);
    });

    $(".slider-product-prev-2").on("click", function() {
        productCarusel.flickity("previous", true);
    });

    $(".slider-product-next-2").on("click", function() {
        productCarusel.flickity("next", true);
    });

    /*-----end product slider --------*/

    /*  header */
    require("../views/blocks/menu/menu_bottom/menu-cat/menu_big");
    require("../views/includes/sections/header/header");
    require("../views/blocks/menu/menu_top/btn/menu-btn");
    /*END header*/

    /* validator */
    var validator_review = new Validator($("#product_item_review"));
    var validator_Call_Back_Pharma = new Validator($("#form-call-back-pharm"));
    validator_review.watch();
    validator_Call_Back_Pharma.watch();
    /* END validator */

    require("./includes/global_auth");
    require("./includes/global_subscribe");
    require("./includes/global_callback");
    require("./includes/global");

    /*PhotoSwipe*/

    image_Carusel.on("staticClick.flickity", function(
        event,
        pointer,
        cellElement,
        cellIndex
    ) {
        if (!cellElement) {
            return;
        }

        // Photoswipe functions
        var openPhotoSwipe = function() {
            var pswpElement = document.querySelectorAll(".pswp")[0];

            // build items array

            var items = $.map($(".slider_product_img").find("img"), function(
                el
            ) {
                return {
                    src: el.getAttribute("data-fullscreen-src"),
                    w: el.getAttribute("data-fullscreen-width"),
                    h: el.getAttribute("data-fullscreen-height")
                };
            });

            var options = {
                history: false,
                shareEl: false,
                index: cellIndex
            };

            var gallery = new PhotoSwipe(
                pswpElement,
                PhotoSwipeUI_Default,
                items,
                options
            );
            gallery.init();
        };

        openPhotoSwipe();
    });

    $(".btn-body-view").on("click", function() {
        let self = $(this);
        if (!self.hasClass("active")) {
            self.addClass("active");

            self.text("Свернуть");
            $(".mobail-single .product_description").addClass(
                "product_description_height"
            );
        } else {
            self.removeClass("active");
            self.text("Развернуть");

            let destination = $(".product_description").offset().top;
            $("html, body").scrollTop(destination);
            //destination.scrollTop(0);
            // $('html, body').animate({ scrollTop: destination }, 500);

            $(".mobail-single .product_description").removeClass(
                "product_description_height"
            );
        }
    });
    // var initPhotoSwipeFromDOM = function(gallerySelector) {
    //
    //     // parse slide data (url, title, size ...) from DOM elements
    //     // (children of gallerySelector)
    //     var parseThumbnailElements = function(el) {
    //         var thumbElements = el.childNodes,
    //             numNodes = thumbElements.length,
    //             items = [],
    //             figureEl,
    //             linkEl,
    //             size,
    //             item;
    //
    //         for(var i = 0; i < numNodes; i++) {
    //
    //             figureEl = thumbElements[i]; // <figure> element
    //
    //             // include only element nodes
    //             if(figureEl.nodeType !== 1) {
    //                 continue;
    //             }
    //
    //             linkEl = figureEl.children[0]; // <a> element
    //
    //             size = linkEl.getAttribute('data-size').split('x');
    //
    //             // create slide object
    //             item = {
    //                 src: linkEl.getAttribute('href'),
    //                 w: parseInt(size[0], 10),
    //                 h: parseInt(size[1], 10)
    //             };
    //
    //
    //
    //             if(figureEl.children.length > 1) {
    //                 // <figcaption> content
    //                 item.title = figureEl.children[1].innerHTML;
    //             }
    //
    //             if(linkEl.children.length > 0) {
    //                 // <img> thumbnail element, retrieving thumbnail url
    //                 item.msrc = linkEl.children[0].getAttribute('src');
    //             }
    //
    //             item.el = figureEl; // save link to element for getThumbBoundsFn
    //             items.push(item);
    //         }
    //
    //         return items;
    //     };
    //
    //     // find nearest parent element
    //     var closest = function closest(el, fn) {
    //         return el && ( fn(el) ? el : closest(el.parentNode, fn) );
    //     };
    //
    //     // triggers when user clicks on thumbnail
    //     var onThumbnailsClick = function(e) {
    //         e = e || window.event;
    //         e.preventDefault ? e.preventDefault() : e.returnValue = false;
    //
    //         var eTarget = e.target || e.srcElement;
    //
    //         // find root element of slide
    //         var clickedListItem = closest(eTarget, function(el) {
    //             return (el.tagName && el.tagName.toUpperCase() === 'FIGURE');
    //         });
    //
    //         if(!clickedListItem) {
    //             return;
    //         }
    //
    //         // find index of clicked item by looping through all child nodes
    //         // alternatively, you may define index via data- attribute
    //         var clickedGallery = clickedListItem.parentNode,
    //             childNodes = clickedListItem.parentNode.childNodes,
    //             numChildNodes = childNodes.length,
    //             nodeIndex = 0,
    //             index;
    //
    //         for (var i = 0; i < numChildNodes; i++) {
    //             if(childNodes[i].nodeType !== 1) {
    //                 continue;
    //             }
    //
    //             if(childNodes[i] === clickedListItem) {
    //                 index = nodeIndex;
    //                 break;
    //             }
    //             nodeIndex++;
    //         }
    //
    //
    //
    //         if(index >= 0) {
    //             // open PhotoSwipe if valid index found
    //             openPhotoSwipe( index, clickedGallery );
    //
    //         }
    //         return false;
    //     };
    //
    //     // parse picture index and gallery index from URL (#&pid=1&gid=2)
    //     var photoswipeParseHash = function() {
    //         var hash = window.location.hash.substring(1),
    //             params = {};
    //
    //         if(hash.length < 5) {
    //             return params;
    //         }
    //
    //         var vars = hash.split('&');
    //         for (var i = 0; i < vars.length; i++) {
    //             if(!vars[i]) {
    //                 continue;
    //             }
    //             var pair = vars[i].split('=');
    //             if(pair.length < 2) {
    //                 continue;
    //             }
    //             params[pair[0]] = pair[1];
    //         }
    //
    //         if(params.gid) {
    //             params.gid = parseInt(params.gid, 10);
    //         }
    //
    //         return params;
    //     };
    //
    //     var openPhotoSwipe = function(index, galleryElement, disableAnimation, fromURL) {
    //         var pswpElement = document.querySelectorAll('.pswp')[0],
    //             gallery,
    //             options,
    //             items;
    //
    //         items = parseThumbnailElements(galleryElement);
    //
    //         // define options (if needed)
    //         options = {
    //
    //             // define gallery index (for URL)
    //             galleryUID: galleryElement.getAttribute('data-pswp-uid'),
    //
    //             getThumbBoundsFn: function(index) {
    //                 // See Options -> getThumbBoundsFn section of documentation for more info
    //                 var thumbnail = items[index].el.getElementsByTagName('img')[0], // find thumbnail
    //                     pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
    //                     rect = thumbnail.getBoundingClientRect();
    //
    //                 return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
    //             }
    //
    //         };
    //
    //         // PhotoSwipe opened from URL
    //         if(fromURL) {
    //             if(options.galleryPIDs) {
    //                 // parse real index when custom PIDs are used
    //                 // http://photoswipe.com/documentation/faq.html#custom-pid-in-url
    //                 for(var j = 0; j < items.length; j++) {
    //                     if(items[j].pid == index) {
    //                         options.index = j;
    //                         break;
    //                     }
    //                 }
    //             } else {
    //                 // in URL indexes start from 1
    //                 options.index = parseInt(index, 10) - 1;
    //             }
    //         } else {
    //             options.index = parseInt(index, 10);
    //         }
    //
    //         // exit if index not found
    //         if( isNaN(options.index) ) {
    //             return;
    //         }
    //
    //         if(disableAnimation) {
    //             options.showAnimationDuration = 0;
    //         }
    //
    //         // Pass data to PhotoSwipe and initialize it
    //         gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
    //         gallery.init();
    //     };
    //
    //     // loop through all gallery elements and bind events
    //     var galleryElements = document.querySelectorAll( gallerySelector );
    //
    //     for(var i = 0, l = galleryElements.length; i < l; i++) {
    //         galleryElements[i].setAttribute('data-pswp-uid', i+1);
    //         galleryElements[i].onclick = onThumbnailsClick;
    //     }
    //
    //     // Parse URL and open gallery if it contains #&pid=3&gid=1
    //     var hashData = photoswipeParseHash();
    //     if(hashData.pid && hashData.gid) {
    //         openPhotoSwipe( hashData.pid ,  galleryElements[ hashData.gid - 1 ], true, true );
    //     }
    // };
    //
    // // execute above function
    // initPhotoSwipeFromDOM('.slider_product_img');

    /*END PhotoSwipe*/

    /* functions for animation with added items */
    $("body").on("click", ".btn_add_anim_single", function() {
        console.log("click to btn add");

        let parent = $(".slider_product_img ");

        const image = $(".product_img").find(".flickity-lazyloaded");

        let flyingTo = $(".basket-flying-to");

        flyingTo.each(function() {
            if ($(this).offset().top > 0 || $(this).offset().left > 0) {
                console.log($(this).offset());
                flyingTo = $(this);
            }
        });

        flyToElement(image, flyingTo, parent);
    });

    function flyToElement(flyer, flyingTo, parent) {
        var $func = $(this);
        var divider = 3;
        var flyerClone = $(flyer).clone();
        $(flyerClone).css({
            position: "absolute",
            top: parent.offset().top + "px",
            left: parent.offset().left + "px",
            width: 100 + "px",
            height: 150 + "px",
            display: "block",
            opacity: 1,
            "z-index": 1000
        });
        $("body").append($(flyerClone));

        var gotoX = flyingTo.offset().left + 10;
        var gotoY = flyingTo.offset().top + 5;

        console.log("gotoX", gotoX);
        console.log("gotoY", gotoY);

        $(flyerClone).animate(
            {
                opacity: 0.4,
                left: gotoX + "px",
                top: gotoY + "px",
                width: 50 / divider,
                height: 75 / divider,
                borderRadius: "50%"
            },
            700,
            function() {
                $(flyingTo).fadeOut("fast", function() {
                    $(flyingTo).fadeIn("fast", function() {
                        $(flyerClone).fadeOut("fast", function() {
                            $(flyerClone).remove();
                        });
                    });
                });
            }
        );
    }

    /*----- rating --------*/
    var ajax_request;
    var ajax_allowed = true;
    $(".user_star_list").on("click", ".product_user_star_icon", function() {
        if (!ajax_allowed) {
            return;
        }
        ajax_allowed = false;

        var rating = $(this).data("rating"); // active
        ajax_request = $.ajax({
            url: baseUrl + "/ajax/product_rate",
            type: "POST",
            cache: false,
            data: {
                user_id: user_id,
                product_id: product_id,
                rating: rating,
                _token: $('meta[name="csrf-token"]').attr("content")
            },
            //processData: false,
            //contentType: false,
            beforeSend: function() {
                setTimeout(function() {
                    ajax_allowed = true;
                }, 1000);
            },
            success: function(response) {
                if (response["status"] === "1") {
                    console.log("OK");
                    cStars(rating - 1);
                    // меняем количество по клику
                    starsCount = rating; //$('.star_btn.active').length;
                } else {
                    if (response["type"] === "wrong_user") {
                        console.log("ERROR: user not equal");
                    } else {
                        console.log(response);
                    }
                }
            },
            error: function(response) {
                console.log("error", response);
            }
        });
    });
    /*----- rating END --------*/
    /*star rating*/

    var cStars = function(nowPos) {
        // У всех убираем active
        $(".star_btn").removeClass("active");

        for (var i = 0; nowPos + 1 > i; i++) {
            $(".star_btn")
                .eq(i)
                .toggleClass("active");
        }
    };
    // переменная содержит количество активных звезд
    var starsCount = $(".star_btn.active").length;

    // При наведении
    $(".star_btn").hover(function() {
        cStars($(this).index());
    });

    // При клике
    /*
        $('.star_btn').click(function() {
            cStars($(this).index());
            // меняем количество по клику
            starsCount = $('.star_btn.active').length;
        });
        */

    // Как только отводим мышку, возвращаем количество активных айтемов, которые были изначально
    $(".star_btn").on("mouseleave", function() {
        cStars(+starsCount - 1);
    });
    /*END star rating*/

    slider_icons_phone(
        5000,
        ".Contact_Info_Icon_Slider",
        ".Contact_Info_Icon_Slider_Item"
    );

    //address
    var currentIdCity;
    $("#select_city")
        .change(function() {
            $("select option:selected").each(function() {
                currentIdCity = $(this).val();
            });
            console.log(currentIdCity);

            $(".product_address_item").addClass("hidden");
            $(".product_address_item").removeClass("active");

            $('.product_address_item[data-cityid="' + currentIdCity + '"]')
                .removeClass("hidden")
                .addClass("active");

            // if(currentIdCity != $(".product_address_item").data("cityid")){
            //     $(".product_address_item").addClass("hidden")
            //     $(".product_address_item").removeClass("active")
            //
            // }else{
            //     $(".product_address_item").addClass("active")
            //     $(".product_address_item").removeClass("hidden")
            // }
        })
        .trigger("change");

    //popup
    $(".check_availability").click(function() {
        let id_shop = $(this).data("idshop");
        let id_product = $(this).data("idproduct");

        popup_show({ cls: "Call_Back_Pharma" });

        $("input[name=id_shop]").val(id_shop);
        $("input[name=id_product]").val(id_product);
    });

    // Check availability submit
    $("body").on("click", "#form-call-back-pharm .btn-submit", function(e) {
        e.preventDefault();
        //var validator = new Validator($('#form-reg'));
        var result = validator_Call_Back_Pharma.validate();
        if (result !== false) {
            result.append("locale", locale);
            result.append(
                "_token",
                $('meta[name="csrf-token"]').attr("content")
            );
            console.log(baseUrl);
            ajax_request = $.ajax({
                url: baseUrl + "/ajax/check_availability",
                type: "POST",
                cache: false,
                data: result,
                processData: false,
                contentType: false,
                beforeSend: function() {},
                success: function(response) {
                    if (response["status"] === "1") {
                        console.log("OK");
                        window.popup_close({ cls: "Call_Back_Pharma" });
                        window.popup_set_type("Notification", "Notf_Good");
                        window.popup_show({
                            cls: "Notification",
                            optional_text:
                                js_strings["success_check_availability"]
                        });
                    } else {
                        if (response["type"] === "not_found") {
                            window.popup_set_type("Notification", "Notf_Bad");
                            window.popup_show({
                                cls: "Notification",
                                optional_text: js_strings["error_not_found"]
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

    // Review submit
    $("body").on("click", "#product_item_review .btn-submit", function(e) {
        e.preventDefault();
        var result = validator_review.validate();
        if (result !== false) {
            result.append("locale", locale);
            result.append(
                "_token",
                $('meta[name="csrf-token"]').attr("content")
            );
            console.log(baseUrl);
            ajax_request = $.ajax({
                url: baseUrl + "/ajax/review_submit",
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
                            optional_text: js_strings["success_review_submit"],
                            callbacks: {
                                after_close: function() {
                                    document.location.reload();
                                }
                            }
                        });
                    } else {
                        if (response["type"] === "not_found") {
                            window.popup_set_type("Notification", "Notf_Bad");
                            window.popup_show({
                                cls: "Notification",
                                optional_text: js_strings["error_not_found"]
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

    // Review Load more
    $(".review_load_more").on("click", function() {
        console.log("review_load_more");
        const load_svg = $(this).children(".review_load_more_svg");
        const load_active_class = "Load_More_Active";
        const url = window.baseUrl + "/ajax/reviews/load_more";
        const items_container = $(".review_list");

        if (is_loading === true) {
            load_svg.removeClass(load_active_class);
            if (ajaxed_request !== null) {
                ajaxed_request.abort();
            }
        }
        is_loading = true;

        ajaxed_request = $.ajax({
            url: url,
            type: "POST",
            cache: false,
            data: {
                page: reviews_page,
                product_id: product_id,
                _token: $('meta[name="csrf-token"]').attr("content")
            },
            beforeSend: function() {
                load_svg.addClass(load_active_class);
            },
            success: function(response) {
                if (response["status"] === "1") {
                    if (response["content"].length > 0) {
                        items_container.append(response["content"]);
                        reviews_page++;
                        Picture.lazy_load_launch();
                        Picture.background_is_picture_launch();
                    } else {
                        //$('.Vacancies_Container').append(`<div style="text-align: center">That's all!</div>`);
                        // Nothing more
                    }
                    load_svg.removeClass(load_active_class);
                    is_loading = false;
                } else {
                    console.log("ERROR!", response);
                    load_svg.removeClass(load_active_class);
                    is_loading = false;
                }
            },
            error: function(response) {
                console.log("error", response);
                load_svg.removeClass(load_active_class);
                is_loading = false;
            }
        });
    });

    //select
    // $(".select_arrow").click(function () {
    //     showDropdown($('.product_city_select')[0]);
    //     // $(".product_city_select")[0].click()
    // })
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
