@include('layouts.head')
@include('includes.sections.header.header',
	[
        'header_include'=>"transparent_gheader.header",
        'header_top_bar' => true,
        'params' => [
		    'breadcrumbs_items'=>[
				[
					"href"=>"/",
					"title"=>"Главная"
				],
				[
					"title"=> string($strings, 'breadcrumbs_our_pharmacies', "Наши аптеки")
				],
			],
		]

	]
)



<div class="container">
    @component("blocks.title.section_title",
        [
            "data" =>[
                "title" => "<h1>НАШИ АПТЕКИ</h1>",
                "section_class_title" => "title-gen title_our_pharmacies wow fadeIn"
            ]
        ])
		<div class="map-wrapper wow fadeIn">
			<div class="map" id="map"></div>
			<div id="map_hider"></div>
		</div>

        @component("blocks.slider.slider_section",
                [
                    "data"=>[
                        "class_slider_container"=>"category-tab-slider",
                        "slider_arrow_next"=>"slider-category-next",
                        "slider_arrow_prev"=>"slider-category-prev",
                        "slider_list_class"=>"cards_list cards_list_cities wow fadeIn"
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
                        "data_atr_container"=>$data_atr_buf,
                    ],
                    "template"=> "slider_pharmacy_item",
                    "data_items"=> $city->shops
                ]
                )
            @endcomponent
        @endforeach

    @endcomponent
</div>
@include('blocks.modal_basket.modal_basket')
@include('blocks.popup.popup')
@include('layouts.footer')