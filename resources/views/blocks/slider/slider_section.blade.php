@php
	$path_item ='blocks.slider.items.'.$template;

	$arr_keys =["class_slider_container","slider_arrow_next",
	"slider_arrow_prev","slider_list_class"];

	if(!isset($data)){
		$data = [];
	}

	for($i=0; $i<count($arr_keys);$i++){
		if(!isset($data[$arr_keys[$i]])){
			$data[$arr_keys[$i]] = "";
		}
	}

@endphp
<div class="slider-container {{$data["class_slider_container"]}}" {!! isset($data['data_atr_container'])?$data['data_atr_container']:"data-not='null'"!!}>

	<div class="slider-list {{$data["slider_list_class"]}}" {!! isset($data_atr)?$data_atr:""!!}>
		@foreach($data_items as $item)
            @include($path_item)
        @endforeach
	</div>

    <div class="slider-arrow-prev {{$data["slider_arrow_prev"]}}">
        <img src="{{asset("img/svg/arrow-left-noshaft-rounded.svg")}}" alt="arrow-left">
    </div>

    <div class="slider-arrow-next {{$data["slider_arrow_next"]}}">
        <img src="{{asset("img/svg/arrow-right-noshaft-rounded.svg")}}" alt="arrow-right">
    </div>

</div>