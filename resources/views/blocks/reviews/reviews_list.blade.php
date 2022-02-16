<div class="reviews_container">
    <div class="review_list_title">
        Отзывы:
    </div>
    @if(count($items) === 0)
        <div class="review_list">
            Пока нет отзывов
        </div>
    @else
        <div class="review_list">
        @foreach($items as $item)
            @include("blocks.reviews.items.review_item", ['item' => $item])
        @endforeach
        </div>

        @component("blocks.default.default_pagination_&_loadMore.default_pagination_&_loadMore",
    [
        "data"=>[
            "class_navigation"=>"review_navigation",
            "class_load_more"=>"review_load_more",
            "class_load_more_svg"=>"review_load_more_svg"
        ],
        "load_more"=>"true",
        "pagination"=>"false"
    ]
    )
        @endcomponent
    @endif
</div>