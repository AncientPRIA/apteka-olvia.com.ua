
@php
    $input_keys_arr =["form_group_class","validation","label_class","name","title","input_class","type","atr","value"];

    for($i=0;$i<count($input_keys_arr);$i++){
        if(!isset($input_param[$input_keys_arr[$i]])){
            switch ($input_keys_arr[$i]){
                case "type": $input_param[$input_keys_arr[$i]] ="text";break;
                default: $input_param[$input_keys_arr[$i]] = "";break;
            };
        };
    };

@endphp

<div class="form-group {{$input_param['form_group_class']}}" data-validation="{!! $input_param['validation'] !!}">
    <div class="input-container">
        <label class=" label {{$input_param['label_class']}} @if($input_param['value'] !== ''){{'label_active'}}@endif" for="{{$input_param['name']}}">{{$input_param['title']}}</label>
        <input class=" input autofill-disable {{$input_param['input_class']}}" value="{{$input_param['value']}}" name="{{$input_param['name']}}" type="{{$input_param['type']}}" {!! $input_param['atr'] !!}>
    </div>
    <div class="input_error"></div>
</div>
