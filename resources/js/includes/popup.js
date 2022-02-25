var fade_time = 300;

// Show Popup
// cls - class of popup. (Like .Popup.Type)
// optional_title - some popups can change title
// optional_text - some popups can change message
function popup_show(cls, optional_title, optional_text){
    var popup = $('.Popup.'+cls);
    var hider = $('.Hider.'+cls);

    if(typeof optional_title !== 'undefined'){
        popup.find('.Popup_Title').html(optional_title);
    }
    if(typeof optional_text !== 'undefined'){
        popup.find('.Popup_Text').html(optional_text);
    }

    hider.fadeIn(fade_time);
    popup.addClass('Visible');
}

// Close popup
function popup_close(cls){
    var popup = $('.Popup.'+cls);
    var hider = $('.Hider.'+cls);

    hider.fadeOut(fade_time);
    popup.removeClass('Visible');
}

// Set or remove type class on popup. Remove all classes except .Popup and _cls_. Add class of _type_
// type - class (no dot) of type. Example type - Success, Error.
function popup_set_type(cls, type) {
    var popup = $('.Popup.' + cls);
    if(type === ''){
        popup.attr('class', 'Popup '+ cls);
        return
    }

    var classList = popup.attr('class').split(/\s+/);
    $.each(classList, function(index, item) {
        if (item !== 'Popup' && item !== cls) {
            popup.removeClass(item);
        }
    });
    popup.addClass(type);
}