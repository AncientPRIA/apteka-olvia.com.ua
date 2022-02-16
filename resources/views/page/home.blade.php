@php
$head_include = '
<link rel="preload" href="'.asset('css/home.min.css').'" as="style">
';
if(\Browser::isMobile()){
    $head_include .= '<link rel="preload" href="'.asset('uploads/video/home_mobile.mp4').'">';
}

@endphp
@include('layouts.head')
@include('includes.sections.header.header',
	[
	'header_include'=>"home",
	'header_top_bar' => true
	]
)

<div class="container ">

{{--  слайдер с товарами--}}
    <div class="tab_list_product">
        @php
            $btn_param=[
                "name"=>"btn",
                "atr"=>"data-product='season'",
                "btn_class"=>"title_product_slider active",
                "text"=>"СПЕЦИАЛЬНЫЕ ПРЕДЛОЖЕНИЯ"
            ];
        @endphp
        @include("blocks.btn.btn")

        @php
            $btn_param=[
                "name"=>"btn",
                "atr"=>"data-product='top'",
                "btn_class"=>"title_product_slider",
                "text"=>"ТОП ПРОДАЖ"
            ];
        @endphp
        @include("blocks.btn.btn")
    </div>

    @component("blocks.slider.slider_section",
                [
                    "data"=>[
                        "class_slider_container"=>"product-slider cards_list_product-season product_item_too_mobail",
                        "slider_arrow_next"=>"slider-product-next",
                        "slider_arrow_prev"=>"slider-product-prev",
                        "slider_list_class"=>"cards_list cards_list_product"
                    ],
                    "template"=> "slider_product_item",
                    "data_items"=> $products_featured
                ]
                )
    @endcomponent

    @component("blocks.slider.slider_section",
                [
                    "data"=>[
                        "class_slider_container"=>"product-slider cards_list_product-top product_item_too_mobail",
                        "slider_arrow_next"=>"slider-product-next",
                        "slider_arrow_prev"=>"slider-product-prev",
                        "slider_list_class"=>"cards_list cards_list_product",
                        'data_atr_container' => 'style=display:none',
                    ],
                    "template"=> "slider_product_item",
                    "data_items"=> $products_top_sells
                ]
                )
    @endcomponent


{{-- END слайдер с товарами--}}


    {{--@include("blocks.advantages.advantages")--}}

    @php
        $btn_param=[
            "name"=>"btn",
            "btn_class"=>"tab_btn btn_all_product wow slideInUp",
            "href"=>"/products",
            "text"=>"Все товары"
        ];
    @endphp
    @include("blocks.btn.link")
</div>


@if(count($recommended_wide) === 2)
<div class="stocks_category_list">
    @php
        $item = $recommended_wide[0];
        $image = $item->image;
        $sizes = array(
                        '1' => ['width' => 1922, 'relative_path' => 'uploads/'.$image, 'q'=> 90],
                        'id' => 'banner_01',
                      );
        $mappings = array(
                        '>320' => '1',
                        'default' => '1'
                      );
        $sizes = Img::img($sizes);

        $picture = Img::picture_compose($sizes, $mappings, true, '', '', true);
    @endphp

    <div class="stocks_category_item ">
        <div class="stocks_category_item_bg Background_Is_Picture">
            {!! $picture !!}
        </div>

        <div class="stocks_category_item_info">
            <div class="stocks_category_item_info_name">
                Новое
            </div>
            <div class="stocks_category_item_info_title">
                {{$item->name}}
            </div>

            @php
                $btn_param=[
                    "name"=>"btn",
                    "btn_class"=>"tab_btn stocks_btn wow slideInUp",
                    "href"=>$item->get_path(),
                    "text"=>"Подробнее"
                ];
            @endphp
            @include("blocks.btn.link")
        </div>
    </div>

    @php
        $item = $recommended_wide[1];
        $image = $item->image;
        $sizes = array(
                        '1' => ['width' => 1922, 'relative_path' => 'uploads/'.$image, 'q'=> 90],
                        'id' => 'banner_02',
                      );
        $mappings = array(
                        '>320' => '1',
                        'default' => '1'
                      );
        $sizes = Img::img($sizes);

        $picture = Img::picture_compose($sizes, $mappings, true, '', '', true);
    @endphp

    <div class="stocks_category_item ">
        <div class="stocks_category_item_bg Background_Is_Picture">
            {!! $picture !!}
        </div>

        <div class="stocks_category_item_info">
            <div class="stocks_category_item_info_name">
                Новое
            </div>
            <div class="stocks_category_item_info_title">
                {{$item->name}}
            </div>

            @php
                $btn_param=[
                    "name"=>"btn",
                    "btn_class"=>"tab_btn stocks_btn wow slideInUp",
                    "href"=>$item->get_path(),
                    "text"=>"Подробнее"
                ];
            @endphp
            @include("blocks.btn.link")
        </div>
    </div>
</div>
@endif


@if(count($categories_featured) >= 6)
<div class="container"> {{-- Наша рекоммендации (Recommended) --}}

    @component("blocks.title.section_title",
        [
            "data" =>[
                "title" => "НАШИ РЕКОМЕНДАЦИИ",
                "section_class_title" => "title-gen"
            ]
        ])

        @include("blocks.recommendations.recommendations", [
            "data_items" => $categories_featured
        ])

    @endcomponent
</div>
@endif

<div class="container">

    @component("blocks.title.section_title",
        [
            "data" =>[
                "title" => "СПИСОК АПТЕК",
                "section_class_title" => "title-gen"
            ]
        ])

        @component("blocks.slider.slider_section",
                [
                    "data"=>[
                        "class_slider_container"=>"category-tab-slider",
                        "slider_arrow_next"=>"slider-category-next",
                        "slider_arrow_prev"=>"slider-category-prev",
                        "slider_list_class"=>"cards_list cards_list_cities"
                    ],
                    "template"=> "slider_tab_item",
                    "data_items"=> $cities
                ]
                )
        @endcomponent

		@php
			$count_pharh = 0;
		@endphp

        @foreach($cities as $city)

				@php
					if($count_pharh === 0){
						$data_atr_buf="";
						$count_pharh = 1;
					}else{
						$data_atr_buf = 'style="display:none"';
					}
				@endphp

                @component("blocks.slider.slider_section",
                    [
                        "data"=>[
                            "class_slider_container"=>"pharmacy-slider pharmacy-slider-".$city['id'],
                            "slider_arrow_next"=>"slider-pharmacy-next",
                            "slider_arrow_prev"=>"slider-pharmacy-prev",
                            "slider_list_class"=>"cards_list cards_list_pharmacy",
                            "data_atr_container"=>$data_atr_buf
                        ],
                        "template"=> "slider_pharmacy_item",
                        "data_items"=> $city->shops
                    ]
                    )
                @endcomponent
        @endforeach

    @endcomponent
</div>

<div class="container">
    @php
        if(count($actions) >= 1){
            $action_right_1 = $actions[0];
            $actions->forget(0);
        }else{
            $action_right_1 = null;
        }
        if(count($actions) >= 1){
            $action_right_2 = $actions[1];
            $actions->forget(1);
        }else{
            $action_right_2 = null;
        }
    //dd($actions);

    @endphp
    @if($action_right_1 !== null && $action_right_2 !== null)
        <div class="sell-home-grid">
            @component("blocks.slider.slider_section",
                [
                    "data"=>[
                        "class_slider_container"=>"sell-slider wow slideInLeft",
                        "slider_arrow_next"=>"slider-sell-next",
                        "slider_arrow_prev"=>"slider-sell-prev",
                        "slider_list_class"=>"cards_list cards_list_sell"
                    ],
                    "template"=> "slider_sell_item",
                    "data_items"=> $actions
                ]
                )
            @endcomponent
            <div class="sell-home-grid-group">
                @php
                    $image = $action_right_1['image'];
                    $sizes = array(
                                    '1' => ['width' => 376, 'relative_path' => 'uploads/'.$image, 'q'=> 90],
                                    'id' => 'sell_item_right_'.$action_right_1['id'],
                                  );
                    $mappings = array(
                                    '>320' => '1',
                                    'default' => '1'
                                  );
                    $sizes = Img::img($sizes);

                    $picture = Img::picture_compose($sizes, $mappings, true, '', $image, true);
                @endphp
                <a href="{{route('products').'/'.$action_right_1->get_path()}}" class="sell-home-grid-item sell-home-grid-item_min Background_Is_Picture wow slideInDown" data-wow-offset="10" data-wow-dalay="0.2s">
                    {!! $picture !!}

                    <div class="discount">
                        {{$action_right_1->get_root()->name}}
                    </div>
                    <div class="discount-info">
                        {!! $action_right_1['name'] !!}
                    </div>
                </a>
                @php
                    $image = $action_right_2['image'];
                    $sizes = array(
                                    '1' => ['width' => 376, 'relative_path' => 'uploads/'.$image, 'q'=> 90],
                                    'id' => 'sell_item_right_'.$action_right_2['id'],
                                  );
                    $mappings = array(
                                    '>320' => '1',
                                    'default' => '1'
                                  );
                    $sizes = Img::img($sizes);

                    $picture = Img::picture_compose($sizes, $mappings, true, '', $image, true);
                @endphp
                <a href="{{route('products').'/'.$action_right_2->get_path()}}" class="sell-home-grid-item sell-home-grid-item_min Background_Is_Picture wow slideInUp" data-wow-offset="10" data-wow-delay="0.4s">
                    {!! $picture !!}

                    <div class="discount">
                        {{$action_right_2->get_root()->name}}
                    </div>
                    <div class="discount-info">
                        {!! $action_right_2['name'] !!}
                    </div>
                </a>
            </div>

        </div>
    @endif

</div>

<div class="container">

    <div class="o_nas">
        <div class="o_nas_logo">
            <img src="{{ URL::asset("uploads/design/logo.svg")}}" alt="">
        </div>
        <div class="o_nas_list">
            <div class="col-left">
                <div class="o_nas_content">
                    <div class="o_nas_title">
                        {!! string($strings, 'home_o_nas_title_1', "placeholder") !!}
                    </div>
                    <div class="o_nas_sub_title">
                        {!! string($strings, 'home_o_nas_sub_title_1', "placeholder") !!}
                    </div>
                    <div class="o_nas_text">
                        {!! string($strings, 'home_o_nas_text_1', "placeholder") !!}
                    </div>
                </div>
            </div>
            <div class="col-right">
                <div class="o_nas_content">
                    <div class="o_nas_title">
                        {!! string($strings, 'home_o_nas_title_2', "placeholder") !!}
                    </div>
                    <div class="o_nas_sub_title">
                        {!! string($strings, 'home_o_nas_sub_title_2', "placeholder") !!}
                    </div>
                    <div class="o_nas_text">
                        {!! string($strings, 'home_o_nas_text_2', "placeholder") !!}
                    </div>
                </div>
                <div class="o_nas_content">
                    <div class="o_nas_title">
                        {!! string($strings, 'home_o_nas_title_3', "placeholder") !!}
                    </div>
                    <div class="o_nas_sub_title">
                        {!! string($strings, 'home_o_nas_sub_title_3', "placeholder") !!}
                    </div>
                    <div class="o_nas_text">
                        {!! string($strings, 'home_o_nas_text_3', "placeholder") !!}
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>


@include('blocks.modal_basket.modal_basket')
@include('blocks.popup.popup')
@include('layouts.footer')