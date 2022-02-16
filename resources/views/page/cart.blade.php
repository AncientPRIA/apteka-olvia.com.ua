@include('layouts.head')
@include('includes.sections.header.header',
	[
        'header_include'=>"transparent_gheader.header",
        'header_top_bar' => true,
        'params' => [
		    'bg' => 'design/home/bg.png',
		    'breadcrumbs_items'=>[
				[
					"href"=>"/",
					"title"=>"Главная"
				],
				[
					"title"=>string($strings, 'breadcrumbs_cart', "Корзина")
				],
			],
		]
	]
)

{{--<div class="border_bottom">--}}
{{--    <div class="container">--}}
{{--        @include("includes.sections.header.header-top-bar")--}}
{{--    </div>--}}
{{--</div>--}}

<div class="container cart_section_list">

    <div class="cart_col_left">
        @component("blocks.title.section_title",
            [
                "data" =>[
                    "title" => "<span>Корзина</span>",
                    "section_class_title" => "title-gen title_cart"
                ]
            ])

            <div class="cart_items_list">
                {!!$cart_product_list !!}
            </div>

            <div class="modal_basket_totalPrice">
                Итого: <span class="Total_Price">0</span>
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     viewBox="0 0 330 330" style="enable-background:new 0 0 330 330;" xml:space="preserve">
                    <path id="XMLID_449_" d="M180,170c46.869,0,85-38.131,85-85S226.869,0,180,0c-0.183,0-0.365,0.003-0.546,0.01h-69.434
                        c-0.007,0-0.013-0.001-0.019-0.001c-8.284,0-15,6.716-15,15v0.001V155v45H80c-8.284,0-15,6.716-15,15s6.716,15,15,15h15v85
                        c0,8.284,6.716,15,15,15s15-6.716,15-15v-85h55c8.284,0,15-6.716,15-15s-6.716-15-15-15h-55v-30H180z M180,30.01
                        c0.162,0,0.324-0.003,0.484-0.008C210.59,30.262,235,54.834,235,85c0,30.327-24.673,55-55,55h-55V30.01H180z"/>
                </svg>
            </div>

        @endcomponent
    </div>

    <div class="cart_col_right">
        @component("blocks.title.section_title",
            [
                "data" =>[
                    "title" => "<span>Данные заказчика</span>",
                    "section_class_title" => "title-gen title_cart"
                ]
            ])

            <form class="cart_form_group" id="form_cart_order">
                @php
                    $input_param=[
                        "name"=>"name",
                        "atr" =>"autocomplete='off'",
                        "form_group_class"=>"form-name",
                        "input_class"=>"",
                        "title"=>"Имя*",
                        "value" => $user->name ?? '',
                        "validation" => "required|minlength:3|maxlength:255",
                    ];
                @endphp
                @include("blocks.input.input")

                @php
                    $input_param=[
                        "name"=>"phone",
                        "atr" =>"autocomplete='off'",
                        "form_group_class"=>"form-phone",
                        "input_class"=>"",
                        "title"=>"Ваш номер телефона*",
                        "value" => $user->phone ?? '',
                        "validation" => "required|minlength:6",

                    ];
                @endphp
                @include("blocks.input.input")

{{--                @php--}}
{{--                    $input_param=[--}}
{{--                        "name"=>"comment",--}}
{{--                        "atr" =>"",--}}
{{--                        "form_group_class"=>"form-comment",--}}
{{--                        "input_class"=>"",--}}
{{--                        "title"=>"Комментарий*",--}}
{{--                        "validation" => "maxlength:1000",--}}
{{--                    ];--}}
{{--                @endphp--}}
{{--                @include("blocks.input.input")--}}

                <div class="form-group form-comment" data-validation="maxlength:1000">
                    <div class="input-container">
                        <label class="label" for="comment">Комментарий</label>
                        <textarea class=" input input_textarea autofill-disable comment_input" value="" autocomplete='off' name="comment" type="text"></textarea>
                    </div>
                    <div class="input_error"></div>
                </div>

                <div class="form-group hidden input_mgb">
                    <div class="input-container">
                        <input class="input autofill-disable"   readonly value="{{$user->id ?? ''}}" name="user_id" type="hidden">
                    </div>
                </div>

                @php
                    $btn_param=[
                        "name"=>"btn",
                        "btn_class"=>"tab_btn btn_white submit",
                        "text"=>"Отправить заказ"
                    ];
                @endphp

	            <div class="container-select">

		            @php
			            $input_param=[
							"name"=>"adress",
							"atr" =>"autocomplete='off'",
							"form_group_class"=>"form-adr dn",
							"input_class"=>"street-input",
							"title"=>"Город и ул.",
							"value" => '',
							"validation" => "required|minlength:1",
						];
		            @endphp
		            @include("blocks.input.input")

		            <div class="select-wrapper">
			            <div class="select-value">
				            Выберите город и адрес аптеки*
			            </div>
			            <div class="select-list">
				            @foreach($cities as $city)
								<div class="select-list-item select-item__city" data-city="{{$city['name']}}|{{$city['id']}}">
									{{$city['name']}}
								</div>
				            {{--{{dd($city->shops)}}--}}
								@foreach($city->shops as $shop)
									<div class="select-list-item select-item__street" data-city="{{$city['name']}}|{{$city['id']}}" data-id="{{$shop['address']}}|{{$shop['id']}}" >
										{{$shop['address']}}
									</div>
					            @endforeach
				            @endforeach
			            </div>
		            </div>
	            </div>

                @include("blocks.btn.btn")
            </form>

        @endcomponent
    </div>


</div>

@include('blocks.modal_basket.modal_basket')
@include('blocks.popup.popup')
@include('layouts.footer')