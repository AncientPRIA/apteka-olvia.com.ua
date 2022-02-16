@php
    $image = $item['image'] ?? $item->no_image;
    $sizes = array(
                    '1' => ['width' => 540, 'relative_path' => 'uploads/'.$image, 'q'=> 90],
                    'id' => 'blog_sidebar_'.$item['id'],
                  );
    $mappings = array(
                    '>320' => '1',
                    'default' => '1'
                  );
    $sizes = Img::img($sizes);
    $picture = Img::picture_compose($sizes, $mappings, true, '', $item['title'].'_'.$item->category->name.'_photo_'.$loop->index, true);
@endphp

<a href="{{route('blog').'/'.$item->get_path()}}" class="sidebar_item wow slideInDown" data-wow-delay="0.10">
    <div class="sidebar_item_img Background_Is_Picture">
        {!! $picture !!}
    </div>
    <div class="sidebar_item_info">
        <div class="sidebar_item_top">
{{--            <div class="sidebar_item_date">--}}
{{--                {{$item->created_at->formatLocalized("%d.%m.%G")}}--}}
{{--            </div>--}}
            <div class="sidebar_item_category">
                {{$item->category->name}}
            </div>
        </div>
        <div class="sidebar_item_title">
            {{$item["title"]}}
        </div>

        <div class="sidebar_item_excerpt">
            {{$item["excerpt"]}}
        </div>
    </div>
</a>
