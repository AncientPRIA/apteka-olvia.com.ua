require("../../views/blocks/menu/menu_bottom/search/search");
const Cart = require("./cart");
const { detect } = require("detect-browser");
const browser = detect();

const body = $("body");

let window_width = $(window).width();
window.scroll_off = scroll_off;
window.scroll_on = scroll_on;

/* cart */
Cart.openCart(".Basket", "get-produts-list", true, { position: true });
Cart.addCart(".btn_add_product", ".Product_Item_Fn", function() {
    // $(".btn_add_product").fadeOut("slow",function(){
    // 	$(".btn_add_product").fadeIn("slow");
    // })
});
Cart.closeCart(".Hider_Modal_Def, .Cart_Close", true, { position: true });
Cart.plusCart();
Cart.minusCard();
Cart.delItemCart();
Cart.cartCustom(function() {
    if (browser && browser.name == "ios") {
        let version = browser.version.split(".");
        if (parseInt(version) >= 12) {
            //alert(version)
            $(".modal_basket_top").css("margin-top", "36px");
        }
    }
});

body.on("click", ".btn_add_anim", function() {
    let _this = $(this);
    let parent = _this.parents(".product_item");
    let image = parent.find(".product_item_img").find("img");

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
    body.append($(flyerClone));

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
/* END cart */

// const bodyScrollLock = require('body-scroll-lock');
// const disableBodyScroll = bodyScrollLock.disableBodyScroll;
// const enableBodyScroll = bodyScrollLock.enableBodyScroll;

const scrollWidth = () => {
    let inner = document.createElement("p");
    inner.style.width = "100%";
    inner.style.height = "200px";

    let outer = document.createElement("div");
    outer.style.position = "absolute";
    outer.style.top = "0px";
    outer.style.left = "0px";
    outer.style.visibility = "hidden";
    outer.style.width = "200px";
    outer.style.height = "150px";
    outer.style.overflow = "hidden";
    outer.appendChild(inner);

    document.body.appendChild(outer);
    var w1 = inner.offsetWidth;
    outer.style.overflow = "scroll";
    var w2 = inner.offsetWidth;
    if (w1 == w2) w2 = outer.clientWidth;

    document.body.removeChild(outer);

    return w1 - w2;
};

const scroll_width = scrollWidth();

function scroll_off(element) {
    if (
        /safari/.test(navigator.userAgent.toLowerCase()) &&
        !/chrome/.test(navigator.userAgent.toLowerCase())
    ) {
        if (window_width < 996) {
            page_scroll_position =
                $("html").scrollTop() || $("body").scrollTop();
            $("body").css("position", "fixed");
        } else {
            $("body").css("padding-right", scroll_width + "px");
        }

        $("body").css("overflow", "hidden");
    } else {
        if (typeof element[0] !== "undefined") {
            //disableBodyScroll(element[0]);
        } else {
            // disableBodyScroll(element);
        }
    }
}

function scroll_on(element) {
    if (
        /safari/.test(navigator.userAgent.toLowerCase()) &&
        !/chrome/.test(navigator.userAgent.toLowerCase())
    ) {
        if (window_width < 996) {
            $("body").css("position", "relative");
            $("html, body").animate(
                {
                    scrollTop: page_scroll_position
                },
                0
            );
        } else {
            $("body").css("padding-right", "");
        }
        $("body").css("overflow", "");
    } else {
        //enableBodyScroll(element[0]);
    }
}
