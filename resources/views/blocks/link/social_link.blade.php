@php
	$soc_link_att = !isset($soc_link_att)? "":$soc_link_att;
	$soc_class = !isset($soc_class)? "":$soc_class;
@endphp

<a href="{{$soc_link}}" class="social-link {{$soc_class}}" {!! $soc_link_att !!} target="_blank">
	{!! $slot !!}
</a>