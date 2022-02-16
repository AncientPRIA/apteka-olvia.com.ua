
@php
	$btn_keys_arr =["btn_class","name","text","btn_class","atr"];

	for($i=0;$i<count($btn_keys_arr);$i++){
		if(!isset($btn_param[$btn_keys_arr[$i]])){
			switch ($btn_keys_arr[$i]){
				default: $btn_param[$btn_keys_arr[$i]] = "";break;
			}
		}
	}

@endphp

@if(isset($btn_param['type']))
	<div class="{{$btn_param['btn_class']}}" {!! $btn_param['atr'] !!} >{{$btn_param['text']}}</div>
@else
	<a class="{{$btn_param['btn_class']}}" {!! $btn_param['atr'] !!}  href="{{$btn_param["href"]}}">{{$btn_param['text']}}</a>
@endif

