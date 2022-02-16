{{--
@php
    $sizes = array(
    '1' => ['width' => 376, 'relative_path' => 'uploads/posts/news.jpg', 'q'=> 90],
    'id' => 'blog_item',
    );
    $mappings = array(
    '>320' => '1',
    'default' => '1'
    );
    $sizes = Img::img($sizes);blog_section.blade.php

    $picture = Img::picture_compose($sizes, $mappings, true, '', '', true);
@endphp
--}}


<div class="blog_section">

    <div class="blog_section_top">
        <div class="blog_section_col_left wow slideInLeft" data-wow-delay="0.1">
        @if(isset($posts[0]))
            @php
            $item = $posts[0];
            $image = $item->image ?? '';
            if($image === ''){
                $image = $item->no_image;
            }

            $sizes = array(
            '1' => ['width' => 376, 'relative_path' => 'uploads/'.$image, 'q'=> 90],
            'id' => 'blog_item_'.$item->id,
            );
            $mappings = array(
            '>320' => '1',
            'default' => '1'
            );
            $sizes = Img::img($sizes);
            $picture = Img::picture_compose($sizes, $mappings, true, '', '', true);
            @endphp
            <a href="{{route('blog').'/'.$item->get_path()}}" class="blog_section_item padding-30 padding-60 w-100 Background_Is_Picture">
                {!! $picture !!}
                <div class="blog_section_item_top">
                    <div class="blog_section_item_date">
                        {{$item->created_at->isoFormat('D MMMM YYYY, hh:mm:ss ')}}
                    </div>
                    <div class="blog_section_item_category">
                        {{$item->category->name}}
                    </div>
                </div>
                <div class="blog_section_item_footer">
                    <div class="blog_section_item_title">
                        {{$item->title}}
                    </div>
                    <div class="blog_section_item_excerpt">
                        {{$item->excerpt}}
                    </div>
                </div>
            </a>
        @endif

        </div>
        <div class="blog_section_col_right">
            <div class="blog_section_col_right_top wow slideInDown" data-wow-delay="0.3">
                @if(isset($posts[1]))
                    @php
                        $item = $posts[1];
                        $image = $item->image ?? '';
                        if($image === ''){
                            $image = $item->no_image;
                        }

                        $sizes = array(
                        '1' => ['width' => 376, 'relative_path' => 'uploads/'.$image, 'q'=> 90],
                        'id' => 'blog_item_'.$item->id,
                        );
                        $mappings = array(
                        '>320' => '1',
                        'default' => '1'
                        );
                        $sizes = Img::img($sizes);
                        $picture = Img::picture_compose($sizes, $mappings, true, '', '', true);
                    @endphp
                <a href="{{route('blog').'/'.$item->get_path()}}" class="blog_section_item padding-30 padding-30 Background_Is_Picture">
                    {!! $picture !!}
                    <div class="blog_section_item_top">
                        <div class="blog_section_item_date">
                            {{$item->created_at->isoFormat('D MMMM YYYY, hh:mm:ss ')}}
                        </div>
                        <div class="blog_section_item_category">
                            {{$item->category->name}}
                        </div>
                    </div>
                    <div class="blog_section_item_footer">
                        <div class="blog_section_item_title">
                            {{$item->title}}
                        </div>
                        <div class="blog_section_item_excerpt">
                            {{$item->excerpt}}
                        </div>
                    </div>
                </a>
                @endif
            </div>

            <div class="blog_section_col_right_footer">
                @if(isset($posts[2]))
                    @php
                        $item = $posts[2];
                        $image = $item->image ?? '';
                        if($image === ''){
                            $image = $item->no_image;
                        }

                        $sizes = array(
                        '1' => ['width' => 376, 'relative_path' => 'uploads/'.$image, 'q'=> 90],
                        'id' => 'blog_item_'.$item->id,
                        );
                        $mappings = array(
                        '>320' => '1',
                        'default' => '1'
                        );
                        $sizes = Img::img($sizes);
                        $picture = Img::picture_compose($sizes, $mappings, true, '', '', true);
                    @endphp
                <a href="{{route('blog').'/'.$item->get_path()}}" class="blog_section_item wow slideInUp padding-30 w-100 w-50 Background_Is_Picture" data-wow-delay="0.5">
                    {!! $picture !!}
                    <div class="blog_section_item_top">
                        <div class="blog_section_item_date">
                            {{$item->created_at->isoFormat('D MMMM YYYY, hh:mm:ss ')}}
                        </div>
                        <div class="blog_section_item_category">
                            {{$item->category->name}}
                        </div>
                    </div>
                    <div class="blog_section_item_footer">
                        <div class="blog_section_item_title">
                            {{$item->title}}
                        </div>
                        <div class="blog_section_item_excerpt">
                            {{$item->excerpt}}
                        </div>
                    </div>
                </a>
                @endif
                @if(isset($posts[3]))
                    @php
                        $item = $posts[3];
                        $image = $item->image ?? '';
                        if($image === ''){
                            $image = $item->no_image;
                        }

                        $sizes = array(
                        '1' => ['width' => 376, 'relative_path' => 'uploads/'.$image, 'q'=> 90],
                        'id' => 'blog_item_'.$item->id,
                        );
                        $mappings = array(
                        '>320' => '1',
                        'default' => '1'
                        );
                        $sizes = Img::img($sizes);
                        $picture = Img::picture_compose($sizes, $mappings, true, '', '', true);
                    @endphp
                <a href="{{route('blog').'/'.$item->get_path()}}" class="blog_section_item wow slideInRight padding-30 w-100 w-50 Background_Is_Picture" data-wow-delay="0.7">
                    {!! $picture !!}
                    <div class="blog_section_item_top">
                        <div class="blog_section_item_date">
                            {{$item->created_at->isoFormat('D MMMM YYYY, hh:mm:ss ')}}
                        </div>
                        <div class="blog_section_item_category">
                            {{$item->category->name}}
                        </div>
                    </div>
                    <div class="blog_section_item_footer">
                        <div class="blog_section_item_title">
                            {{$item->title}}
                        </div>
                        <div class="blog_section_item_excerpt">
                            {{$item->excerpt}}
                        </div>
                    </div>
                </a>
                    @endif
            </div>
        </div>
    </div>

    <div class="blog_section_footer">
        <div class="blog_section_column wow slideInLeft" data-wow-delay="0.10">
            @if(isset($posts[4]))
                @php
                    $item = $posts[4];
                    $image = $item->image ?? '';
                    if($image === ''){
                        $image = $item->no_image;
                    }

                    $sizes = array(
                    '1' => ['width' => 376, 'relative_path' => 'uploads/'.$image, 'q'=> 90],
                    'id' => 'blog_item_'.$item->id,
                    );
                    $mappings = array(
                    '>320' => '1',
                    'default' => '1'
                    );
                    $sizes = Img::img($sizes);
                    $picture = Img::picture_compose($sizes, $mappings, true, '', '', true);
                @endphp
            <a href="{{route('blog').'/'.$item->get_path()}}" class="blog_section_item w-100 padding-30 padding-60 Background_Is_Picture">
                {!! $picture !!}
                <div class="blog_section_item_top">
                    <div class="blog_section_item_date">
                        {{$item->created_at->isoFormat('D MMMM YYYY, hh:mm:ss ')}}
                    </div>
                    <div class="blog_section_item_category">
                        {{$item->category->name}}
                    </div>
                </div>
                <div class="blog_section_item_footer">
                    <div class="blog_section_item_title">
                        {{$item->title}}
                    </div>
                    <div class="blog_section_item_excerpt">
                        {{$item->excerpt}}
                    </div>
                </div>
            </a>
            @endif
            @if(isset($posts[5]))
                @php
                    $item = $posts[5];
                    $image = $item->image ?? '';
                    if($image === ''){
                        $image = $item->no_image;
                    }

                    $sizes = array(
                    '1' => ['width' => 376, 'relative_path' => 'uploads/'.$image, 'q'=> 90],
                    'id' => 'blog_item_'.$item->id,
                    );
                    $mappings = array(
                    '>320' => '1',
                    'default' => '1'
                    );
                    $sizes = Img::img($sizes);
                    $picture = Img::picture_compose($sizes, $mappings, true, '', '', true);
                @endphp
            <a href="{{route('blog').'/'.$item->get_path()}}" class="blog_section_item w-100 padding-30 padding-60 Background_Is_Picture">
                {!! $picture !!}
                <div class="blog_section_item_top">
                    <div class="blog_section_item_date">
                        {{$item->created_at->isoFormat('D MMMM YYYY, hh:mm:ss ')}}
                    </div>
                    <div class="blog_section_item_category">
                        {{$item->category->name}}
                    </div>
                </div>
                <div class="blog_section_item_footer">
                    <div class="blog_section_item_title">
                        {{$item->title}}
                    </div>
                    <div class="blog_section_item_excerpt">
                        {{$item->excerpt}}
                    </div>
                </div>
            </a>
                @endif
        </div>
        @if(isset($posts[6]))
            @php
                $item = $posts[6];
                $image = $item->image ?? '';
                if($image === ''){
                    $image = $item->no_image;
                }

                $sizes = array(
                '1' => ['width' => 376, 'relative_path' => 'uploads/'.$image, 'q'=> 90],
                'id' => 'blog_item_'.$item->id,
                );
                $mappings = array(
                '>320' => '1',
                'default' => '1'
                );
                $sizes = Img::img($sizes);
                $picture = Img::picture_compose($sizes, $mappings, true, '', '', true);
            @endphp
        <a href="{{route('blog').'/'.$item->get_path()}}" class="blog_section_item wow slideInUp padding-30 w-100 w-70 Background_Is_Picture" data-wow-delay="0.13">
            {!! $picture !!}
            <div class="blog_section_item_top">
                <div class="blog_section_item_date">
                    {{$item->created_at->isoFormat('D MMMM YYYY, hh:mm:ss ')}}
                </div>
                <div class="blog_section_item_category">
                    {{$item->category->name}}
                </div>
            </div>
            <div class="blog_section_item_footer">
                <div class="blog_section_item_title">
                    {{$item->title}}
                </div>
                <div class="blog_section_item_excerpt">
                    {{$item->excerpt}}
                </div>
            </div>
        </a>
        @endif
    </div>
</div>