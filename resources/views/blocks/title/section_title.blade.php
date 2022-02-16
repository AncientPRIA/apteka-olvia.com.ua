@php
	$arr_keys =["section_class","section_class_header",
	"section_class_title","title","subTitle","section_class_subTitle"];

	for($i=0; $i<count($arr_keys);$i++){
		if(!isset($data[$arr_keys[$i]])){
			$data[$arr_keys[$i]] = "";
		}
	}

@endphp
<section class="section {{$data["section_class"]}}">
	<div class="section-header {{$data["section_class_header"]}}">
		<div class="section-title {{$data["section_class_title"]}}">
			{!! $data["title"] !!}
		</div>

		@if($data["subTitle"]!="")
			<div class="section-sub-title {{$data["section_class_subTitle"]}}">
				{!!$data["subTitle"]!!}
			</div>
		@endif

	</div>
	{!! $slot  !!}
</section>
