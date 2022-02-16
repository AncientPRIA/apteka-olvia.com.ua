const fade_time = 300;
const page_scroll_position = 0;
const body = $("body");

window.popup_show = popup_show;
window.popup_close = popup_close;
window.popup_set_type = popup_set_type;

// Show Popup
// cls - class of popup. (Like .Popup.Type)
// optional_title - some popups can change title
// optional_text - some popups can change message
// function popup_show(cls, optional_title, optional_text, callbacks){
//     const popup = $('.Popup.'+cls);
//     const hider = $('.Hider.'+cls);
//
//     if(typeof optional_title !== 'undefined'){
//         popup.find('.Popup_Title').html(optional_title);
//     }
//     if(typeof optional_text !== 'undefined'){
//         popup.find('.Popup_Text').html(optional_text);
//     }
//
//     if(typeof callbacks === 'object'){
//         if(typeof callbacks.after_close === 'function'){
//             popup.data({after_close: callbacks.after_close});
//         }
//     }
//
//     hider.fadeIn(fade_time);
//     popup.addClass('Visible');
//    // body.css("overflow","hidden");
// }

function popup_show(popup_setting) {
    let {
        cls = ".Autf",
        optional_title = undefined,
        optional_text = undefined,
        scrollOff = undefined,
        callbacks = undefined
    } = popup_setting || {};

    const popup = $(".Popup." + cls);
    const hider = $(".Hider." + cls);

    if (cls !== "Popup_footer") {
        body.css({ overflow: "hidden" });
    }

    if (typeof optional_title !== "undefined") {
        popup.find(".Popup_Title").html(optional_title);
    }
    if (typeof optional_text !== "undefined") {
        popup.find(".Popup_Text").html(optional_text);
    }

    if (scrollOff !== undefined) {
        popup.data("scroll-off", "true");
        scroll_off(popup);

        //document.addEventListener('touchmove', scroll_off, { passive: false });
        //popup[0].addEventListener('touchmove', scroll_off_boundary, { passive: false });

        //document.addEventListener('scroll', function(event){console.log('Body scroll'); event.preventDefault(); event.stopPropagation(); event.returnValue = false; return false;});

        //document.addEventListener('touchmove', function(e){console.log('111'); e.preventDefault()}, { passive: false });
        //document.getElementById('inner-scroll').addEventListener('touchmove', function(e){console.log('222'); e.stopPropagation()}, false);

        //$('body').css('pointer-events', 'none');
        //popup.css('pointer-events', 'none');
        //popup.children('.Popup_Content').css('pointer-events', 'auto');

        /*
		$('body').delegate('#inner-scroll','touchmove',function(e){

			e.preventDefault();

		}).delegate('.scroll','touchmove',function(e){

			e.stopPropagation();

		});
		*/

        /*
				if($(window).width() <= '995'){
					$(`${scrollOff}`).css("position","fixed");
				}

				$(`${scrollOff}`).css("overflow","hidden");
		*/
    }

    // !!! уточнить !!!
    if (typeof callbacks === "object") {
        if (typeof callbacks.after_close === "function") {
            popup.data({ after_close: callbacks.after_close });
        }
    }

    // if(callbacks !== undefined){
    //     callbacks();
    // }

    hider.fadeIn(fade_time);
    popup.addClass("Visible");
    //

    /*
	$('document')
		.on('touchstart', function (e) {
			console.log('touchstart');

		})
		.on('touchend', function (e) {
			console.log('touchend');

		})
		.on('touchmove', function (e) {
			console.log('touchmove');
			console.log(e);
			e.preventDefault();
			e.stopPropagation();
			return false;
			//const target = $(e.currentTarget);

			console.log(e.currentTarget.tagName);
			if(e.currentTarget.tagName == 'BODY'){
				console.log('Prevent');
				e.preventDefault();
				e.stopPropagation();
			}


		});
	*/
}

// Close popup
function popup_close(popup_setting) {
    body.css({ overflow: "visible" });

    let { cls = ".Autf", scrollOff = undefined, callbacks = undefined } =
        popup_setting || {};

    const popup = $(".Popup." + cls);
    const hider = $(".Hider." + cls);

    let after_close = popup.data("after_close");

    hider.fadeOut(fade_time);
    popup.removeClass("Visible");

    /*
	if(scrollOff !== undefined){

		if($(window).width() <= '995') {
			$(`${scrollOff}`).css("position", "");
		}
		$(`${scrollOff}`).css("overflow","");

	}
	*/

    if (popup.data("scroll-off") === "true") {
        scroll_on(popup);
        popup.data("scroll-off", "");
        //console.log('enabling scroll');
        //enableBodyScroll(popup[0]);

        //$('body').css('pointer-events', '');
        //popup.css('pointer-events', '');
        //popup.children('.Popup_Content').css('pointer-events', '');

        //document.removeEventListener('touchmove', scroll_off);
    }

    if (callbacks !== undefined) {
        callbacks();
    }

    if (typeof after_close === "function") {
        after_close();
        popup.data("after_close", "");
    }
}

// Set or remove type class on popup. Remove all classes except .Popup and _cls_. Add class of _type_
// type - class (no dot) of type. Example type - Success, Error.
function popup_set_type(cls, type) {
    const popup = $(".Popup." + cls);
    if (type === "") {
        popup.attr("class", "Popup " + cls);
        return;
    }

    const classList = popup.attr("class").split(/\s+/);
    $.each(classList, function(index, item) {
        if (item !== "Popup" && item !== cls) {
            popup.removeClass(item);
        }
    });
    popup.addClass(type);
}
