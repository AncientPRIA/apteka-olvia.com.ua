{{--@php--}}
{{--    $sizes = array(--}}
{{--                    '1' => ['width' => 81, 'relative_path' => 'uploads/'.$content["image"]],--}}
{{--                    'id' => 'home_product_'.$content["id"].'_image_mini',--}}
{{--                  );--}}
{{--    $mappings = array(--}}
{{--                    '>320' => '1',--}}
{{--                    'default' => '1'--}}
{{--                  );--}}
{{--    $sizes = Img::img($sizes);--}}
{{--    $picture = Img::picture_compose($sizes, $mappings, false, '', '', false);--}}
{{--@endphp--}}

<a class="card_item Background_Is_Picture" href="#">
    {{-- {!! $picture !!}--}}

    <div class="card_item_header">
        Шапка
    </div>

    <div class="card_item_text">
        <div class="title">
            {{$content["title"]}}
        </div>
        <div class="text">
            {{$content["description"]}}
        </div>
    </div>
</a>