@php

    $arr_keys =["class_navigation", "class_load_more", "class_load_more_svg"];

    if(!isset($data)){
        $data = [];
    }

    for($i=0; $i<count($arr_keys);$i++){
        if(!isset($data[$arr_keys[$i]])){
            $data[$arr_keys[$i]] = "";
        }
    }

@endphp

<div class="navigation {{$data["class_navigation"]}}">
    @if($load_more == "true" )

        <div class="load_more {{$data["class_load_more"]}}">
            <svg class="load_more_svg {{$data["class_load_more_svg"]}}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 88.1 96.36">
                <g data-name="Слой 2">
                    <g data-name="Layer 1">
                        <path class="Load_More_Svg_Path" d="M17.94,19.11a38.41,38.41,0,0,1,49.94-1.32l-11.43.43a2.66,2.66,0,0,0,.1,5.32h.09l17.56-.65a2.65,2.65,0,0,0,2.55-2.66v-.31L76.11,2.56a2.66,2.66,0,1,0-5.32.2l.42,10.88a43.7,43.7,0,0,0-56.83,1.53A43.72,43.72,0,0,0,1.23,57.89a2.65,2.65,0,0,0,2.58,2,2.22,2.22,0,0,0,.63-.08,2.66,2.66,0,0,0,1.95-3.21A38.37,38.37,0,0,1,17.94,19.11Z" />
                        <path class="Load_More_Svg_Path" d="M86.87,38.47a2.66,2.66,0,0,0-5.16,1.26,38.39,38.39,0,0,1-61.8,38.59l11.57-1A2.66,2.66,0,1,0,31,72l-17.5,1.58a2.65,2.65,0,0,0-2.4,2.89l1.58,17.49a2.64,2.64,0,0,0,2.63,2.42,1,1,0,0,0,.24,0,2.65,2.65,0,0,0,2.4-2.89L17,82.8a43.39,43.39,0,0,0,25.24,9.63c.75,0,1.5,0,2.22,0a43.7,43.7,0,0,0,42.41-54Z" />
                    </g>
                </g>
            </svg>
        </div>

    @endif

    @if($pagination == "true" )
        @if(isset($data_items))
                {{ $data_items->links('vendor.pagination.default') }}
        @endif
    @endif

</div>