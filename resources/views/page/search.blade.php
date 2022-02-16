@include('layouts.head')
@include('includes.sections.header.header',
	[
	'header_include'=>"transparent_gheader.header",
	'header_top_bar' => true,
	]
)

<div class="container container_footer_padding">

	@component("blocks.title.section_title",
		[
			"data" =>[
				"section_class" => "section_product",
				"title" => "Результат по поиску: ".mb_strimwidth($keyword, 0, 16, "..."),
				"section_class_title" => "title-gen product_title margin_left margin_bottom"
			]
		])

		<div class="section_sp"></div>


		<div class="product_filter">
			<div class="product_totalProducts">
				Товаров найдено: {{count($search)}}
			</div>
		</div>

	@if(count($search) > 0)
		<div class="product_list">
			@foreach($search as $item)
				@include("blocks.slider.items.slider_product_item")
			@endforeach
		</div>
	@else
		<p class="rezult-null">
			По запросу {{$keyword}}, найдено 0
		</p>
	@endif
	@endcomponent
</div>



@include('blocks.modal_basket.modal_basket')
@include('blocks.popup.popup')
@include('layouts.footer')