@php

    $sizes = array(
                    '1' => ['width' => 170, 'relative_path' => 'uploads/'.$item["image"], 'q'=> 90],
                    'id' => 'pharm_img_'.$item->id,
                  );
    $mappings = array(
                    '>320' => '1',
                    'default' => '1'
                  );
    $sizes = Img::img($sizes);
    $picture = Img::picture_compose($sizes, $mappings, true, '', $item["image"], true);
@endphp



<div class="pharmacy_item">
    <div class="pharmacy_item_top">
        <div class="pharmacy_item_col_left">
            <div class="pharmacy_item_svg svg_name">
                @svg("img/svg/pharm_name.svg")
            </div>
            <div class="pharmacy_item_title">
                Наша аптека
            </div>
        </div>
        <div class="pharmacy_item_col_right">
            <div class="pharmacy_item_svg pharm_map margin-left">
                @svg("img/svg/pharm_map.svg")
            </div>
            <div class="pharmacy_item_title margin-left">
                Мы на карте
            </div>
        </div>
    </div>

    <div class="pharmacy_item_img">
        <div class="pharmacy_item_col_left pharmacy_item_img_photo Background_Is_Picture">
            {!! $picture !!}
	        <div class="pharmacy_item_time">
		        <div class="pharmacy_item_svg svg_time">
			        @svg("img/svg/pharm_time.svg")
		        </div>
		        {{--<div class="pharmacy_item_title">--}}
			        {{--ЧАСЫ РАБОТЫ--}}
		        {{--</div>--}}
		        <div class="pharmacy_item_text">
			        {{$item["opening_hours"]}}
		        </div>
	        </div>
        </div>
        @php

            $sizes = array(
                            '1' => ['width' => 170, 'relative_path' => 'uploads/'.$item["image_map"], 'q'=> 90],
                            'id' => 'pharm_img'.$item["image_map"],
                          );
            $mappings = array(
                            '>320' => '1',
                            'default' => '1'
                          );
            $sizes = Img::img($sizes);
            $picture = Img::picture_compose($sizes, $mappings, true, '', $item["image_map"], true);
        @endphp
        <div class="pharmacy_item_col_right pharmacy_item_img_map Background_Is_Picture">
            {!! $picture !!}
        </div>
    </div>

    <div class="pharmacy_item_info">
        <div class="pharmacy_item_info_top">
            <div class="pharmacy_item_col_left">
                <div class="pharmacy_item_address">
                    <div class="pharmacy_item_svg svg_address">
                        @svg("img/svg/pharm_address.svg")
                    </div>
                    <div class="pharmacy_item_title">
                        Адрес
                    </div>
                    <a rel="nofollow,noindex" target="_blank" href="{{$item["map_link"]}}" class="pharmacy_item_text text_link">
                        {{$item["address"]}}
                    </a>
                </div>
            </div>

            <div class="pharmacy_item_col_right">
                <div class="pharmacy_item_phone">
                    <div class="pharmacy_item_svg svg_phone margin-left">
                        @svg("img/svg/pharm_phone.svg")
                    </div>
                    <div class="pharmacy_item_title margin-left">
                        Тел.
                    </div>
                    <div class="pharmacy_item_text margin-left pharmacy_item_text-column">
                            @php
                                 //$phone_list = trim($item["phone"]); // del space
                                 //$phone_list = explode(",", $phone_list); // split ,
                            @endphp
                            @for($i=0; $i <= 3; $i++)
                            @if(isset($item['phone_'.($i+1)]) && $item['phone_'.($i+1)] !== null)
                            <div class="pharmacy_item_phone_container">
                                <a class="pharmacy_item_phone_link" target="_blank" rel="nofollow, noindex" href="tel:+{{phone_strip($item['phone_'.($i+1)])}}">
                                    {{$item['phone_'.($i+1)]}} @if($item['phone_'.($i+1).'_text']){!! '<br class="pharmacy_item_phone_link_text_breaker">('.$item['phone_'.($i+1).'_text'].')' !!}@endif
                                </a>
                                <div class="Contact_Info_Phone_Icon Contact_Info_Icon_Slider">
                                    @if(isset($item['phone_'.($i+1).'_whatsapp']) && $item['phone_'.($i+1).'_whatsapp'] !== null)
                                    <a class="Contact_Info_Icon_Slider_Item" target="_blank" rel="nofollow, noindex" href="https://wa.me/{{$item['phone_'.($i+1).'_whatsapp']}}">
                                        <img class="Contact_Info_Phone_Icon_Item LazyLoad" data-src="{{ URL::asset('img/svg/whatsapp.svg') }}" target="_blank" rel="nofollow, noindex" src="" alt=""/>
                                    </a>
                                    @endif
                                        @if(isset($item['phone_'.($i+1).'_viber']) && $item['phone_'.($i+1).'_viber'] !== null)
                                    <a class="Contact_Info_Icon_Slider_Item" rel="nofollow, noindex" href="viber://add?number={{$item['phone_'.($i+1).'_viber']}}">
                                        <img class="Contact_Info_Phone_Icon_Item LazyLoad" data-src="{{ URL::asset('img/svg/viber.svg') }}" rel="nofollow, noindex" src="" alt=""/>
                                    </a>
                                    @endif
                                    @if(isset($item['phone_'.($i+1).'_telegram']) && $item['phone_'.($i+1).'_telegram'] !== null)
                                    <a class="Contact_Info_Icon_Slider_Item" href="tg://resolve?domain={{$item['phone_'.($i+1).'_telegram']}}" rel="nofollow, noindex">
                                        <img class="Contact_Info_Phone_Icon_Item LazyLoad" data-src="{{ URL::asset('img/svg/telegram.svg') }}" rel="nofollow, noindex" src="" alt=""/>
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endif
                            @endfor
                    </div>
                </div>
            </div>
        </div>

        {{--<div class="pharmacy_item_info_footer">--}}
            {{--<div class="pharmacy_item_col_left">--}}
                {{--<div class="pharmacy_item_time">--}}
                    {{--<div class="pharmacy_item_svg svg_time">--}}
                        {{--@svg("img/svg/pharm_time.svg")--}}
                    {{--</div>--}}
                    {{--<div class="pharmacy_item_title">--}}
                        {{--ЧАСЫ РАБОТЫ--}}
                    {{--</div>--}}
                    {{--<div class="pharmacy_item_text">--}}
                        {{--{{$item["time_work"]}}--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="pharmacy_item_col_right">--}}
                {{--<div class="btn_print margin-left">--}}
                    {{--<div class="btn_print_svg">--}}
                        {{--@svg("img/svg/pharm_print.svg")--}}
                    {{--</div>--}}
                    {{--<div class="btn_print_text">--}}
                        {{--Печатная версия--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}

    </div>
</div>