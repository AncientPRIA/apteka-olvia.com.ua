@php
    // Image (multiple media picker)
/*
    $item["image"] = json_decode($item["image"]);
    if(is_array($item["image"])){
        if(count($item["image"]) > 0){
            $item["image"] = $item["image"][0];
        }else{
            $item["image"] = $item->no_image ?? '';
        }
    }else{
        if(empty($item["image"])){
            $item["image"] = $item->no_image ?? '';
        }
    }
    */
    // Image END

    $image = $item["image_thumb"] ?? '';
    if(empty($image)){
        $image = $item->no_image ?? '';
    }


    $sizes = array(
                    '1' => ['width' => 320, 'relative_path' => 'uploads/'.$image, 'q'=> 60, 'watermark' => config('image.watermark.product')],
                    '2' => ['width' => 540, 'relative_path' => 'uploads/'.$image, 'q'=> 60, 'watermark' => config('image.watermark.product')],
                    'id' => 'product_img_'.$item->id,
                  );
    $mappings = array(
                    '>540' => '2',
                    '>320' => '1',
                    'default' => '1'
                  );
    $sizes = Img::img($sizes);
    $picture = Img::picture_compose($sizes, $mappings, true, '', $item["title"].'_photo', true);
        if($item->id === 1227){
          //var_dump($sizes);
        }

@endphp
@php
	if(is_object($item)){
		$link = route('products').'/'.$item->get_path();
	}else{
		$link = '';
	}
@endphp
<div class="product_item Product_Item_Fn" data-product-id="{{$item['id']}}" >

    <a href="{{$link}}" class="product_item_img Background_Is_Picture" style="background-image: url({{asset('uploads/product_image_placeholder.png')}})">
        {!! $picture !!}
    </a>

    @if(isset($item["discount"]) && $item["discount"] !== 0)
        <div class="product_item_discount">
            <div class="discount_svg">
                @svg("img/svg/discount_svg.svg")
            </div>
            <span>{{$item["discount"]}}</span>
        </div>
        @php
            $old_price = $item['price'];
            $item["price"] = $price_discounted = round($item['price'] - ($item['price'] * ($item["discount"] / 100)));
        @endphp
    @endif

    <div class="product_item_info">
        @if(isset($old_price))
            {{--   TODO: PRICE VALUE (strike). UNCOMMENT, AFTER SYNC
           <div class="product_item_old_price">
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
            --}}
       @endif
       <div class="product_item_price">
           <span class="price-item-product_fn">Стоимость уточняйте</span>

           {{--   TODO: PRICE VALUE. UNCOMMENT AFTER SYNC
           <span class="price-item-product_fn">{{$item["price"]}}</span>

           <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                viewBox="0 0 330 330" style="enable-background:new 0 0 330 330;" xml:space="preserve">
               <path id="XMLID_449_" d="M180,170c46.869,0,85-38.131,85-85S226.869,0,180,0c-0.183,0-0.365,0.003-0.546,0.01h-69.434
                   c-0.007,0-0.013-0.001-0.019-0.001c-8.284,0-15,6.716-15,15v0.001V155v45H80c-8.284,0-15,6.716-15,15s6.716,15,15,15h15v85
                   c0,8.284,6.716,15,15,15s15-6.716,15-15v-85h55c8.284,0,15-6.716,15-15s-6.716-15-15-15h-55v-30H180z M180,30.01
                   c0.162,0,0.324-0.003,0.484-0.008C210.59,30.262,235,54.834,235,85c0,30.327-24.673,55-55,55h-55V30.01H180z"/>
                               <g>
           </svg>
           --}}

        </div>

        <a href="{{$link}}" class="product_item_name">
            {{$item["title"] ?? ''}}
        </a>

        <div class="product_item_description">
            {{$item["excerpt"] ?? ''}}
        </div>

        <div class="product_item_btn_group">
            <div class="product_item_btn_left">
                <div data-product-id="{{$item['id']}}" class="btn_like btn_product {{ user_verified()?"Add_Favorites":"" }}" @if (!user_verified()) onclick="popup_show({cls:'Autf',scrollOff:'body'})" @endif >
                    @svg("img/svg/heart_product_btn.svg")
                </div>

                <a href="{{$link}}" class="btn_product_link btn_product">
                    @svg("img/svg/arrow_product_btn.svg")
                </a>
            </div>
            <div class="product_item_btn_right">
                <div class="btn_add_product btn_product btn_add_anim" data-product-id="{{$item['id']}}">
                    @svg("img/svg/add_product_btn.svg")
                </div>
            </div>
        </div>
    </div>
</div>