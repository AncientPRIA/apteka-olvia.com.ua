
@include('layouts.head')
@include('includes.sections.header.header',
	[
        'header_include'=>"transparent_gheader.header",
        'header_top_bar' => true,
        'params' =>[
			'breadcrumbs_items'=> $breadcrumbs
		]
	]
)

<script>
    var user_id = {{$user->id ?? 'false'}}
    var product_id = "{{$current_product->id}}";
    var reviews_page = 1;
</script>
{{-- MOBILE VERSION --}}
{{--@if(preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]))--}}
@if(Browser::isMobile())
	<div class="container mobail mobail-single Product_Item_Fn" data-product-id = {{$current_product['id']}}>
		<div class="single-nav-fixed">
			<div class="single-nav-fixed__item">
				<div class="btn-back">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20.33 9.93"><defs><style>.ebafb0ad-deea-4aa9-a9ea-281e82e65a47{fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:2.5px;fill-rule:evenodd;}</style></defs><title>lРесурс 1</title><g id="f1b2205b-12ef-4ada-8476-1c7f1eb70f14" data-name="Слой 2"><g id="bde1870b-ff48-40ef-bf6b-538e2d1c70cf" data-name="Layer 1"><path class="ebafb0ad-deea-4aa9-a9ea-281e82e65a47" d="M19.08,5H1.41M5,1.25,1.25,5M5,8.68,1.25,5"/></g></g></svg>
				</div>
			</div>

			<div class="single-nav-fixed__item">
				<div class="btn_like btn_product_share" >
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 17.26 18.83"><defs><style>.b8d9c341-965d-4edd-b2d9-50d184a84590{fill:#ccc;fill-rule:evenodd;}</style></defs><title>lРесурс 2</title><g id="bda2cbb2-3f4a-44b9-ba30-2c6b133bcc74" data-name="Слой 2"><g id="ba096281-30b1-4490-94eb-df58f07596a9" data-name="Layer 1"><path class="b8d9c341-965d-4edd-b2d9-50d184a84590" d="M14.12,6.28a3.12,3.12,0,0,1-2.3-1L6.14,8.5a3.06,3.06,0,0,1,0,1.83l5.68,3.23A3.13,3.13,0,1,1,11,15.69a3.06,3.06,0,0,1,.09-.74L5.32,11.67a3.09,3.09,0,0,1-2.18.88,3.14,3.14,0,1,1,0-6.27,3.09,3.09,0,0,1,2.18.88l5.75-3.28A3.06,3.06,0,0,1,11,3.14a3.14,3.14,0,1,1,3.14,3.14Z"/></g></g></svg>
				</div>
				<div data-product-id="{{$current_product['id']}}" class="btn_like btn_product {{ user_verified()?"Add_Favorites":"" }}" @if (!user_verified()) onclick="popup_show({cls:'Autf',scrollOff:'body'})" @endif >
					@svg("img/svg/heart_product_btn.svg")
				</div>
				<div class="btn_add_product btn_product" data-product-id="{{$current_product['id']}}">
					@svg("img/svg/add_product_btn.svg")
				</div>
			</div>
		</div>
		<div class="product_img">
			{{--                    {!! $picture !!}--}}

			<div class="slider-image-prev">
				<img src="{{asset("img/svg/arrow-left-noshaft-rounded.svg")}}" alt="arrow-left">
			</div>

			<div class="slider-image-next">
				<img src="{{asset("img/svg/arrow-right-noshaft-rounded.svg")}}" alt="arrow-right">
			</div>

			<div class="slider_product_img" >

				@for($i=0;$i<count($img_array);$i++)
					@php
                        $sizes = array(
                                        '1' => ['width' => 376, 'relative_path' => 'uploads/'.$img_array[$i], 'q'=> 90, 'watermark' => config('image.watermark.product')],
                                        'id' => 'product'.$img_array[$i],
                                        'get_dimensions' => true
                        );
                        $mappings = array(
                                        '>320' => '1',
                                        'default' => '1'
                                      );
                        $sizes = Img::img($sizes);

                        $picture = Img::picture_compose($sizes, $mappings, [
                            'is_hidden' => false,
                            'classes' => '',
                            'alt' => $current_product['title'].'_photo_'.$i,
                            'is_lazy' => true,
                            'fullscreen' => true,
                            'placeholder' => [
                                'plain' => asset('uploads/product_image_placeholder.png'),
                            ]
                        ]);
					@endphp

					<div class="image_item">
						{!! $picture !!}
					</div>
					{{--                            <img src="https://olvia.pria.digital/uploads/{!! $img_array[$i] !!}" alt="" data-src="https://olvia.pria.digital/uploads/{!! $img_array[$i] !!}">--}}

					{{--                            <div class="image_item Background_Is_Picture" data-src="https://olvia.pria.digital/uploads/{!! $img_array[$i] !!}">--}}
					{{--                                {!! $picture !!}--}}
					{{--                            </div>--}}
				@endfor

			</div>

			@if(isset($current_product["discount"]) && $current_product["discount"] !== 0)
				<div class="product_discount">
					<div class="discount_svg">
						@svg("img/svg/discount_svg.svg")
					</div>
					<span>{{$current_product["discount"]}}</span>
				</div>
				@php
					$old_price = $current_product['price'];
					$current_product["price"] = $price_discounted = round($current_product['price'] - ($current_product['price'] * ($current_product["discount"] / 100)));
				@endphp
			@endif
		</div>

		<div class="mobail-product-header">
			<div class="mobail-product-header__top">
				<div class="product_code">Артикул: {{ $current_product["sku"] }}</div>
{{--				<div class="comment-counter">--}}
{{--					0--}}
{{--				</div>--}}
				<div class="product_favourite_star_list">
					@for($i=0;$i< 5; $i++)
						<div class="product_star_icon @if($i < $rating_average){{'active'}}@endif" data-rating="{{$i+1}}">
							<svg class="star" viewBox="0 0 20 20" >
								<path d="M10.53 16.081l6.5 3.926-1.72-7.4 5.74-4.98-7.56-.65-2.96-6.981-2.96 6.981-7.56.65 5.74 4.98-1.72 7.4z" style="fill-rule:nonzero;"></path>
							</svg>
						</div>
					@endfor
				</div>
				{{--<div class="product-star">--}}
				{{--<span class="star-active">★</span>--}}
				{{--<span class="star-active">★</span>--}}
				{{--<span class="star-active">★</span>--}}
				{{--<span class="star-active">★</span>--}}
				{{--<span>★</span>--}}
				{{--</div>--}}
			</div>

			<div class="mobail-product-title">
				{{ $current_product["title"] }}
			</div>

		</div>

		<div class="mobail-product-buy">
			<div class="product_price">
				@if(isset($old_price))
					<div class="product_old_price">
						{{$old_price}}

                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                             viewBox="0 0 330 330" style="enable-background:new 0 0 330 330;" xml:space="preserve">
                            <path id="XMLID_449_" d="M180,170c46.869,0,85-38.131,85-85S226.869,0,180,0c-0.183,0-0.365,0.003-0.546,0.01h-69.434
                                c-0.007,0-0.013-0.001-0.019-0.001c-8.284,0-15,6.716-15,15v0.001V155v45H80c-8.284,0-15,6.716-15,15s6.716,15,15,15h15v85
                                c0,8.284,6.716,15,15,15s15-6.716,15-15v-85h55c8.284,0,15-6.716,15-15s-6.716-15-15-15h-55v-30H180z M180,30.01
                                c0.162,0,0.324-0.003,0.484-0.008C210.59,30.262,235,54.834,235,85c0,30.327-24.673,55-55,55h-55V30.01H180z"/>
                                    <g>
                        </svg>
					</div>
				@endif

                    <span class="price-item-product_fn">Стоимость уточняйте</span>
                {{--TODO: PRICE VALUE. UNCOMMENT, AFTER SYNC--}}
				{{--<span class="price-item-product_fn">{!! $current_product["price"] !!}</span>--}}

                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                         viewBox="0 0 330 330" style="enable-background:new 0 0 330 330;" xml:space="preserve">
                            <path id="XMLID_449_" d="M180,170c46.869,0,85-38.131,85-85S226.869,0,180,0c-0.183,0-0.365,0.003-0.546,0.01h-69.434
                                c-0.007,0-0.013-0.001-0.019-0.001c-8.284,0-15,6.716-15,15v0.001V155v45H80c-8.284,0-15,6.716-15,15s6.716,15,15,15h15v85
                                c0,8.284,6.716,15,15,15s15-6.716,15-15v-85h55c8.284,0,15-6.716,15-15s-6.716-15-15-15h-55v-30H180z M180,30.01
                                c0.162,0,0.324-0.003,0.484-0.008C210.59,30.262,235,54.834,235,85c0,30.327-24.673,55-55,55h-55V30.01H180z"/>
                        <g>
                        </svg>
			</div>

			<div class="product_btn_group">
				<div class="product_buy">
					<div class="btn_buy_product btn_product" data-product-id="{{$current_product['id']}}" onclick="popup_show({cls:'Call_Back',scrollOff:'body'});">
						@svg("img/svg/add_product_btn.svg")
					</div>
					<div class="product_buy_text">
						Купить в один клик
					</div>
				</div>
			</div>
		</div>

		<div class="product_parameters">
			<div class="product_parameters_title">
				Основные параметры
			</div>

			<div class="product_parameters_list">
				@foreach($parameters as $key=>$value)
					@if((isset($value) && $value !== '') || (is_array($value) && count($value) > 0))
						<div class="product_parameters_item">
							<div class="product_parameters_item_key">
								{{$key}}
							</div>
							@if(is_array($value))
								<ul class="product_parameters_item_nested_list">
									@foreach($value as $value_key=>$value_item)
										@if(isset($value_item))
											<li class="product_parameters_item_nested_item">
												<div class="product_parameters_item_nested_key">{{$value_key}}</div>
												<div class="product_parameters_item_nested_value">{{$value_item}}</div>
											</li>
										@endif
									@endforeach
								</ul>
							@else
								<div class="product_parameters_item_value">
									{{$value}}
								</div>
							@endif

						</div>
					@endif
				@endforeach
			</div>
		</div>

		@if($current_product["body"] !== null && $current_product["body"] !== "")
			<div class="product_description">
				{!! $current_product["body"] !!}
			</div>

			@php
				$btn_param=[
					"name"=>"btn",
					"btn_class"=>"tab_btn btn-body-view",
					"text"=>"Развернуть"
				];
			@endphp
			@include("blocks.btn.btn")
		@endif
		<div class="cities-container">

			<div class="cities-header">
				<div class="product_address_title">
					Уточнить наличие
				</div>

				<div class="select">
					<select name="#" id="select_city_mobail" class="product_city_select">
						@foreach($cities as $city)
							<option value="{{$city["id"]}}">{{$city["name"]}}</option>
						@endforeach
					</select>
					<div class="select_arrow">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8.66 4.74"><defs><style>.efc19b84-cc1e-4ccf-bd42-5026869b54b2{fill:none;stroke:#999;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.2px;}</style></defs><g id="f52205ba-de68-4763-9a63-0e4a62afff5a" data-name="Слой 2"><g id="ac8984cd-dd04-4f62-9083-d1a72755c459" data-name="Layer 1"><line class="efc19b84-cc1e-4ccf-bd42-5026869b54b2" x1="8.06" y1="0.6" x2="4.33" y2="4.14"/><line class="efc19b84-cc1e-4ccf-bd42-5026869b54b2" x1="0.6" y1="0.6" x2="4.33" y2="4.14"/></g></g></svg>
					</div>
				</div>
			</div>

			<div class="slider-city-arrow">
				<div class="slider-city-prev">
					<img src="{{asset("img/svg/arrow-left-noshaft-rounded.svg")}}" alt="arrow-left">
				</div>

				<div class="slider-city-next">
					<img src="{{asset("img/svg/arrow-right-noshaft-rounded.svg")}}" alt="arrow-right">
				</div>
			</div>


			<div class="slider-cities">
				@foreach($cities as $city)
					<div class="city-items" {!! $city["id"]!=1? 'style="display:none"':""!!} data-city="{{$city["id"]}}">
						@foreach($city->shops as $item)
							<div class="product_address_item" data-cityid="{{$item["city_id"]}}">
								@php
									$sizes = array(
													'1' => ['width' => 376, 'relative_path' => 'uploads/'.$item["image"], 'q'=> 90],
													'id' => 'address_img'.$item["image"],
												  );
									$mappings = array(
													'>320' => '1',
													'default' => '1'
												  );
									$sizes = Img::img($sizes);

									$picture = Img::picture_compose($sizes, $mappings, true, '', '', true);
								@endphp
								<div class="product_address_item_img Background_Is_Picture">
									{!! $picture !!}
								</div>

								<div class="product_address_item_info">
									<div class="product_address_item_address">
										<div class="product_address_item_title">
											адрес
										</div>
										<div class="product_address_item_address_group">
											{{$item["address"]}}
										</div>
									</div>
									<div class="product_address_item_phone">
										<div class="product_address_item_title">
											Тел.
										</div>
										<div class="product_address_item_phone_group">
											@for($i=0; $i <= 1; $i++)
												@if(isset($item['phone_'.($i+1)]) && $item['phone_'.($i+1)] !== null)
													<div class="product_address_item_phone_container">
														<a class="product_address_item_phone_link" target="_blank" rel="nofollow, noindex" href="tel:+{{phone_strip($item['phone_'.($i+1)])}}">{{$item['phone_'.($i+1)]}}</a>
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
									<div class="product_address_item_workTime">
										<div class="product_address_item_workTime_svg">
											@svg("img/svg/pharm_time.svg")
										</div>
										<div class="product_address_item_workTime_text">
											{{$item["opening_hours"]}}
										</div>
									</div>
									<div class="product_address_item_btn">
										<button class="tab_btn check_availability" data-idshop="{{$item["id"]}}" data-idproduct="{{$current_product["id"]}}">Уточнить наличие</button>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				@endforeach
			</div>

		</div>

		<div class="product_review">
			<div class="product_review_title">
				Оставить отзыв
			</div>
			@if(user_verified() !== false)
				<div class="product_user_star_list">
					Оценить:&nbsp;
					<div class="user_star_list">
						@for($i=0;$i< 5;$i++)
							<div class="product_user_star_icon star_btn @if($i < $user_rating){{'active'}}@endif"" data-rating="{{$i+1}}">
							<svg class="star" viewBox="0 0 20 20" >
								<path d="M10.53 16.081l6.5 3.926-1.72-7.4 5.74-4.98-7.56-.65-2.96-6.981-2.96 6.981-7.56.65 5.74 4.98-1.72 7.4z" style="fill-rule:nonzero;"></path>
							</svg>
					</div>
					@endfor
				</div>
			@endif

			<form id="product_item_review" class="product_item_review" action="#">
				@php
					$input_param=[
						"name"=>"name",
						"atr" =>"",
						"form_group_class"=>"form_group_name",
						"input_class"=>"name_input",
						"title"=>"Имя*",
						"validation" => "required|minlength:3",
						"value" => $user->name ?? '',
					];
				@endphp
				@include("blocks.input.input")

				@php
					$input_param=[
						"name"=>"email",
						"atr" =>"",
						"form_group_class"=>"form_group_email",
						"input_class"=>"email_input",
						"title"=>"Email*",
						"validation" => "required|email|maxlength:32",
						"value" => $user->email ?? '',
					];
				@endphp
				@include("blocks.input.input")

				<div class="form-group form_group_review" data-validation="required|maxlength:1000">
					<div class="input-container">
						<label class="label" for="review">Отзыв*</label>
						<textarea class=" input input_textarea autofill-disable review_input" value="" name="review" type="text"></textarea>
					</div>
					<div class="input_error"></div>
				</div>

				<div class="form-group input_mgb hidden">
					<div class="input-container">
						<input class="input autofill-disable" readonly value="{{$current_product->id}}" name="product_id" type="text" >
					</div>
				</div>

				@php
					$btn_param=[
						"name"=>"btn",
						"btn_class"=>"tab_btn btn-submit",
						"text"=>"Отправить"
					];
				@endphp
				@include("blocks.btn.btn")
			</form>
		</div>
		@include("blocks.reviews.reviews_list", ["items" => $reviews])
	</div>
	<div class="modal-share">
		<div class="product_social">
			<div class="product_social_title">
				Поделиться в соц.сетях
			</div>
			<div class="product_social_list">

				@include("blocks.social.share.social_share", [
							"social_list_class"=>"top-social social-list",
							"url" =>"#",
							"social"=>[
								[
									"social"=>"vk",
									"path_svg" => "img/svg/vk.svg",
									"social_item_class" => "social-share__item social-share__vk",
								],
								[
									"social"=>"ok",
									"path_svg" => "img/svg/ok.svg",
									"social_item_class" => "social-share__item social-share__ok",
								],
								[
									"social"=>"tg",
									"path_svg" => "img/svg/telegram.svg",
									"social_item_class" => "social-share__item social-share__tg",
								]
							],

				])
			</div>
		</div>
	</div>

@else
{{-- DESKTOP VERSION --}}
<div class="container desktop Product_Item_Fn" data-product-id = {{$current_product['id']}}>
    @component("blocks.title.section_title",
        [
            "data" =>[
                "title" => "<h1>".$current_product["title"]."</h1>",
                "section_class_title" => "title-gen single_product_title"
            ]
        ])

        <div class="product_info_list">
            <div class="product_col_left">
                <div class="product_img">
{{--                    {!! $picture !!}--}}

                    <div class="slider-image-prev">
                        <img src="{{asset("img/svg/arrow-left-noshaft-rounded.svg")}}" alt="arrow-left">
                    </div>

                    <div class="slider-image-next">
                        <img src="{{asset("img/svg/arrow-right-noshaft-rounded.svg")}}" alt="arrow-right">
                    </div>

                    <div class="slider_product_img" >

                        @for($i=0;$i<count($img_array);$i++)
{{--                            {{dd($img_array[$i])}}--}}
                            @php
                                $sizes = array(
                                                '1' => ['width' => 376, 'relative_path' => 'uploads/'.$img_array[$i], 'q'=> 90, 'watermark' => config('image.watermark.product')],
                                                'id' => 'product'.$img_array[$i],
                                                'get_dimensions' => true
                                              );
                                $mappings = array(
                                                '>320' => '1',
                                                'default' => '1'
                                              );
                                $sizes = Img::img($sizes);

                                $picture = Img::picture_compose($sizes, $mappings, [
                                    'is_hidden' => false,
                                    'classes' => '',
                                    'alt' => $current_product['title'].'_photo_'.$i,
                                    'is_lazy' => true,
                                    'fullscreen' => true,
                                    'placeholder' => [
                                        'plain' => asset('uploads/product_image_placeholder.png'),
                                    ]
                                ]);
                            @endphp

                            <div class="image_item">
                                {!! $picture !!}
                            </div>
{{--                            <img src="https://olvia.pria.digital/uploads/{!! $img_array[$i] !!}" alt="" data-src="https://olvia.pria.digital/uploads/{!! $img_array[$i] !!}">--}}

{{--                            <div class="image_item Background_Is_Picture" data-src="https://olvia.pria.digital/uploads/{!! $img_array[$i] !!}">--}}
{{--                                {!! $picture !!}--}}
{{--                            </div>--}}
                        @endfor

                    </div>

                    @if(isset($current_product["discount"]) && $current_product["discount"] !== 0)
                        <div class="product_discount">
                            <div class="discount_svg">
                                @svg("img/svg/discount_svg.svg")
                            </div>
                            <span>{{$current_product["discount"]}}</span>
                        </div>
                        @php
                            $old_price = $current_product['price'];
                            $current_product["price"] = $price_discounted = round($current_product['price'] - ($current_product['price'] * ($current_product["discount"] / 100)));
                        @endphp
                    @endif
                </div>
                <div class="product_info">
                    <div class="product_top_info">
                        <div class="product_code">
                            Артикул: {{ $current_product["sku"] }}
                        </div>
                        <div class="product_favourite_star_list">
                            @for($i=0;$i< 5; $i++)
                                <div class="product_star_icon @if($i < $rating_average){{'active'}}@endif" data-rating="{{$i+1}}">
                                    <svg class="star" viewBox="0 0 20 20" >
                                        <path d="M10.53 16.081l6.5 3.926-1.72-7.4 5.74-4.98-7.56-.65-2.96-6.981-2.96 6.981-7.56.65 5.74 4.98-1.72 7.4z" style="fill-rule:nonzero;"></path>
                                    </svg>
                                </div>
                            @endfor
                        </div>
                        @if(user_verified() !== false)
                        <div class="product_user_star_list">
                            Ваша оценка:&nbsp;
                            <div class="user_star_list">
                                @for($i=0;$i< 5;$i++)
                                    <div class="product_user_star_icon star_btn @if($i < $user_rating){{'active'}}@endif"" data-rating="{{$i+1}}">
                                        <svg class="star" viewBox="0 0 20 20" >
                                            <path d="M10.53 16.081l6.5 3.926-1.72-7.4 5.74-4.98-7.56-.65-2.96-6.981-2.96 6.981-7.56.65 5.74 4.98-1.72 7.4z" style="fill-rule:nonzero;"></path>
                                        </svg>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="product_price">
                        @if(isset($old_price))
                            {{--<div class="product_old_price">
                                {{$old_price}}

                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                     viewBox="0 0 330 330" style="enable-background:new 0 0 330 330;" xml:space="preserve">
                                    <path id="XMLID_449_" d="M180,170c46.869,0,85-38.131,85-85S226.869,0,180,0c-0.183,0-0.365,0.003-0.546,0.01h-69.434
                                        c-0.007,0-0.013-0.001-0.019-0.001c-8.284,0-15,6.716-15,15v0.001V155v45H80c-8.284,0-15,6.716-15,15s6.716,15,15,15h15v85
                                        c0,8.284,6.716,15,15,15s15-6.716,15-15v-85h55c8.284,0,15-6.716,15-15s-6.716-15-15-15h-55v-30H180z M180,30.01
                                        c0.162,0,0.324-0.003,0.484-0.008C210.59,30.262,235,54.834,235,85c0,30.327-24.673,55-55,55h-55V30.01H180z"/>

                                </svg>
                            </div>--}}
                        @endif
                            {{--TODO: PRICE VALUE. UNCOMMENT, AFTER SYNC--}}
                            <span class="price-item-product_fn">Стоимость уточняйте</span>
                        {{--<span class="price-item-product_fn">{!! $current_product["price"] !!}</span>--}}
                            {{--<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                 viewBox="0 0 330 330" style="enable-background:new 0 0 330 330;" xml:space="preserve">
                                    <path id="XMLID_449_" d="M180,170c46.869,0,85-38.131,85-85S226.869,0,180,0c-0.183,0-0.365,0.003-0.546,0.01h-69.434
                                        c-0.007,0-0.013-0.001-0.019-0.001c-8.284,0-15,6.716-15,15v0.001V155v45H80c-8.284,0-15,6.716-15,15s6.716,15,15,15h15v85
                                        c0,8.284,6.716,15,15,15s15-6.716,15-15v-85h55c8.284,0,15-6.716,15-15s-6.716-15-15-15h-55v-30H180z M180,30.01
                                        c0.162,0,0.324-0.003,0.484-0.008C210.59,30.262,235,54.834,235,85c0,30.327-24.673,55-55,55h-55V30.01H180z"/>

                                </svg>--}}
                    </div>

                    <div class="product_btn_group">

                        <div class="product_favorite">
                            <div data-product-id="{{$current_product['id']}}" class="btn_like btn_product {{ user_verified()?"Add_Favorites":"" }}" @if (!user_verified()) onclick="popup_show({cls:'Autf',scrollOff:'body'})" @endif >
                                @svg("img/svg/heart_product_btn.svg")
                            </div>
                            <div class="product_favorite_text">
                                В избранное
                            </div>
                        </div>

                        <div class="product_buy">
                            <div class="btn_buy_product btn_product" data-product-id="{{$current_product['id']}}" onclick="popup_show({cls:'Call_Back',scrollOff:'body'});">
                                @svg("img/svg/add_product_btn.svg")
                            </div>
                            <div class="product_buy_text">
                                Купить в один клик
                            </div>
                        </div>

                        <div class="product_add">
                            <div class="btn_add_product btn_product btn_add_anim_single" data-product-id="{{$current_product['id']}}">
                                @svg("img/svg/add_product_btn.svg")
                            </div>
                            <div class="product_add_text">
                                В корзину
                            </div>
                        </div>
                    </div>

                    <div class="product_social">
                        <div class="product_social_title">
                            Поделиться в соц.сетях
                        </div>
                        <div class="product_social_list">

                            @include("blocks.social.share.social_share", [
                                        "social_list_class"=>"top-social social-list",
                                        "url" =>"#",
                                        "social"=>[
                                            [
                                                "social"=>"vk",
                                                "path_svg" => "img/svg/vk.svg",
                                                "social_item_class" => "social-share__item social-share__vk",
                                            ],
                                            [
                                                "social"=>"ok",
                                                "path_svg" => "img/svg/ok.svg",
                                                "social_item_class" => "social-share__item social-share__ok",
                                            ],
                                            [
                                                "social"=>"tg",
                                                "path_svg" => "img/svg/telegram.svg",
                                                "social_item_class" => "social-share__item social-share__tg",
                                            ]
                                        ],

                            ])
                        </div>
                    </div>


                </div>
            </div>

            <div class="product_col_right">
                <div class="product_parameters">
                    <div class="product_parameters_title">
                        Основные параметры
                    </div>

                    <div class="product_parameters_list">
                        @foreach($parameters as $key=>$value)
                            @if((isset($value) && $value !== '') || (is_array($value) && count($value) > 0))
                                <div class="product_parameters_item">
                                            <div class="product_parameters_item_key">
                                                {{$key}}
                                            </div>
                                            @if(is_array($value))
                                            <ul class="product_parameters_item_nested_list">
                                                @foreach($value as $value_key=>$value_item)
                                                    @if(isset($value_item))
                                                    <li class="product_parameters_item_nested_item">
                                                        <div class="product_parameters_item_nested_key">{{$value_key}}</div>
                                                        <div class="product_parameters_item_nested_value">{{$value_item}}</div>
                                                    </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                            @else
                                                <div class="product_parameters_item_value">
                                                    {{$value}}
                                                </div>
                                            @endif

                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="product_description">
                    {!! $current_product["body"] !!}
                </div>
            </div>
        </div>

        <div class="product_address_review">
            <div class="product_col_left">
                <div class="product_address">

                    <div class="product_address_title">
                        Уточнить наличие
                    </div>
                    <div class="select">
                        <select name="#" id="select_city" class="product_city_select">
                            @foreach($cities as $city)
                                <option value="{{$city["id"]}}">{{$city["name"]}}</option>
                            @endforeach
                        </select>
                        <div class="select_arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8.66 4.74"><defs><style>.efc19b84-cc1e-4ccf-bd42-5026869b54b2{fill:none;stroke:#999;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.2px;}</style></defs><g id="f52205ba-de68-4763-9a63-0e4a62afff5a" data-name="Слой 2"><g id="ac8984cd-dd04-4f62-9083-d1a72755c459" data-name="Layer 1"><line class="efc19b84-cc1e-4ccf-bd42-5026869b54b2" x1="8.06" y1="0.6" x2="4.33" y2="4.14"/><line class="efc19b84-cc1e-4ccf-bd42-5026869b54b2" x1="0.6" y1="0.6" x2="4.33" y2="4.14"/></g></g></svg>
                        </div>
                    </div>


                    <div class="product_address_list">
                        @foreach($cities as $city)
                            @foreach($city->shops as $item)

                                <div class="product_address_item" data-cityid="{{$item["city_id"]}}">
                                    @php
                                        $sizes = array(
                                                        '1' => ['width' => 376, 'relative_path' => 'uploads/'.$item["image"], 'q'=> 90],
                                                        'id' => 'address_img'.$item["image"],
                                                      );
                                        $mappings = array(
                                                        '>320' => '1',
                                                        'default' => '1'
                                                      );
                                        $sizes = Img::img($sizes);

                                        $picture = Img::picture_compose($sizes, $mappings, true, '', '', true);
                                    @endphp
                                    <div class="product_address_item_img Background_Is_Picture">
                                        {!! $picture !!}
                                    </div>

                                    <div class="product_address_item_info">
                                        <div class="product_address_item_address">
                                            <div class="product_address_item_title">
                                                адрес
                                            </div>
                                            <div class="product_address_item_address_group">
                                                {{$item["address"]}}
                                            </div>
                                        </div>
                                        <div class="product_address_item_phone">
                                            <div class="product_address_item_title">
                                                Тел.
                                            </div>
                                            <div class="product_address_item_phone_group">
                                                @for($i=0; $i <= 1; $i++)
                                                    @if(isset($item['phone_'.($i+1)]) && $item['phone_'.($i+1)] !== null)
                                                        <div class="product_address_item_phone_container">
                                                            <a class="product_address_item_phone_link" target="_blank" rel="nofollow, noindex" href="tel:+{{phone_strip($item['phone_'.($i+1)])}}">{{$item['phone_'.($i+1)]}}</a>
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
                                        <div class="product_address_item_workTime">
                                            <div class="product_address_item_workTime_svg">
                                                @svg("img/svg/pharm_time.svg")
                                            </div>
                                            <div class="product_address_item_workTime_text">
                                                {{$item["opening_hours"]}}
                                            </div>
                                        </div>
                                        <div class="product_address_item_btn">
                                            <button class="tab_btn check_availability" data-idshop="{{$item["id"]}}" data-idproduct="{{$current_product["id"]}}">Уточнить наличие</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        @endforeach
                    </div>
                </div>

            </div>

            <div class="product_col_right">
                <div class="product_review">
                    <div class="product_review_title">
                        Оставить отзыв
                    </div>

                    <form id="product_item_review" class="product_item_review" action="#">
                        @php
                            $input_param=[
                                "name"=>"name",
                                "atr" =>"",
                                "form_group_class"=>"form_group_name",
                                "input_class"=>"name_input",
                                "title"=>"Имя*",
                                "validation" => "required|minlength:3",
                                "value" => $user->name ?? '',
                            ];
                        @endphp
                        @include("blocks.input.input")

                        @php
                            $input_param=[
                                "name"=>"email",
                                "atr" =>"",
                                "form_group_class"=>"form_group_email",
                                "input_class"=>"email_input",
                                "title"=>"Email*",
                                "validation" => "required|email|maxlength:32",
                                "value" => $user->email ?? '',
                            ];
                        @endphp
                        @include("blocks.input.input")

                        <div class="form-group form_group_review" data-validation="required|maxlength:1000">
                            <div class="input-container">
                                <label class="label" for="review">Отзыв*</label>
                                <textarea class=" input input_textarea autofill-disable review_input" value="" name="review" type="text"></textarea>
                            </div>
                            <div class="input_error"></div>
                        </div>

                        <div class="form-group input_mgb hidden">
                            <div class="input-container">
                                <input class="input autofill-disable" readonly value="{{$current_product->id}}" name="product_id" type="text" >
                            </div>
                        </div>

                        @php
                            $btn_param=[
                                "name"=>"btn",
                                "btn_class"=>"tab_btn btn-submit",
                                "text"=>"Отправить"
                            ];
                        @endphp
                        @include("blocks.btn.btn")
                    </form>
                </div>
            @include("blocks.reviews.reviews_list", ["items" => $reviews])
            </div>
        </div>
    @endcomponent
</div>

@endif

<div class="product_sliders">
    {{--  слайдер с товарами--}}
    @php
        $product_list = [

            [
                "image" => "product_item.png",
                "price" => "351",
                "old_price" => "300",
                "discount" => "5",
                "name" => "Дезодорант",
                "description" => "Дезодорант для тела DRYDRY (Драй драй) 35 мл",
                "id"=>1,

            ],
            [
                "image" => "product_item.png",
                "price" => "351",
                "name" => "Дезодорант",
                "description" => "Дезодорант для тела DRYDRY (Драй драй) 35 мл",
                "id"=>2,

            ],
            [
                "image" => "product_item.png",
                "price" => "351",
                "name" => "Дезодорант",
                "description" => "Дезодорант для тела DRYDRY (Драй драй) 35 мл",
                "id"=>3,
            ],
            [
                "image" => "product_item.png",
                "price" => "351",
                "name" => "Дезодорант",
                "description" => "Дезодорант для тела DRYDRYas asd asd asd as dasd as d (Драй драй) 35 мл",
                "id"=>4,

            ],
            [
                "image" => "product_item.png",
                "price" => "351",
                "name" => "Дезодорант",
                "description" => "Дезодорант для тела DRYDRY (Драй драй) 35 мл",
                "id"=>5,

            ],
        ];

    @endphp

    <div class="container">
        @if(count($analogs) > 0)
            @component("blocks.title.section_title",
            [
                "data" =>[
                    "title" => "<span>Другие аналоги</span>",
                    "section_class" => "product_slider_section",
                    "section_class_title" => "title-gen title_product_slider"
                ]
            ])

                @php
                    $btn_param=[
                        "name"=>"btn",
                        "btn_class"=>"tab_btn btn_product_slider",
                        "text"=>"Все товары",
                        "link"=>route('products')
                    ];
                @endphp
                @include("blocks.btn.btn", $btn_param)

                @component("blocks.slider.slider_section",
                [
                    "data"=>[
                        "class_slider_container"=>"product-slider cards_list_product-top product_item_too_mobail",
                        "slider_arrow_next"=>"slider-product-next slider-product-next-1",
                        "slider_arrow_prev"=>"slider-product-prev slider-product-prev-1",
                        "slider_list_class"=>"cards_list cards_list_product cards_list_product_analog"
                    ],
                    "template"=> "slider_product_item",
                    "data_items"=> $analogs
                ]
                )
                @endcomponent
            @endcomponent
        @endif

        @if(count($related_products) > 0)
            @component("blocks.title.section_title",
            [
                "data" =>[
                    "title" => "<span>С этим товаром так же покупают</span>",
                    "section_class" => "product_slider_section",
                    "section_class_title" => "title-gen title_product_slider",
                ]
            ])

                @php
                    $btn_param=[
                        "name"=>"btn",
                        "btn_class"=>"tab_btn btn_product_slider",
                        "text"=>"Все товары",
                        "link"=>route('products')
                    ];
                @endphp
                @include("blocks.btn.btn")

                @component("blocks.slider.slider_section",
                [
                    "data"=>[
                        "class_slider_container"=>"product-slider cards_list_product-top product_item_too_mobail",
                        "slider_arrow_next"=>"slider-product-next slider-product-next-2",
                        "slider_arrow_prev"=>"slider-product-prev slider-product-prev-2",
                        "slider_list_class"=>"cards_list cards_list_product cards_list_product_rel"
                    ],
                    "template"=> "slider_product_item",
                    "data_items"=> $related_products
                ]
                )
                @endcomponent
            @endcomponent
        @endif
    </div>
</div>


<!-- Root element of PhotoSwipe. Must have class pswp. -->
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

    <!-- Background of PhotoSwipe.
         It's a separate element as animating opacity is faster than rgba(). -->
    <div class="pswp__bg"></div>

    <!-- Slides wrapper with overflow:hidden. -->
    <div class="pswp__scroll-wrap">

        <!-- Container that holds slides.
            PhotoSwipe keeps only 3 of them in the DOM to save memory.
            Don't modify these 3 pswp__item elements, data is added later on. -->
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>

        <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
        <div class="pswp__ui pswp__ui--hidden">

            <div class="pswp__top-bar">

                <!--  Controls are self-explanatory. Order can be changed. -->

                <div class="pswp__counter"></div>

                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>

                <button class="pswp__button pswp__button--share" title="Share"></button>

                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>

                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>

                <!-- Preloader demo https://codepen.io/dimsemenov/pen/yyBWoR -->
                <!-- element will get class pswp__preloader--active when preloader is running -->
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                        <div class="pswp__preloader__cut">
                            <div class="pswp__preloader__donut"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div>
            </div>

            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
            </button>

            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
            </button>

            <div class="pswp__caption">
                <div class="pswp__caption__center"></div>
            </div>

        </div>

    </div>

</div>


{{--{{dd($current_product)}}--}}
@include('blocks.modal_basket.modal_basket')
@include('blocks.popup.popup')
@include('layouts.footer')