@php
    //https://telegram.me/share/url?url=<URL>&text=<TEXT>
        $social_list_class= !isset($social_list_class)? "":$social_list_class;
    $social_url_share=[
        "tg" =>"https://telegram.me/share/url?url=".$url."&text=share",
        "vk"=>"https://vk.com/share.php?url={".$url."}",
        "ok"=>"https://connect.ok.ru/offer?url=".$url,
        "fb"=>"https://www.facebook.com/sharer.php?u=".$url,
        "tw"=>"",
        "vb"=>"viber://forward?text=".$url
    ];

@endphp

<div class="social-share {{$social_list_class}}">
    @foreach($social as  $link)
        <a  {!!  isset($link["attribute"])?$link["attribute"]:""!!}
            class="social-share__item social-share__{{$link["social"]}}  {{!isset($link["social_item_class"]) ? "" : $link["social_item_class"] }}" href="{{$social_url_share[$link["social"]]}}"
            rel="nofollow,noindex" target="_blank"
        >
            @svg($link['path_svg'],"")
        </a>
    @endforeach
</div>

{{--@include("blocks.social.link-group.social_list",[--}}
            {{--"social_list_class"=>"top-social",--}}
            {{--"url" =>"#",--}}
            {{--"social"=>[--}}
            {{--[--}}
            {{--"social"=>"vk",--}}
            {{--"path_svg" => "img/svg/vk.svg"--}}
            {{--],--}}
            {{--[--}}
            {{--"social"=>"ok",--}}
            {{--"link" => "#",--}}
            {{--"path_svg" => "img/svg/ok.svg"--}}
            {{--],--}}
            {{--[--}}
            {{--"social"=>"tg",--}}
            {{--"path_svg" => "img/svg/telegram.svg"--}}
            {{--]--}}
        {{--],--}}
{{--])--}}

{{--<a href="{!! $share_link !!}" {!!$share_atrr!!} target="_blank" class="share-link {{$share_modif}}">--}}
    {{--<div class="share-block-left">--}}
        {{--<div class="share-icon-svg">--}}
            {{--{!! $share_svg !!}--}}
        {{--</div>--}}
        {{--<span>{!! $share_text !!}</span>--}}
    {{--</div>--}}
{{--    <div class="share-block-right">--}}
{{--        <div class="share-counter">--}}
{{--            0--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</a>--}}