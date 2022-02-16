
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

@if(isset($link))
<a href="{{$link}}" class="{{$btn_param['btn_class']}}" {!! $btn_param['atr'] !!} name="{{$btn_param['name']}}">{{$btn_param['text']}}</a>
@else
<button class="{{$btn_param['btn_class']}}" {!! $btn_param['atr'] !!} name="{{$btn_param['name']}}">{{$btn_param['text']}}</button>
@endif