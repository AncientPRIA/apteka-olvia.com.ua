@php
	if(!isset($sp)){
		$sp = "/";
	}

	$breadcrumbs_index = 0;
	$breadcrumbs_count = count($breadcrumbs_items);
@endphp
<div class="breadcrumbs desktop">
	@foreach($breadcrumbs_items as $item)
		@if(isset($item["href"]) && $breadcrumbs_index < $breadcrumbs_count-1)
			<a class="breadcrumbs-link" href="{{$item["href"]}}">
				{{$item["title"]}}
			</a>
		@else
			<span class="breadcrumbs-no-link">
				{{$item["title"]}}
			</span>
		@endif
		@if($breadcrumbs_index < $breadcrumbs_count-1)
			<div class="breadcrumbs-sp">
				{{$sp}}
			</div>
		@endif
		@php $breadcrumbs_index++; @endphp
	@endforeach
</div>