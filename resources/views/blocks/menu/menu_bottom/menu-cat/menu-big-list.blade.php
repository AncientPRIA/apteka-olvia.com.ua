@php
	$lvl = "level-".$index_lvl;
	/*if(isset($active_full_sub) && $active_full_sub === true ){
		$menu['active'] = true;
	}*/
	if(isset($active_full_sub) && $active_full_sub === true && $index_lvl===2){
		$menu['active'] = true;
	}
@endphp

@if(isset($menu["submenu"]))

	<li class="menu-sub-item menu-sub-item-{{$lvl}}">
		<div class="menu-sub-item__nav">
			@if(isset($menu["icon"]))
			<div class="menu-sub-item__nav-wrap">
				<img src="{{URL::asset($menu["icon"])}}" class="menu-sub-item__icon" alt="">
				<a  href="{{$menu["link"]}}" >{{$menu["title"]}}</a>
			</div>
			@else
				<a  href="{{$menu["link"]}}" >{{$menu["title"]}}</a>
			@endif
			@if($lvl > 0)
				<img src="{{URL::asset("img/svg/right-arrow-solid.svg")}}" class="toggle-menu-big {{isset($menu["active"])?"toggle-menu-big_open":""}}" data-lvl = "{{$index_lvl}}" data-menu="menu-sub-{{$lvl}}-{{$menu["name"]}}" alt="">
			@else
				<img src="{{URL::asset("img/svg/a.svg")}}" class="toggle-menu-big {{isset($menu["active"])?"toggle-menu-big_open":""}}" data-lvl = "{{$index_lvl}}" data-menu="menu-sub-{{$lvl}}-{{$menu["name"]}}" alt="">
			@endif

		</div>
		{{--<h1>{{$menu["active"]}} {{true}}</h1>--}}
		<ul data-lvl = "{{$index_lvl}}"  class="menu-sub menu-sub-{{$lvl}}  menu-sub-{{$lvl}}-{{$menu["name"]}} {{isset($menu["active"])? "show_lvl_1 show_active" : ""}}" >
			@for($j=0;$j<count($menu["submenu"]);$j++)
				@include("blocks.menu.menu_bottom.menu-cat.menu-big-list",[
					"menu" => $menu["submenu"][$j],
					"index"=>$j,
					"index_lvl"=>$index_lvl+1,
				])
			@endfor
		</ul>
	</li>
@else
	<li class="menu-sub-item menu-sub-item__not-sub menu-sub-item-{{$lvl}}" data-lvl = "{{$index_lvl}}">
		<a href="{{$menu["link"]}}">{{$menu["title"]}}</a>
	</li>
@endif


