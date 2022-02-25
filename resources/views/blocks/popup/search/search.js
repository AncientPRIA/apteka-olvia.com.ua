import gsap from "gsap";
const Flickity = require("flickity");
const jQueryBridget = require("jquery-bridget");

/*----- flickity wabpack init-----*/
Flickity.setJQuery( $ );
jQueryBridget( "flickity", Flickity, $ );
/* ------------------------------*/

// Для демонстрации верстки
$.fn.duplicate = function(count, cloneEvents) {
    var tmp = [];
    for ( var i = 0; i < count; i++ ) {
        $.merge( tmp, this.clone( cloneEvents ).get() );
    }
    return this.pushStack( tmp );
};

$('.search-result-category__more').on('click', function(){
    let _this = $(this),
        parent_data = _this.parents('.search__col').clone(),
        btns_data = $('.search__btns').removeClass('d-none').clone()

    $('.search__search-result-category-row *').remove()
    parent_data.find('.search-result-category__more').remove()
    parent_data.appendTo('.search__search-result-category-row');
    if($(window).width() > 1260){
        parent_data.duplicate(2).appendTo('.search__search-result-category-row');
        $('.search__col:not(:first-child)').find('.search-result-category__head *').remove()
    }else{
        if($(window).width() > 991){
            parent_data.duplicate(1).appendTo('.search__search-result-category-row');
            $('.search__col:not(:first-child)').find('.search-result-category__head *').remove()
        }
    }

    $.when( $('.search__col:last-child').find('.search-result-category__item:last-child').replaceWith(btns_data)).then(function() {
        let counter = 2,
            btnPrev = $('.search__btn-prev'),
            btnNext = $('.search__btn-next')
        btnPrev.removeClass('search__btn-prev_hidden')
        btnNext.find('.search__btn-next-count-wrap').removeClass('d-none')
        btnNext.find('.search__btn-next-count').text(String(counter))
        btnNext.on('click', function(){
            counter++;
            if(btnPrev.hasClass('search__btn-prev_hidden')){
                btnPrev.removeClass('search__btn-prev_hidden')
            }
            if(counter > 1){
                btnNext.find('.search__btn-next-count-wrap').removeClass('d-none')
            }
            btnNext.find('.search__btn-next-count').text(String(counter))
        })
        btnPrev.on('click', function(){
            let _this = $(this)
            if(counter > 1){
                counter--
                btnNext.find('.search__btn-next-count').text(String(counter))
            }
            if(counter === 1){
                _this.addClass('search__btn-prev_hidden')
                btnNext.find('.search__btn-next-count-wrap').addClass('d-none')
            }

        })
    })
})
//
