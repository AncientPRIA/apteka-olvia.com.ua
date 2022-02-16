
// module.exports = {
//     global_modal_polit: function global_modal_polit() {

        // set_cookie("show_modal", false, 24);

        var popup_footer_text = js_strings.field_modal;
        var show_modal = getCookie("show_modal");

        if (show_modal != 'true' ) {

            //setTimeout(popup_show, 5000, "Popup_footer", undefined, popup_footer_text, undefined);

            // setTimeout(function(){
            //     popup_show({cls:"Popup_footer",optional_text:popup_footer_text})
            // },15000);

        };

        //Кнопка "Принять"
        $(".btn_ok").click(function () {
            set_cookie("show_modal", true, 24);
            popup_close({cls:"Popup_footer",scrollOff: "body"});
        });

        //кнопка "Отклонить"
        $("body").on("click",".btn_cancel",function(e){
            popup_close({cls:"Popup_footer"});
        });

        //кнопка "Узнать больше"
        $("body").on("click",".btn-more",function(e){

            popup_close({cls:"Popup_footer"});

            $(".body").css("overflow", "hidden")

            $(".Popup_c > .Popup_Text").children().remove();

            ajax_request(17);

        });

        $("body").on("click",".btn_ok",function(e){

            popup_close({cls:"Popup_c"});
            $(".body").css("overflow", "");

            set_cookie("show_modal", true, 24);


        });

        //закрыть модалку
        $("body").on("click", ".Button_Close", function () {

            popup_close({cls: "Popup_c"});

            if (show_modal != 'true' ) {
                popup_show({cls: "Popup_footer", optional_text: popup_footer_text});
            }

            $(".body").css("overflow", "")
        });

//модалка для политики
// $(".Resume_Agree_Item_Name").click(function () {
//     $("body").css("overflow", "hidden");
//
//     $(".Popup_c > .Popup_Text").children().remove();
//
//     ajax_request(13);
// });
// $("body").on("click", ".Resume_Agree_Item_Name", function (e) {
//     e.preventDefault();
//     $("body").css("overflow", "hidden");
//
//     $(".Popup_c > .Popup_Text").children().remove();
//
//     ajax_request(13);
// });

        $("body").on("click", ".btn_Policy", function (e) {
            e.preventDefault();
           // $(".body").css("overflow", "hidden");

            $(".Popup_c > .Popup_Text").children().remove();

            ajax_request(15);
        });

        $("body").on("click", ".btn_Term", function (e) {
            e.preventDefault();
            $(".body").css("overflow", "hidden");

            $(".Popup_c > .Popup_Text").children().remove();

            ajax_request(16);
        });

        function ajax_request(id) {
            $.ajax({
                url: baseUrl + "/polit",
                type: 'POST',
                cache: false,
                data: {
                    'polit_id': id,
                    'locale': locale,
                    "_token": $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function beforeSend() {},
                success: function success(response) {
                    if (response['status'] === '1') {
                        popup_show({cls:"Popup_c", optional_title:response['title'], optional_text:response['content'],scrollOff:"body"});
                        console.log(response['content']);
                    } else {
                        console.log('ERROR!', response);
                    }
                },
                error: function error(response) {
                    console.log("error", response);
                }
            });
        };

        function getCookie(name) {

            var matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ))
            return matches ? decodeURIComponent(matches[1]) : undefined
        }

        function set_cookie(cookie_name, cookie_val, expires_hours){
            if(typeof expires_hours === 'undefined'){
                expires_hours = -1;
            }
            var expires;
            var path;
            path = '; path=/';
            if(expires_hours !== -1){
                var d = new Date();
                d.setTime(d.getTime() + (expires_hours * 60 * 60 * 1000));
                expires = "; expires="+d.toUTCString();
            }else{
                expires = ";";
            }
            document.cookie = cookie_name+"="+cookie_val+path+expires;
        }
    // }

// }

