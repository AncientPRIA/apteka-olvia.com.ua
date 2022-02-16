@include('layouts.head')
@include('includes.sections.header.header',
	[
	'header_include'=>"home",
	'header_top_bar' => true
	]
)
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

<button onclick="popup_show('Autf');">Рег</button>
{{--<button class="Reg">Рег</button>--}}
<button class="Basket open_close" >Корзина</button>
@include('blocks.modal_basket.modal_basket')
@include('blocks.popup.popup')
@include('layouts.footer')