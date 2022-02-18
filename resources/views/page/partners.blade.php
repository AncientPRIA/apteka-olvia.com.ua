@include('layouts.head')

@include('includes.sections.header.header',
	[
        'header_include'=>"transparent_gheader.header",
        'header_top_bar' => true,
        'params' => [
		    'breadcrumbs_items'=> $breadcrumbs,
		    'background' => $image_header ?? null
		]
	]
)

<div class="container">
	<div class="block"></div>
	{{--excerpt--}}
	{{--image--}}
	{{--<div class="jobs-excerpt">--}}
		{{--{{$jobs[0]['excerpt']}}--}}
	{{--</div>--}}
	{{--@php--}}
		{{--$sizes = array(--}}
						{{--'1' => ['width' => 1922, 'relative_path' => 'uploads/'.$jobs[0]['image'], 'q'=> 90],--}}
						{{--'id' => 'banner_01',--}}
					  {{--);--}}
		{{--$mappings = array(--}}
						{{--'>320' => '1',--}}
						{{--'default' => '1'--}}
					  {{--);--}}
		{{--$sizes = Img::img($sizes);--}}

		{{--$picture = Img::picture_compose($sizes, $mappings, true, '', '', true);--}}
	{{--@endphp--}}

	{{--<div class="jobs-img Background_Is_Picture">--}}
		{{--{!! $picture !!}--}}
	{{--</div>--}}


@component("blocks.title.section_title",
		[
			"data" =>[
				"section_class" => "section_partners",
				"title" => "Партнерам",
				"section_class_title" => "title-gen"
			]
		])

		<br>

		{{--{{dd($jobs[0])}}--}}
		<div class="partners-list">
			{!! $partners[0]['body'] !!}
		</div>

	@endcomponent
</div>

@include('blocks.modal_basket.modal_basket')
@include('blocks.popup.popup')
@include('layouts.footer')