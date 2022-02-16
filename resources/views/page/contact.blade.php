@include('layouts.head')
@include('includes.sections.header.header',
	[
	    'header_include'=>"transparent_gheader.header",
	    'header_top_bar' => true,
	]
)
{{--<div class="border_bottom">--}}
{{--    <div class="container">--}}
{{--        @include("includes.sections.header.header-top-bar")--}}
{{--    </div>--}}
{{--</div>--}}

{{--<div class="container container_relative">--}}
{{--    @include("blocks.menu.menu_top.menu")--}}
{{--    @include("blocks.menu.menu_bottom.menu")--}}
{{--</div>--}}

<div class="container contact">
    @component("blocks.title.section_title",
        [
            "data" =>[
                "title" => "<h1><span>Контакты</span></h1>",
                "section_class_title" => "title-gen contact_title wow slideInDown"
            ]
        ])

        <div class="contact_list">
            <div class="contact_col_left">
                <div class="pharmacy_item">
                    <div class="pharmacy_item_top">
                        <div class="pharmacy_item_col_left wow slideInDown">
                            <div class="pharmacy_item_col_title">
                                <div class="pharmacy_item_svg svg_name">
                                    @svg("img/svg/pharm_name.svg")
                                </div>
                                <div class="pharmacy_item_title">
                                    {!! string($strings, 'contact_pharmacy_item_title_1', "Главный офис") !!}
                                </div>
                            </div>

                            @php
                                $sizes = array(
                                                '1' => ['width' => 540, 'relative_path' => 'uploads/design/contacts/apteka-olvia.jpg', 'q'=> 90],
                                                'id' => 'apteka-olvia.jpg',
                                                'tag' => 'DESIGN',
                                              );
                                $mappings = array(
                                                '>320' => '1',
                                                'default' => '1'
                                              );
                                $sizes = Img::img($sizes);
                                $picture = Img::picture_compose($sizes, $mappings, true, '', '', true);
                            @endphp
                            <div class="pharmacy_item_img_photo Background_Is_Picture">
                                {!! $picture !!}
                            </div>
                        </div>
                        <div class="pharmacy_item_col_right wow slideInDown">
                            <div class="pharmacy_item_col_title">
                                <div class="pharmacy_item_svg pharm_map margin-left">
                                    @svg("img/svg/pharm_map.svg")
                                </div>
                                <div class="pharmacy_item_title margin-left">
                                    {!! string($strings, 'contact_pharmacy_item_title_2', "Мы на карте") !!}
                                </div>
                            </div>
                            @php
                                $sizes = array(
                                                '1' => ['width' => 540, 'relative_path' => 'uploads/design/contacts/img-map.jpg', 'q'=> 90],
                                                'id' => 'contacts_img_map',
                                                'tag' => 'DESIGN',
                                              );
                                $mappings = array(
                                                '>320' => '1',
                                                'default' => '1'
                                              );
                                $sizes = Img::img($sizes);
                                $picture = Img::picture_compose($sizes, $mappings, true, '', '', true);
                            @endphp
                            <div class="pharmacy_item_img_map Background_Is_Picture">
                                {!! $picture !!}
                            </div>
                        </div>
                    </div>
                    <div class="pharmacy_item_info">
                        <div class="pharmacy_item_info_top">
                            <div class="pharmacy_item_col_left wow slideInLeft">
                                <div class="pharmacy_item_address">
                                    <div class="pharmacy_item_col_title">
                                        <div class="pharmacy_item_svg svg_address">
                                            @svg("img/svg/pharm_address.svg")
                                        </div>
                                        <div class="pharmacy_item_title">
                                            Адрес
                                        </div>
                                    </div>
                                    <a rel="nofollow,noindex" target="_blank" href="https://yandex.ua/maps/142/donetsk/house/krasnoarmiiska_vulytsia_56/Z04YfgdgT0UDQFptfXV4cH5lbQ==/?l=stv%2Csta&amp;lang=ru&amp;ll=37.800604%2C47.991269&amp;z=17.7" class="pharmacy_item_text text_link">
                                        ул. Красноармейская 56 (р-н «Золотого кольца»)
                                    </a>
                                </div>
                            </div>

                            <div class="pharmacy_item_col_right wow slideInRight">
                                <div class="pharmacy_item_phone">
                                    <div class="pharmacy_item_col_title">
                                        <div class="pharmacy_item_svg svg_phone margin-left">
                                            @svg("img/svg/pharm_phone.svg")
                                        </div>
                                        <div class="pharmacy_item_title margin-left">
                                            Тел.
                                        </div>
                                    </div>

                                    <div class="pharmacy_item_text pharmacy_item_text-column margin-left">
                                        @php
                                        $phones = explode('|', setting('site.phones'));
                                        @endphp
                                        @foreach($phones as $phone)
                                        <div class="Contact_Info_Phone_Container">
                                            <a target="_blank" rel="nofollow, noindex" href="tel:+8-495-649-41-66">{{$phone}}</a>
                                            <div class="Contact_Info_Phone_Icon Contact_Info_Icon_Slider ">
                                                <a class="Contact_Info_Icon_Slider_Item" target="_blank" rel="nofollow, noindex" href="#">
                                                    <img class="Contact_Info_Phone_Icon_Item LazyLoad" data-src="{{ URL::asset('img/svg/whatsapp.svg') }}" target="_blank" rel="nofollow, noindex" src="" alt=""/>
                                                </a>
                                                <a class="Contact_Info_Icon_Slider_Item" rel="nofollow, noindex" href="#">
                                                    <img class="Contact_Info_Phone_Icon_Item LazyLoad" data-src="{{ URL::asset('img/svg/viber.svg') }}" rel="nofollow, noindex" src="" alt=""/>
                                                </a>
                                                <a class="Contact_Info_Icon_Slider_Item" href="#" rel="nofollow, noindex">
                                                    <img class="Contact_Info_Phone_Icon_Item LazyLoad" data-src="{{ URL::asset('img/svg/telegram.svg') }}" rel="nofollow, noindex" src="" alt=""/>
                                                </a>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pharmacy_item_info_footer">
                            <div class="pharmacy_item_col_left wow slideInLeft">
                                <div class="pharmacy_item_email">
                                    <div class="pharmacy_item_col_title">
                                        <div class="pharmacy_item_svg svg_time">
                                            @svg("img/svg/pharm_time.svg")
                                        </div>
                                        <div class="pharmacy_item_title">
                                            EMAIL
                                        </div>
                                    </div>

                                        <a class="pharmacy_item_text text_link" href="{{setting('site.email_contact')}}" rel="nofollow, noindex" target="_blank">{{setting('site.email_contact')}}</a>

                                </div>
                            </div>
                            <div class="pharmacy_item_col_right wow slideInUp">
                                <div class="pharmacy_item_time margin-left">
                                    <div class="pharmacy_item_col_title">
                                        <div class="pharmacy_item_svg svg_time">
                                            @svg("img/svg/pharm_time.svg")
                                        </div>
                                        <div class="pharmacy_item_title">
                                            ЧАСЫ РАБОТЫ
                                        </div>
                                    </div>
                                    <div class="pharmacy_item_text">
                                        {!! string($strings, 'global_work_time', 'placeholder') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--<div class="pharmacy_item_info_btnPrint">--}}
                            {{--<div class="pharmacy_item_col_left">--}}
                                {{--<div class="btn_print">--}}
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
            </div>

            <div class="contact_col_right wow slideInRight">
                @include('blocks.forms.contact_form.contact_form')
            </div>
        </div>
    @endcomponent
</div>
@include('blocks.modal_basket.modal_basket')
@include('blocks.popup.popup')
@include('layouts.footer')