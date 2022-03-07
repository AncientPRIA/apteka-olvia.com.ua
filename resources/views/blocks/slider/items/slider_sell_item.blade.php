@php
    $image = $item["image"] ?? '';
	$sizes = array(
					'1' => ['width' => 360, 'relative_path' => 'uploads/'.$image, 'q'=> 90],
					'2' => ['width' => 764, 'relative_path' => 'uploads/'.$image, 'q'=> 90],
					'id' => 'sell_big'.$item['id'],
				  );
	$mappings = array(
					'>320' => '2',
					'>768' => '1',
					'default' => '1'
				  );
	$sizes = Img::img($sizes);
	$picture_big = Img::picture_compose($sizes, $mappings, true, '', $image, true);
@endphp

<a href="{{route('products').'/'.$item->get_path()}}" rel="nofollow,noindex" class="sell-home-grid-item sell-home-grid-item_full Background_Is_Picture">
    {!! $picture_big !!}
    <div class="stock">

    </div>
    <div class="discount">
        @php
            $rootName = $item->get_root()->name
        @endphp
        {{$rootName}}
    </div>
    <div class="discount-info">
        @if($rootName !== $item['name'])
            {!! $item['name'] !!}
        @endif
    </div>
    @php
        $btn_param=[
            "name"=>"btn",
            "btn_class"=>"tab_btn sell_btn",
            "text"=>"Подробнее",
            "href"=>route('products').'/'.$item->get_path(),
            "type"=>"btn"
        ];
    @endphp
    @include("blocks.btn.link")
	{{--@include("blocks.btn.btn")--}}
</a>
