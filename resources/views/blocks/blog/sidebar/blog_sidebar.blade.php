@php
    $path_item ='blocks.blog.sidebar.items.'.$template;

    $arr_keys =["class_sidebar", "sidebar_title", "sidebar_list_class"];

    if(!isset($data)){
        $data = [];
    }

    for($i=0; $i<count($arr_keys);$i++){
        if(!isset($data[$arr_keys[$i]])){
            $data[$arr_keys[$i]] = "";
        }
    }

@endphp

<div class="sidebar_container {{$data["class_sidebar"]}}">

    @component("blocks.title.section_title",
        [
            "data" =>[
                "title" => $data["sidebar_title"],
                "section_class_title" => "title-gen sidebar_title wow slideInDown"
            ]
        ])

        <div class="sidebar_list {{$data["sidebar_list_class"]}}">
            @foreach($data_items as $item)
                @include($path_item)
            @endforeach
        </div>

    @endcomponent
</div>
