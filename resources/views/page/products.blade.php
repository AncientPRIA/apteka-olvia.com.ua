@include('layouts.head')

@include('includes.sections.header.header',
	[
        'header_include'=>"transparent_gheader.header",
        'header_top_bar' => true,
        'params' => [
		    'breadcrumbs_items'=> $breadcrumbs,
		    'background' => $image_header ?? null
		]
	]
)

<script>
    var products_route = "{{route('products')}}";
    var sorting = "{{$sorting}}";
    var page = {!! $products->currentPage() !!};
    var category_id = {!! $category_id ?? 0 !!};
</script>

{{--<div class="border_bottom">--}}
{{--    <div class="container">--}}
{{--        @include("includes.sections.header.header-top-bar")--}}
{{--    </div>--}}
{{--</div>--}}
{{--<div class="container container_relative">--}}
{{--    @include("blocks.menu.menu_top.menu")--}}
{{--    @include("blocks.menu.menu_bottom.menu")--}}
{{--</div>--}}

<div class="container product_container">

    @component("blocks.title.section_title",
        [
            "data" =>[
                "section_class" => "section_sidebar",
                "title" => string($strings, 'products_sidebar_title', "Лекарственные препараты"),
                "section_class_title" => "title-gen product_title product_title_sidebar"
            ]
        ])

        <div class="section_sp"></div>

        <div class="section_sidebar_mobileBtn">
            Категории
        </div>

        <div class="section_sidebar_list">

			@include("blocks.sidebar.cat.cat-par",[])

        </div>


    @endcomponent

    @component("blocks.title.section_title",
        [
            "data" =>[
                "section_class" => "section_product",
                "title" => '<h1>'.$category_name.'</h1>',
                "section_class_title" => "title-gen product_title margin_left margin_bottom"
            ]
        ])

        <div class="section_sp"></div>

        <div class="product_filter">
            <div class="product_totalProducts">
                Товаров: {{$products->total()}}
            </div>
            <select class="product_select">
                @foreach($sorting_options as $sorting_option=>$sorting_text)

                    <option @if($sorting_option === $sorting){{'selected'}}@endif value="{{$sorting_option}}">{{$sorting_text}}</option>
                @endforeach
            </select>
        </div>

        <div class="product_list">
            @foreach($products as $item)
                @include("blocks.slider.items.slider_product_item")
            @endforeach
        </div>

{{--        <div class="product_navigation">--}}
{{--            <div class="product_load_more">--}}
{{--                <svg class="product_load_more_svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 88.1 96.36">--}}
{{--                    <g data-name="Слой 2">--}}
{{--                        <g data-name="Layer 1">--}}
{{--                            <path class="Load_More_Svg_Path" d="M17.94,19.11a38.41,38.41,0,0,1,49.94-1.32l-11.43.43a2.66,2.66,0,0,0,.1,5.32h.09l17.56-.65a2.65,2.65,0,0,0,2.55-2.66v-.31L76.11,2.56a2.66,2.66,0,1,0-5.32.2l.42,10.88a43.7,43.7,0,0,0-56.83,1.53A43.72,43.72,0,0,0,1.23,57.89a2.65,2.65,0,0,0,2.58,2,2.22,2.22,0,0,0,.63-.08,2.66,2.66,0,0,0,1.95-3.21A38.37,38.37,0,0,1,17.94,19.11Z" />--}}
{{--                            <path class="Load_More_Svg_Path" d="M86.87,38.47a2.66,2.66,0,0,0-5.16,1.26,38.39,38.39,0,0,1-61.8,38.59l11.57-1A2.66,2.66,0,1,0,31,72l-17.5,1.58a2.65,2.65,0,0,0-2.4,2.89l1.58,17.49a2.64,2.64,0,0,0,2.63,2.42,1,1,0,0,0,.24,0,2.65,2.65,0,0,0,2.4-2.89L17,82.8a43.39,43.39,0,0,0,25.24,9.63c.75,0,1.5,0,2.22,0a43.7,43.7,0,0,0,42.41-54Z" />--}}
{{--                        </g>--}}
{{--                    </g>--}}
{{--                </svg>--}}
{{--            </div>--}}
{{--            {{ $products->links('vendor.pagination.default') }}--}}
{{--        </div>--}}

            @component("blocks.default.default_pagination_&_loadMore.default_pagination_&_loadMore",
                    [
                        "data"=>[
                            "class_navigation"=>"product_navigation",
                            "class_load_more"=>"product_load_more",
                            "class_load_more_svg"=>"product_load_more_svg"
                        ],
                        "load_more"=>"true",
                        "pagination"=>"true",
                        "data_items"=>$products
                    ]
                    )
            @endcomponent

    @endcomponent
</div>
@if(isset($category_description) && $category_description !== "" && $products->currentPage() === 1)
    <div class="container category_description tinymce">
        {!! $category_description !!}
    </div>
@endif

@include('blocks.modal_basket.modal_basket')
@include('blocks.popup.popup')
@include('layouts.footer')