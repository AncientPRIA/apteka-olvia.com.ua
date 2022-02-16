<div class="row menu-bottom desktop">
	<div class="menu-column-left">
		@include("blocks.menu.menu_bottom.menu-cat.menu-cat-big")
		<div class="mobail_btn">
			{{--@include('blocks.btn.favorit.btn_favorit')--}}
			@include('blocks.btn.cart.btn_cart')
			@include('blocks.btn.autf.btn_autf')
		</div>
	</div>
	<div class="menu-column-right">
		@include('blocks.menu.menu_bottom.search.search')
		{{--@include('blocks.btn.favorit.btn_favorit')--}}
		@include('blocks.btn.cart.btn_cart')
		@include('blocks.btn.autf.btn_autf')
	</div>
</div>