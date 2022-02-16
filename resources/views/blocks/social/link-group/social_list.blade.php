@php
//https://telegram.me/share/url?url=<URL>&text=<TEXT>
	$social_list_class= !isset($social_list_class)? "":$social_list_class;
@endphp

<div class="social-list {{$social_list_class}} {{isset($hide_class)? $hide_class:""}}">
	@foreach($links as  $link)
		<a  {!!  isset($link["attribute"])?$link["attribute"]:""!!}
			class="social-list__item {{!isset($link["social_item_class"]) ? "" : $link["social_item_class"] }}" href="{{$link["social_link"]}}"
			rel="nofollow,noindex" target="_blank"
		>
			@svg($link['path_svg'],"")
		</a>
	@endforeach
</div>