<nav class="menu" {!!  isset($att)?$att:""!!}>
	@foreach(get_nav_menu() as $menu_item)
	<a href="{{$menu_item['link']}}" class="menu-link">
		<span class="menu-link__icon">
			@if(isset($menu_item['icon']))
			@svg($menu_item['icon'])
			@endif
		</span>
		{{$menu_item['title'] ?? ''}}
	</a>
	@endforeach
	@if(isset($social))
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
			"social_list_class"=>"top-social",
			"links"=>$links,
		])
	@endif
</nav>