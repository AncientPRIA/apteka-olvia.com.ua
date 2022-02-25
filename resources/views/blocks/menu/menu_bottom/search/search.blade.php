<form action="/search"  method="get" class="form-search navbar__form-group-search {{isset($class_form)? $class_form : ""}}" onclick="popup_show({cls:'Search', bodyFix: true})">
	{{--@csrf--}}
	<div class="form-group-search">
		<input class="input-search" name="q" placeholder="Поиск" type="text" autocomplete="off">
		<button class="search-btn" type="submit">
			@svg("img/svg/search.svg")
		</button>
	</div>
	<ul class="smart-search">
	</ul>

</form>