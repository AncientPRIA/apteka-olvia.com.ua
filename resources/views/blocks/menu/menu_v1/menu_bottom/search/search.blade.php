<form action="/search"  method="get" class="form-search">
	{{--@csrf--}}
	<div class="form-group-search">
		<input class="input-search" name="q" placeholder="Поиск" name="search" type="text">
		<button class="search-btn" type="submit">
			@svg("img/svg/search.svg")
		</button>
	</div>

</form>