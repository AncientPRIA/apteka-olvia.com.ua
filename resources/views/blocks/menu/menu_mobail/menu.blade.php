<div class="mobail menu-mobail-container">
	<div class="row menu-mobail">
		<a @if(request()->url() !== route('home')){!!'href="'.route('home').'"'!!}@endif class="menu-column-left">
			<img class="menu_mobail menu_mobail_img" src="{{ URL::asset("uploads/design/logo_mob.svg")}}" alt="">
		</a>
		<div class="menu-column-right">
			@include("blocks.menu.menu_bottom.menu-cat.menu-cat-big",['title'=>"КАТАЛОГ"])
			<div class="search-btn-menu">
				<svg enable-background="new 0 0 515.558 515.558"  viewBox="0 0 515.558 515.558"  xmlns="http://www.w3.org/2000/svg"><path d="m378.344 332.78c25.37-34.645 40.545-77.2 40.545-123.333 0-115.484-93.961-209.445-209.445-209.445s-209.444 93.961-209.444 209.445 93.961 209.445 209.445 209.445c46.133 0 88.692-15.177 123.337-40.547l137.212 137.212 45.564-45.564c0-.001-137.214-137.213-137.214-137.213zm-168.899 21.667c-79.958 0-145-65.042-145-145s65.042-145 145-145 145 65.042 145 145-65.043 145-145 145z"/></svg>
			</div>
			@include('blocks.btn.cart.btn_cart')
			@include('blocks.btn.autf.btn_autf')
		</div>

	</div>
	@include("blocks.menu.menu_bottom.search.search",["class_form"=>"mobail_form_container"])
</div>

