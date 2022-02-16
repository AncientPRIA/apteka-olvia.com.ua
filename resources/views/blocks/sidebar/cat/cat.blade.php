@php
	$lvl = "level-".$index_lvl;
@endphp

@if(isset($menu["submenu"]))
	<li class="menu-aside-sub-item menu-aside-sub-item-{{$lvl}} @if(isset($menu['is_current']) && $menu['is_current'] === true){{' current'}}@endif"">
		<div class="menu-aside-sub-item__nav {{isset($menu["active"])?"active_item":""}}">
			@if(isset($menu["icon"]))
				<div class="menu-aside-sub-item__nav-wrap">
					<img src="{{URL::asset($menu["icon"])}}" class="menu-aside-sub-item__icon" alt="">
					<a  href="{{$menu["link"]}}" >{{$menu["title"]}}</a>
				</div>
			@else
				<a  href="{{$menu["link"]}}" >{{$menu["title"]}}</a>
			@endif
			<img src="{{URL::asset("img/svg/right-arrow-666666.svg")}}" class="toggle-menu-aside {{isset($menu["active"])?"toggle-menu-aside_open":""}}" data-lvl = "{{$index_lvl}}" data-menu="menu-aside-sub-{{$lvl}}-{{$menu["name"]}}" alt="">
		</div>

		<ul data-lvl = "{{$index_lvl}}" class="menu-aside-sub menu-aside-sub-{{$lvl}}  menu-aside-sub-{{$lvl}}-{{$menu["name"]}} {{isset($menu["active"])? "show_lvl" : ""}}" >
			@for($j=0;$j<count($menu["submenu"]);$j++)
				@include("blocks.sidebar.cat.cat",[
					"menu" => $menu["submenu"][$j],
					"index"=>$j,
					"index_lvl"=>$index_lvl+1,
				])
			@endfor
		</ul>
	</li>
@else
	<li class="menu-aside-sub-item menu-aside-sub-item-{{$lvl}} @if(isset($menu['is_current']) && $menu['is_current'] === true){{' current'}}@endif" data-lvl = "{{$index_lvl}}">
		<a href="{{$menu["link"]}}">{{$menu["title"]}}</a>
	</li>
@endif


