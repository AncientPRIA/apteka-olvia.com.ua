<div class="row menu-top">
	<div class="menu-column-left">
		<a @if(request()->url() !== route('home')){!!'href="'.route('home').'"'!!}@endif class="menu-top__home-link wow heartBeat">
			{{--@svg("uploads/design/logo.svg")--}}
			<img class="menu_desktop" src="{{ URL::asset("uploads/design/logo.svg")}}" alt="">
			<img class="menu_mobail menu_mobail_img" src="{{ URL::asset("uploads/design/logo_mob.svg")}}" alt="">
		</a>
	</div>
	<div class="menu-column-right menu_desktop">
		@include('blocks.menu.menu_top.nav-list')
		@include('blocks.menu.menu_top.btn.menu-btn')
	</div>
	<div class="menu-column-right menu_mobail">
		@include("blocks.menu.menu_bottom.menu-cat.menu-cat-big", ["class"=>"menu_mobail"])
		@include('blocks.btn.cart.btn_cart')
		@include('blocks.btn.autf.btn_autf')
	</div>
</div>