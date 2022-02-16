@php
	$sizes = array(
					'1' => ['width' => 320, 'relative_path' => 'uploads/video/home_mobile_poster.jpg', 'q'=> 90],
					'2' => ['width' => 1140, 'relative_path' => 'uploads/video/home_mobile_poster.jpg', 'q'=> 90],
					'id' => 'home_bg',
				  );
	$mappings = array(
					'>992' => '2',
					'>320' => '1',
					'default' => '1'
				  );
	$sizes = Img::img($sizes);
	$picture = Img::picture_compose($sizes, $mappings, false, '', '', false);
@endphp
{{--<header class="header_bg Background_Is_Picture">--}}
<header class="header_bg">
	{!! $picture !!}
	<div class="video_container video-1">
		@if(\Browser::isMobile())
			{{--<img src="{{URL::asset("uploads/video/home_mobile_poster.jpg")}}" alt="">--}}
			{{--<video id="video-1" class="video" poster="{{ URL::asset("uploads/video/home_mobile_poster.jpg")}}" src="{{ URL::asset("uploads/video/home_mobile.mp4")}}" muted="" playsinline autoplay="" loop="" preload="auto"></video>--}}
			{{--<script>--}}
				{{--document.addEventListener('touchend', function(){--}}
                    {{--var video = document.getElementById('video-1');--}}
                    {{--//setTimeout(function() {--}}
                        {{--video.play();--}}
                    {{--//}, 50);--}}
				{{--});--}}
                {{--//document.click();--}}
                {{--var clickEvent = document.createEvent("MouseEvents");--}}
				{{--clickEvent.initMouseEvent("click", true, true, window, 1, 0, 0, 0, 0, false, false, false, false, 0, null);--}}

			{{--</script>--}}
		@else
			<video id="video-1" class="video" src="{{ URL::asset("uploads/video/home_desktop.mp4")}}" muted="" playsinline autoplay="" loop="" preload="auto"></video>
		@endif

			{{--@php--}}
				{{--$sizes = array(--}}
                        {{--'1' => ['width' => 1140, 'relative_path' => 'uploads/design/bg_bottom__fix1.png', 'q'=> 90],--}}
                        {{--'id' => 'bg_bottom__fix1',--}}
                      {{--);--}}
                {{--$mappings = array(--}}
                                {{--'>320' => '1',--}}
                                {{--'default' => '1'--}}
                              {{--);--}}
                {{--$sizes = Img::img($sizes);--}}
                {{--$picture_bottom = Img::picture_compose($sizes, $mappings, true, '', '', true);--}}
			{{--@endphp--}}

			<div class="header_bg_white" style="background-image: url({{ url('/uploads/design/bg_bottom__fix1.png') }});"></div>

	</div>

	<div class="container container_relative">

		@include("blocks.menu.menu_top.menu")
		@include("blocks.menu.menu_bottom.menu")

		<div class="header-content">
			<img class="logo_home" src="{{ url('/uploads/design/olvia_logo_write.svg') }}" alt="olvia_logo_write" title="olvia_logo_write">
			<h1 class="header-subtitle" >
				{!! string($strings, 'header sub-title', 'СЕТЬ АПТЕК') !!}
				<br>
				{!! string($strings, 'header title', 'ОЛЬВИЯ') !!}
			</h1>
			{{--<div class="header-title">--}}
				{{--{!! string($strings, 'header title', 'ОЛЬВИЯ') !!}--}}
			{{--</div>--}}
			<div class="header-desc" data-wow-delay="0.6" >
				{{--{{$meta['h1']}}--}}
			</div>
		</div>
	</div>
</header>

@include("blocks.menu.menu_mobail.menu")