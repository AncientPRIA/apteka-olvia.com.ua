//require("../../menu_mobail/menu.js")
console.log('search loaded');

var ajax_request = null;

/*
$(".search-btn").on("click",function () {
	let val = this.val();
	console.log(val);
});
*/

// Smart search
let smart_search_class = 'smart-search';
let smart_search_item_class = 'smart-search-item';
let smart_search_highlight_class = 'smart-search-item-highlight';
let smart_search_item_selected_class = 'selected';
let input_search_class = 'input-search';


//let smart_search = $('.'+smart_search_class);
//let input_search = $('.'+input_search_class);
$('.'+input_search_class).on("input", function () {
    let input_search = $(this);
    let smart_search = input_search.parent().next();
	let search = $(this).val();

    smart_search.html('');

	if(search.length >= 3){
        smart_search.show();
        scroll_off(smart_search);
		if(ajax_request !== null){
			ajax_request.abort();
		}

        ajax_request = $.ajax({
            url:    	baseUrl + '/ajax/smart_search',
            type:		'POST',
            cache: 	    true,
            data:   	{"search": search, "_token":$('meta[name="csrf-token"]').attr('content')},
            //processData: false,
            //contentType: false,
            beforeSend: function() {

            },
            success: function(response) {
                if(response['status'] === '1'){
                    console.log(response['content']);
                    $.each(response['content'], function (index, value) {
                        smart_search.append('<li class="'+smart_search_item_class+'"><a href="">'+value['title']+'</a></li>')
                    });
                    $('.'+smart_search_item_class).each(function () {
                        color_text($(this), search)
                    })

                }else{
					console.log(response);
                }
            },
            error:function(response) {
                console.log("error",response);
            }
        });
	}
});

$('.'+input_search_class).keydown(function(e){
    console.log('keydown', e);

        if (e.keyCode == 40) { // Down
            let input_search = $('.'+input_search_class+':focus');
            if(input_search.length > 0){
                e.preventDefault();
                let selected = $('.'+smart_search_item_class+'.'+smart_search_item_selected_class);
                if(selected.length > 0){
                    selected.removeClass(smart_search_item_selected_class);
                    let next = selected.next();
                    if(next.length > 0){
                        next.addClass(smart_search_item_selected_class)
                    }else{
                        $('.'+smart_search_item_class).first().addClass(smart_search_item_selected_class);
                    }
                }else{
                    $('.'+smart_search_item_class).first().addClass(smart_search_item_selected_class);
                }
            }
        }

        if (e.keyCode == 38) { // Up
            let input_search = $('.'+input_search_class+':focus');
            if(input_search.length > 0){
                e.preventDefault();
                let selected = $('.'+smart_search_item_class+'.'+smart_search_item_selected_class);
                if(selected.length > 0){
                    selected.removeClass(smart_search_item_selected_class);
                    let next = selected.prev();
                    console.log('next', next);
                    if(next.length > 0){
                        next.addClass(smart_search_item_selected_class)
                    }else{
                        console.log('last', $('.'+smart_search_item_class).last());
                        $('.'+smart_search_item_class).last().addClass(smart_search_item_selected_class);
                    }
                }else{
                    $('.'+smart_search_item_class).last().addClass(smart_search_item_selected_class);
                }
            }

        }

        if (e.keyCode == 13) { // Enter
            let input_search = $('.'+input_search_class+':focus');
            if(input_search.length > 0){
                //let smart_search = input_search.parent().next();
                let selected = $('.'+smart_search_item_class+'.'+smart_search_item_selected_class);
                if(selected.length > 0){
                    e.preventDefault();
                    input_search.val(selected.text());
                    input_search.closest('form').submit();
                    //smart_search.html('');
                }
            }

        }
});

// $('body').on('click', '.'+smart_search_item_class, function () {
//     console.log('click', $(this).text());
//     let smart_search = $(this).parent();
//     let input_search = smart_search.prev().children('.'+input_search_class);
//     input_search.val($(this).text());
//     input_search.closest('form').submit();
//     console.log(input_search.siblings('form'));
//     //smart_search.html('');
// });

$('.'+input_search_class)
    .on('focusout', function () {
        let smart_search = $(this).parent().next();
        setTimeout(function () {
            smart_search.hide();
            scroll_on(smart_search);
        }, 300)
    })
    .on('focusin', function () {
        let smart_search = $(this).parent().next();
        smart_search.show();
        scroll_off(smart_search);
    });


// Highlight
/*
function color_word(text_id, word, color) {
    words = $('#' + text_id).text().split(' ');
    words = words.map(function(item) { return item == word ? "<span style='color: " + color + "'>" + word + '</span>' : item });
    new_words = words.join(' ');
    $('#' + text_id).html(new_words);
}
*/
function color_text(jq_element, text) {
    console.log(jq_element, text);
    var element_html = jq_element.html();
    var rgxp = new RegExp(text, 'gi');
    var repl = '<span class="'+smart_search_highlight_class+'">' + text + '</span>';
    element_html = element_html.replace(rgxp, repl);
    console.log(element_html);
    jq_element.html(element_html);
}