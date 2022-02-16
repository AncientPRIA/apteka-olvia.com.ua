<!DOCTYPE html>

<html lang="ru-Ru">
<head>
    <meta name="robots" content={!! setting('site.meta_robots') !!}>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
	{{--<meta name="test_bot" content="{{$bot_mame}}">--}}
	<meta name="1" content="{{$_SERVER['HTTP_USER_AGENT']}}">
	<title>{{ $meta['title'] }}</title>
	<meta name="description" content="{{ $meta['description'] }}">
	<meta name="lang" content="ru">
	<meta property="og:url" content="{{ url()->current() }}">
	<meta property="og:title" content="{{ $meta['title'] }}">
	<meta property="og:description" content="{{ $meta['description'] }}">
	<meta property="og:type" content="website">
	<meta property="og:locale" content="ru_RU">
	<meta property="og:site_name" content="">
	<meta property="og:image" content="{{ asset('uploads/'.$meta['og_image']) }}">

    @if(isset($head_pagination))

        @foreach($head_pagination as $key=>$item)
            <link rel="{{$key}}"  href={{$item}}>
        @endforeach

    @endif

	@if(isset($head_include))
		{!! $head_include !!}
	@endif

    <!-- new -->
    <meta name="google-site-verification" content="yx1WJGf4HI0XN0OcQp-mvfr4PMGOBM6fRQeJIokQDyk" />

    <!-- old -->
{{--	<meta name="google-site-verification" content="x5nqEzawTVycRuxnBDjv8rOg5o-02O6wopBR1e4DThY" />--}}

	<meta name="twitter:site" content="">
	<meta name="twitter:title" content="{{ $meta['title'] }}">
	<meta name="twitter:description" content="{{ $meta['description'] }}">
	<meta name="twitter:card" content="summary_large_image">
	{{--<meta name="twitter:image" content="{{ $meta['og_image'] }}">--}}
	<meta name="fb:app_id" content="">

	<link rel="canonical" href="{{ url()->current() }}">
	<link rel="shortcut icon" href="{{ URL::asset("img/favicon.png")}}" type="image/x-icon">
	<link rel="manifest" href="{{ URL::asset('manifest.json')}}">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	{{--<link rel="shortcut icon" href="./icon.ico">--}}

	<style>
		@for($i=0;$i<count($css_critical??[]);$i++)
			@php
				include $css_critical[$i];
			@endphp
		@endfor
	</style>


	@for($i=0;$i<count($css);$i++)
		@if($css[$i][1] === true)
			<link rel="stylesheet" href="{{$css[$i][0]}}">
		@else
			<link rel="stylesheet" href="{{ URL::asset($css[$i][0])}}">
		@endif
	@endfor

	{{--<link rel="stylesheet" href="{{ URL::asset($css[0])}}">--}}
	{{--<link rel="stylesheet" href="{{ URL::asset($css[1])}}">--}}

	<script>
		window.baseUrl = '{!! url('/') !!}';

		// if ('serviceWorker' in navigator) {
		// 	navigator.serviceWorker.register('/sw.js', {
		// 		scope: '.'
		// 	}).then(function (registration) {
		// 		// Registration was successful
		// 		///alert("!!!PWA work!!!!");
		// 		console.log('PWA: ServiceWorker registration successful with scope: ', registration.scope);
		// 	}, function (err) {
		// 		// registration failed :(
		// 		console.log('PWA: ServiceWorker registration failed: ', err);
		// 	});
		// }
	</script>

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-E6CDEVYVJS"></script>
	<script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-E6CDEVYVJS');
	</script>


	@include("includes.js_locale")
</head>
<body>
@if(isset($locale))
	<script>
        var locale = '{!! $locale !!}';
	</script>
@endif
@if(isset($locale_slug))
	<script>
        var locale_slug = '{!! $locale_slug !!}';
	</script>
@endif
@if(isset($microdata))
	{!! $microdata !!}
@endif
<main style="overflow: hidden;position: relative">
{{--<div class="body">--}}