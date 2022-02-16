@php
    $sizes = array(
    '1' => ['width' => 376, 'relative_path' => 'uploads/'.$post["image"], 'q'=> 90],
    'id' => 'blog_item'.$post["image"],
    );
    $mappings = array(
    '>320' => '1',
    'default' => '1'
    );
    $sizes = Img::img($sizes);

    $picture = Img::picture_compose($sizes, $mappings, true, '', '', true);
@endphp

<div class="blog_item Background_Is_Picture">
    {!! $picture !!}
    <div class="blog_item_info">
        <div class="blog_item_top">
            <div class="blog_item_date">
                {{date('d m, Y', strtotime($post['created_at']))}}
            </div>
            <div class="blog_item_category">
                {{$post["category_id"]}}
            </div>
        </div>
        <div class="blog_item_footer">
            <div class="blog_item_title">
                {{$post["title"]}}
            </div>
            <div class="blog_item_excerpt">
                {{$post["excerpt"]}}
            </div>
        </div>
    </div>
</div>