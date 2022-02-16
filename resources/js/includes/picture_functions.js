/*
* You must launch those functions in deocument ready and every time after ajax load
*
* in document ready
*
    lazy_load_launch();
    background_is_picture_launch();
*
* */
module.exports = {
	lazy_load_launch: function lazy_load_launch(){
		console.log('lazy_load_launch');

		// Lazy on load (scroll independent)
		$(".LazyLoad").each(function () {
			var elem = $(this); // picture or img
			//var i = elem.isInViewport();
			var i = true;

			if(i !== false){
				elem.removeClass('LazyLoad');
				if(elem.is('img')){
					var imgdata = elem.data('srcset');
					if(typeof imgdata !== 'undefined'){
						elem.attr('srcset', imgdata);
					}else{
						imgdata = elem.data('src');
						elem.attr('src', imgdata);
					}

				}else if(elem.is('picture')) {
					// For img in picture
					var imgs = elem.children('img');
					imgs.each(function () {
						var img = $(this);
						var imgdata = img.data('srcset');
						if(typeof imgdata !== 'undefined'){
							img.attr('srcset', imgdata);

						}else{
							imgdata = img.data('src');
							img.attr('src', imgdata);
						}

					});
					// for source in picture
					var sources = elem.children('source');
					sources.each(function () {
						var source = $(this);
						var imgdata = source.data('srcset');
						source.attr('srcset', imgdata);
					});

					if(elem.hasClass('Background_Is_Picture')){
						background_is_picture_launch();
					}

				}else{ // Background
					var imgdata = elem.data('src');
					elem.css('background-image', 'url(' + imgdata + ')');
				}
				elem.addClass('LazyLoaded');
			}
		})
	} ,
	background_is_picture_launch: function background_is_picture_launch(){
        console.log('background_is_picture_launch');
		var relaunch_needed = false;
		$('.Background_Is_Picture:not(.Loaded)').each(function () {
			//console.log("BG_PICT");
			var bg_target = $(this);
			//console.log(bg_target);
			var picture = bg_target.children(':first');
			//console.log(picture);
			if(picture.is('picture') === true){
				//console.log(picture.children('img'));
				var img = picture.children('img');
				var img_src = img.prop("currentSrc");

				// If currentSrc not supported
				if(typeof img_src === 'undefined'){
					img_src = img[0].src;
				}

                // console.log('IMAGE_PLACEHOLDER1');
                // console.log(img_src);

				if(img_src === '' || img_src.includes('transparent_placeholder')){
					img.on('load', function () {
						var img_src = img.prop("currentSrc");
						if(typeof img_src === 'undefined'){
							img_src = img[0].src;
						}

                        if(!img_src.includes('transparent_placeholder')){
                            bg_target.css('background-image', 'url(\''+img_src+'\')');
                            bg_target.addClass('Loaded');
						}else{
                            relaunch_needed = true;
						}

                        // console.log('IMAGE_PLACEHOLDER2');
                        // console.log(img_src);


					});

				}else{

                    if(!img_src.includes('transparent_placeholder')){
                        bg_target.css('background-image', 'url(\''+img_src+'\')');
                        bg_target.addClass('Loaded');
                    }else{
                        relaunch_needed = true;
                    }

				}
			}
		});
		console.log('background_is_picture_launch relauntch needed', relaunch_needed);
		if(relaunch_needed){
            //this.lazy_load_launch();
            //this.background_is_picture_launch();

		}
	},
}


