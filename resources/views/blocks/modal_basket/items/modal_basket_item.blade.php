{{--
id: 10
title: "Дзеркало Дзеркало 2 19-12-20_16-43"
slug: "dzerkalo-dzerkalo-2-19-12-20-16-43"
status: "PUBLISHED"
author_id: "1"
created_at: "2019-12-20 16:43:28"
updated_at: "2020-01-24 14:34:33"
category_id: "2"
meta_title: null
meta_description: null
meta_h1: null
excerpt: null
body: null
image: "["products/1.png","products/2.png"]"
price: "300"
discount: "7"
active_substance_id: "5"
featured: "1"

--}}



@php
    $image = $content["image_thumb"] ?? '';
    if(empty($image)){
        $image = $content->no_image ?? '';
    }

    $sizes = array(
                    '1' => ['width' => 540, 'relative_path' => 'uploads/'.$image, 'q'=> 90],
                    'id' => 'item-'.$content["slug"],
                  );
    $mappings = array(
                    '>320' => '1',
                    'default' => '1'
                  );
    $sizes = Img::img($sizes);
    $picture = Img::picture_compose($sizes, $mappings, true, '', '', true);
@endphp

<div class="modal_basket_item Product_Item_Fn" data-product-id="{{$content["id"]}}">
    <div class="modal_basket_item_img Background_Is_Picture">
         {!! $picture !!}
    </div>

    <div class="modal_basket_item_info">
        <div class="modal_basket_vendorCode">
            {{$content["sku"]}}
        </div>
        <div class="modal_basket_item_name">
            {{$content["title"]}}
        </div>
    </div>

    <div class="modal_basket_item_groupBtn">
        <div class="btn_plus Cart_Plus_Product" data-product-id="{{$content["id"]}}">
            +
        </div>
        <div class="modal_basket_item_totalCount Cart_Counter_Product" data-product-id="{{$content["id"]}}">
            {{$count}}
        </div>
        <div class="btn_minus Cart_Minus_Product" data-product-id="{{$content["id"]}}">
            -
        </div>
    </div>

    <div class="modal_basket_item_price">
	    @if(isset($content["discount"]) && $content["discount"] !== 0)
		    @php
			    $old_price = $content['price'];
				$content["price"] = $price_discounted = round($content['price'] - ($content['price'] * ($content["discount"] / 100)));
		    @endphp
	    @endif
        <div class="modal_basket_item_totalPrice">
            {{--TODO: AFTER SYNC--}}
            {{--<span>{{$content['price']}}</span>--}}
            <span>0</span>
            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                 viewBox="0 0 330 330" style="enable-background:new 0 0 330 330;" xml:space="preserve">
                    <path id="XMLID_449_" d="M180,170c46.869,0,85-38.131,85-85S226.869,0,180,0c-0.183,0-0.365,0.003-0.546,0.01h-69.434
                        c-0.007,0-0.013-0.001-0.019-0.001c-8.284,0-15,6.716-15,15v0.001V155v45H80c-8.284,0-15,6.716-15,15s6.716,15,15,15h15v85
                        c0,8.284,6.716,15,15,15s15-6.716,15-15v-85h55c8.284,0,15-6.716,15-15s-6.716-15-15-15h-55v-30H180z M180,30.01
                        c0.162,0,0.324-0.003,0.484-0.008C210.59,30.262,235,54.834,235,85c0,30.327-24.673,55-55,55h-55V30.01H180z"/>
                <g>
                </svg>
        </div>

	    @if(isset($old_price))
	        <div class="modal_basket_item_oldPrice">
	            <span>
		            {{ $old_price  }}
	            </span>
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

    </div>

    <div class="modal_basket_item_deleteBtn Cart_Del_Product" data-product-id="{{$content["id"]}}">
        X
    </div>
</div>