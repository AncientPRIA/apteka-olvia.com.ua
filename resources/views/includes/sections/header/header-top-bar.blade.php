
<div class="container">
	<div class="header-top-bar">

		<div class="header-top-bar-phone">
			@svg("img/svg/phone-2.svg")
		</div>

		<div class="time-work">
			<svg class="watch-svg" enable-background="new 0 0 443.294 443.294"  viewBox="0 0 443.294 443.294" xmlns="http://www.w3.org/2000/svg">
				<path d="m221.647 0c-122.214 0-221.647 99.433-221.647 221.647s99.433 221.647 221.647 221.647 221.647-99.433 221.647-221.647-99.433-221.647-221.647-221.647zm0 415.588c-106.941 0-193.941-87-193.941-193.941s87-193.941 193.941-193.941 193.941 87 193.941 193.941-87 193.941-193.941 193.941z" />
				<path d="m235.5 83.118h-27.706v144.265l87.176 87.176 19.589-19.589-79.059-79.059z" />
			</svg>
			{{--@svg('img/svg/watch.svg',"")--}}
			<span class="time-work__text" >Пн - Вс 08:00-21:00</span>
		</div>

	<ul class="phone-list">
		<li class="phone-list__item">
			<svg version="1.1" class="phone-svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
			     viewBox="0 0 384 384" style="enable-background:new 0 0 384 384;" xml:space="preserve">
					<path d="M353.188,252.052c-23.51,0-46.594-3.677-68.469-10.906c-10.719-3.656-23.896-0.302-30.438,6.417l-43.177,32.594
						c-50.073-26.729-80.917-57.563-107.281-107.26l31.635-42.052c8.219-8.208,11.167-20.198,7.635-31.448
						c-7.26-21.99-10.948-45.063-10.948-68.583C132.146,13.823,118.323,0,101.333,0H30.813C13.823,0,0,13.823,0,30.813
						C0,225.563,158.438,384,353.188,384c16.99,0,30.813-13.823,30.813-30.813v-70.323C384,265.875,370.177,252.052,353.188,252.052z"
					/>
			</svg>

			{{--@svg('img/svg/call.svg',"")--}}
			<a class="phone-list__link" href="tel:+38071617748" rel="nofollow,noindex">+38 (071) 361 77 48</a>
		</li>
		<li class="phone-list__item">
			<a class="phone-list__link" href="tel:+380622018080" rel="nofollow,noindex">+38 (062) 201 80 80</a>
		</li>
		<li class="phone-list__item">
			<a class="phone-list__link" href="tel:+380713617759" rel="nofollow,noindex">+38 (071) 361 77 59 (мед.изделия)</a>
		</li>
		<li class="phone-list__item">
			<span class="phone-list__call" onclick="popup_show({cls:'Call_Back', scrollOff:'body'});">Заказать звонок</span>
		</li>
	</ul>

		@php
			$links = [];

            $social = setting('site.social_link_fb');
            if($social !== null){
            $links[] = [
                                "social_link" => $social,
                                "path_svg" => "img/svg/facebook.svg",
                                "social_item_class" => "wow zoomIn",
                            ];
    		}
            $social = setting('site.social_link_vk');
            if($social !== null){
            $links[] = [
                                "social_link" => $social,
                                "path_svg" => "img/svg/vk.svg",
                                "social_item_class" => "wow zoomIn",
                                "attribute"=>"data-wow-delay='0.2s'"
                            ];

			}
			$social = setting('site.social_link_tw');
            if($social !== null){
            $links[] = [
                                "social_link" => $social,
                                "path_svg" => "img/svg/twitter.svg",
                                "social_item_class" => "wow zoomIn",
                                "attribute"=>"data-wow-delay='0.4s'"
                            ];
    		}
			$social = setting('site.social_link_ig');
            if($social !== null){
            $links[] = [
                                   "social_link" => $social,
                                "path_svg" => "img/svg/instagram.svg",
                                "social_item_class" => "wow zoomIn",
                                "attribute"=>"data-wow-delay='0.6s'"
                            ];
			}
			$social = setting('site.social_link_yt');
            if($social !== null){
            $links[] = [
                                "social_link" => $social,
                                "path_svg" => "img/svg/youtube.svg",
                                "social_item_class" => "wow zoomIn",
                                "attribute"=>"data-wow-delay='0.8s'"
                            ];

            }
		@endphp

		@include("blocks.social.link-group.social_list",[
				"social_list_class"=>"top-social desktop",
                "links"=>$links,
            ])

		@include('blocks.menu.menu_top.btn.menu-btn')

	{{--<div class="social-list">--}}
		{{--<a class="social-list__item" href="#" target="_blank">--}}
			{{--<svg version="1.1" class="fb" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"--}}
			     {{--viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">--}}
				{{--<path d="M288,176v-64c0-17.664,14.336-32,32-32h32V0h-64c-53.024,0-96,42.976-96,96v80h-64v80h64v256h96V256h64l32-80H288z"/>--}}
			{{--</svg>--}}
		{{--</a>--}}
		{{--<a class="social-list__item" href="#" target="_blank">--}}
			{{--<svg version="1.1" class="fb" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"--}}
			     {{--viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">--}}
				{{--<path d="M288,176v-64c0-17.664,14.336-32,32-32h32V0h-64c-53.024,0-96,42.976-96,96v80h-64v80h64v256h96V256h64l32-80H288z"/>--}}
			{{--</svg>--}}
		{{--</a>--}}
		{{--<a class="social-list__item" href="#" target="_blank">--}}
			{{--<svg version="1.1" class="fb" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"--}}
			     {{--viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">--}}
				{{--<path d="M288,176v-64c0-17.664,14.336-32,32-32h32V0h-64c-53.024,0-96,42.976-96,96v80h-64v80h64v256h96V256h64l32-80H288z"/>--}}
			{{--</svg>--}}
		{{--</a>--}}
		{{--<a class="social-list__item" href="#" target="_blank">--}}
			{{--<svg version="1.1" class="fb" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"--}}
			     {{--viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">--}}
				{{--<path d="M288,176v-64c0-17.664,14.336-32,32-32h32V0h-64c-53.024,0-96,42.976-96,96v80h-64v80h64v256h96V256h64l32-80H288z"/>--}}
			{{--</svg>--}}
		{{--</a>--}}
		{{--<a class="social-list__item" href="#" target="_blank">--}}
			{{--<svg version="1.1" class="fb" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"--}}
			     {{--viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">--}}
				{{--<path d="M288,176v-64c0-17.664,14.336-32,32-32h32V0h-64c-53.024,0-96,42.976-96,96v80h-64v80h64v256h96V256h64l32-80H288z"/>--}}
			{{--</svg>--}}
		{{--</a>--}}
	{{--</div>--}}

		{{--@include("blocks.social.link-group.social_list",[--}}
			{{--"social_list_class"=>"",--}}
			{{--"links"=>[--}}
					{{--[--}}
						{{--"social_link" => "#",--}}
						{{--"path_svg" => 'img/svg/facebook.svg'--}}
					{{--],--}}
					{{--[--}}
						{{--"social_link" => "#",--}}
						{{--"path_svg" => 'img/svg/vk.svg'--}}
					{{--],--}}
										{{--[--}}
						{{--"social_link" => "#",--}}
						{{--"path_svg" => 'img/svg/twitter.svg'--}}
					{{--],--}}
										{{--[--}}
						{{--"social_link" => "#",--}}
						{{--"path_svg" => 'img/svg/instagram.svg'--}}
					{{--],--}}
										{{--[--}}
						{{--"social_item_class" => "test-class",--}}
						{{--"social_link" => "#",--}}
						{{--"path_svg" => 'img/svg/youtube.svg'--}}
					{{--],--}}
			{{--],--}}
		{{--])--}}

	</div>
</div>