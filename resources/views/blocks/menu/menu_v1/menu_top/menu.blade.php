<div class="row menu-top">
	<div class="menu-column-left">
		<a href="/" class="menu-top__home-link wow heartBeat">
			{{--@svg("uploads/design/logo.svg")--}}
			<img src="{{ URL::asset("uploads/design/logo.svg")}}" alt="logo" title="logo">
		</a>
	</div>
	<div class="menu-column-right">
		@include('blocks.menu.menu_top.nav-list')
		@include('blocks.menu.menu_top.btn.menu-btn')
	</div>
</div>