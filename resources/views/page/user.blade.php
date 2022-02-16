@include('layouts.head')
@include('includes.sections.header.header',
	[
		'header_include'=>"transparent_gheader.header",
		'header_top_bar' => true,
		'params' =>[
			'breadcrumbs_items'=>[
				[
					"href"=>"/",
					"title"=>"Главная"
				],
				[
					"title"=> string($strings, 'breadcrumbs_user', "Профиль")
				],
			],
		]
	]
)
{{--{{dd($favorites)}}--}}

<div class="container ">
	<div class="row row_ai_fs Profile">
		<div class="col-6">
			<div class="profile-title">
				<div class="profile-title__group">
					@svg("img/svg/user.svg")
					<span>{!! string($strings, 'my prof', 'Личный кабинет') !!}</span>
				</div>
				<a href="/logout" class="exit">
					@svg("img/svg/logout.svg")
				</a>
			</div>
			@include('blocks.forms.profile_update.profile_update')
		</div>
		<div class="col-6">
			<div class="profile-title profile-title_jc-sb">
				<span class="profile-tab active" data-tab="history">
					{!! string($strings, 'my history', 'История заказов') !!}
				</span>
				<span class="profile-tab" data-tab="prof-favorite">
					{!! string($strings, 'my favorit', 'Избранное') !!}
				</span>
			</div>

			<div class="history tab_fn prof-scroll">
				@for($j=0;$j<count($history);$j++)
					<div class="history-item">
						<div class="history-header">
							{{$history[$j]["date"]}}
						</div>

						@foreach($history[$j]["items"] as $content)
							@include("blocks.modal_basket.items.profile")
						@endforeach
						<div class="history-footer">
							ИТОГО: {{$history[$j]["price_full"]}}  &#8381
						</div>
					</div>
				@endfor
			</div>

			<div class="prof-favorite tab_fn prof-scroll">
				<div class="content-favorites-empty">
					У Вас нет избранного
				</div>
				<div class="content-favorites-list">
					@foreach($favorites as $content)
						@include("blocks.modal_basket.items.favorit")
					@endforeach
				</div>
			</div>

		</div>
	</div>

</div>

@include('blocks.modal_basket.modal_basket')
@include('blocks.popup.popup')
@include('layouts.footer')