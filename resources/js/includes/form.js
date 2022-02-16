// Include outside of document ready
$( document ).ready(function() {

    // Fix for disabling autocomplete
    $(".autofill-disable").attr("readonly", false);

    // Label lift (active)
    $(".input").focusin(function() {
        var parent = $(this).parent();
        parent.children(".label").addClass("label_active");
        //autocomplete_check();
    });

    // Label lift (inactive)
    $(".input").focusout(function() {
        var parent = $(this).parent();

        if($(this).val().length <= 0){
            parent.children(".label").removeClass("label_active");
        }
    });

    // Focus input, when clicked on label
    $(".label").on("click", function () {
        $(this).siblings("input").focus();
    });

    //inputmask
    $("input[name='birth_date']").inputmask("datetime", {
        inputFormat: "dd | mm | yyyy",
        // mask: "99/99/9999",
        // "mask": "m \\months",
        // alias: "mm/dd/yyyy",
        separator: " | ",
        // "autoUnmask" : true,
        "clearIncomplete": false,
        "showMaskOnHover": false
    });

    $("input[name='phone']").inputmask({
        "mask": "+99 (999) 999-99-99",
        "clearIncomplete": true,
        "showMaskOnHover": false
    });
    // $("input[name='birth_date']").inputmask();
    //END inputmask

    //autocomplete_check();
});


// Not needed. Use autofill-disable class
function autocomplete_check(){
    var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
    var autofill = isChrome === true ? $("input:-webkit-autofill").length : 0;

    var inputs = $(".input");
    for(var i = 0; i < inputs.length;i++){
        if(inputs.eq(i).val().length > 0 || autofill > 0){
            var parent = inputs.eq(i).parent();
            parent.children(".label").addClass("label_active");
        }

    }
}