@php
    $sizes = array(
                    '1' => ['width' => 1140, 'relative_path' => 'uploads/'.$params["bg"], 'q'=> 90],
                    'id' => 'home_bg'.$params["bg"],
                  );
    $mappings = array(
                    '>320' => '1',
                    'default' => '1'
                  );
    $sizes = Img::img($sizes);
    $picture = Img::picture_compose($sizes, $mappings, true, '', '', true);

@endphp
<header class="header_bg Background_Is_Picture">
    {!! $picture !!}

    @if(isset($params["bg_white"]))
        @php
            $sizes = array(
                    '1' => ['width' => 1140, 'relative_path' => 'uploads/design/bg_bottom__fix1.png', 'q'=> 90],
                    'id' => 'bg_bottom__fix1',
                  );
            $mappings = array(
                            '>320' => '1',
                            'default' => '1'
                          );
            $sizes = Img::img($sizes);
            $picture_bottom = Img::picture_compose($sizes, $mappings, true, '', '', true);
        @endphp

        <div class="header_bg_white Background_Is_Picture">
            {!! $picture_bottom !!}
        </div>
    @endif

    <div class="container container_relative">
        @include("blocks.menu.menu_top.menu")
        @include("blocks.menu.menu_bottom.menu")
	    @include("blocks.menu.menu_mobail.menu")
	    @if(isset( $params['breadcrumbs_items']) )
		    {{--<div class="container">--}}
			    @include("blocks.breadcrumbs.breadcrumbs",[
					"breadcrumbs_items" => $params['breadcrumbs_items']
				])
		    {{--</div>--}}
	    @endif
        <div class="header-content">
{{--            <div class="header-title blog-title">--}}
{{--                {{ $post->category->name ?? string($strings, 'blog title', 'Статьи и новости') }}--}}
{{--            </div>--}}
            <h1 class="header-desc blog-desc">
                {{ $post['title'] ?? string($strings, 'blog desc', 'Узнайте, много нового и полезного в нашем блоге') }}
            </h1>
        </div>
    </div>
</header>