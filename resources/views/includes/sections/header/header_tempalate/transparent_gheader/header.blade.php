@php
	if(!isset( $background ) || $background === null){
        $background = '/header_default.jpg';
	}

        $image = $background;
        $sizes = array(
                        '1' => ['width' => 1922, 'relative_path' => 'uploads/'.$image, 'q'=> 90],
                        'id' => 'product_category_image_header_'.$image,
                      );
        $mappings = array(
                        '>320' => '1',
                        'default' => '1'
                      );
        $sizes = Img::img($sizes);

        $picture = Img::picture_compose($sizes, $mappings, true, '', '', false);
@endphp
<header class="header_transparent Background_Is_Picture">
    {!! $picture !!}
	<div class="container container_relative">
		@include("blocks.menu.menu_top.menu")
		@include("blocks.menu.menu_bottom.menu")

	</div>
	@if(isset( $params['breadcrumbs_items']) )
		<div class="container">
			@include("blocks.breadcrumbs.breadcrumbs",[
				"breadcrumbs_items" => $params['breadcrumbs_items']
			])
		</div>
	@endif
</header>
@include("blocks.menu.menu_mobail.menu")