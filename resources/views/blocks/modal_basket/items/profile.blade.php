@php
	$image = $content['item']["image_thumb"] ?? '';
	if(empty($image)){
		$image = $content['item']->no_image ?? '';
	}

	$sizes = array(
					'1' => ['width' => 540, 'relative_path' => 'uploads/'.$image, 'q'=> 90],
					'id' => 'item-231', //$content["slug"]
				  );
	$mappings = array(
					'>320' => '1',
					'default' => '1'
				  );
	$sizes = Img::img($sizes);
	$picture = Img::picture_compose($sizes, $mappings, true, '', '', true);

@endphp

<div class="profile_basket_item Product_Item_Fn">
	<div class="profile_basket_item_img Background_Is_Picture">
		{!! $picture !!}
	</div>

	<div class="profile_basket_item_info">
		<div class="profile_basket_vendorCode">
			{{$content['item']['sku']}}
		</div>
		<div class="profile_basket_item_name">
			{{$content["title"] ?? ''}}

		</div>
	</div>

	<div class="profile_basket_item_totalCount">
		{{$content['count']}}

	</div>


	<div class="profile_basket_item_price">

		<div class="profile_basket_item_totalPrice">
			<span>
				{{$content['price']}}
			</span>&#8381
		</div>


	</div>

	<a  href="/products/{{$content['item']->get_path()}}" target="_blank" rel="nofollow,noindex" class="profile_basket_item_deleteBtn">
		@svg("./img/svg/link.svg")
	</a>
</div>