@include('layouts.head')
@include('includes.sections.header.header',
	[
	'header_include'=>"discount_products",
	'header_top_bar' => true,
	'params' => [
		    'bg' => 'design/home/bg.png',
		    'breadcrumbs_items'=>[
				[
					"href"=>"/",
					"title"=>"Главная"
				],
				[
					"title"=> string($strings, 'breadcrumbs_discount_products', "Акции")
				],
			],
		]
	]
)

{{--{{dd($products)}}--}}
<div class="container">
    @php
        $actions_title = string($strings, "actions_title", "Товары, принимающие участие в акции:")
    @endphp
    @component("blocks.title.section_title",
        [
            "data" =>[
                "section_class" => "section_discount_product",
                "title" => "<span>$actions_title</span>",
                "section_class_title" => "title-gen discount_products_title"
            ]
        ])

        <div class="discount_products_list">
            @foreach($products as $item)

                    @include("blocks.slider.items.slider_product_item")

            @endforeach
        </div>


    @endcomponent
</div>

@include('blocks.modal_basket.modal_basket')
@include('blocks.popup.popup')
@include('layouts.footer')