
<div class="review_item">
    <div class="review_item_header">
        <div class="review_item_name">
            {{$item["name"] ?? ''}}
        </div>
        <div class="review_item_date">
            {{$item->created_at->formatLocalized("%d %b %G, %H:%M:%S")}}
        </div>
    </div>
    <div class="review_item_body">
        {{$item["body"] ?? ''}}
    </div>
</div>