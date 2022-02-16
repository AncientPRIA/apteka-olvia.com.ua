
<nav class="menu">
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
</nav>