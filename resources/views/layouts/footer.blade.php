@include("blocks.menu.menu_bottom.menu-cat.menu-cat")
</main>
@include('includes.sections.footer.footer')
@include('includes.js_strings')

@if(isset($critical_css))
	@for($i=0;$i<count($css);$i++)
		@if($css[$i][1] === true)
			<link rel="stylesheet" href="{{$css[$i][0]}}">
		@else
			{{--<link rel="stylesheet" href="{{ URL::asset($css[$i][0])}}">--}}
			<link rel="stylesheet" href="{{ URL::asset($css[$i][0])}}" media="none" onload="if(media!='all')media='all'"><noscript><link rel="stylesheet" href="{{ URL::asset($css[$i][0])}}"></noscript>
		@endif
	@endfor
@endif

<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

{{-- indlude page scripts --}}
@for($i=0;$i<count( $scripts);$i++)
    @if($scripts[$i][1] === true)
        <script src="{{$scripts[$i][0]}}"></script>
    @else
        <script src="{{ URL::asset($scripts[$i][0])}}"></script>
    @endif
@endfor