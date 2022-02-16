@php
	$image = $content["image_thumb"] ?? '';
	if(empty($image)){
		$image = $content->no_image ?? '';
	}


$sizes = array(
				'1' => ['width' => 540, 'relative_path' => 'uploads/'.$image, 'q'=> 90, 'watermark' => config('image.watermark.product')],
				'id' => 'item-'.$image,
			  );
$mappings = array(
				'>320' => '1',
				'default' => '1'
			  );
$sizes = Img::img($sizes);
$picture = Img::picture_compose($sizes, $mappings, true, '', '', true);

@endphp

<div class="favorit_basket_item Product_Item_Fn" data-product-id="{{$content['id']}}">
	<div class="favorit_basket_item_img Background_Is_Picture">
		{!! $picture !!}
	</div>

	<div class="favorit_basket_item_info">
		<div class="favorit_basket_vendorCode">
			{{$content['sku']}}
		</div>
		<div class="favorit_basket_item_name">
			{{$content["title"]}}
		</div>
		{{--<div class="favorit_basket_item_description">--}}
			{{--{{$content["body"]}}--}}
		{{--</div>--}}
	</div>


	<div class="favorit_basket_item_price">
		<div class="favorit_basket_item_totalPrice">
			<span class="price-item-product_fn">
				{{$content['price']}}
			</span>
			&#8381
		</div>
	</div>

    @php
        if(is_object($content)){
            $link = route('products').'/'.$content->get_path();
        }else{
            $link = '';
        }
    @endphp

	<a  href="{{$link}}" class="favorit_basket_item_deleteBtn btn_small">
		@svg("./img/svg/link.svg")
	</a>

	<div class="btn_small margin_l_r btn_add_product" data-product-id="{{$content['id']}}">
		@svg("./img/svg/cart.svg")
	</div>

	<div class="btn_small Del_Favorites " data-product-id="{{$content['id']}}">
		@svg("./img/svg/close-strong.svg")
	</div>
</div>