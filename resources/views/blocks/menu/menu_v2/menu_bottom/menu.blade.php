<div class="row menu-bottom">
	<div class="menu-column-left">
		@include("blocks.menu.menu_bottom.menu-cat.menu-cat-big",["class"=>"menu_desktop"])
		{{--<div class="mobail_btn">--}}
			{{--@include('blocks.btn.favorit.btn_favorit')--}}
			{{--@include('blocks.btn.cart.btn_cart')--}}
			{{--@include('blocks.btn.autf.btn_autf')--}}
		{{--</div>--}}
	</div>
	<div class="menu-column-right">
		@include('blocks.menu.menu_bottom.search.search')
		@include('blocks.btn.cart.btn_cart',["class"=>"menu_mobail"])
		@include('blocks.btn.autf.btn_autf',["class"=>"menu_mobail"])
	</div>
</div>