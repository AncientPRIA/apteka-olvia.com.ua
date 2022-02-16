
@if(isset($header_top_bar) && $header_top_bar===true )
	@include("includes.sections.header.header-top-bar")
@endif

@if(isset($header_include))
	@php
		if(!isset($params)){
            $params =[];
		}
	@endphp
	@include("includes.sections.header.header_tempalate.".$header_include, $params)
@endif
<div class="mobail">
	@include("blocks.menu.menu_top.nav-list",['social'=>true,"att"=>'style="display:none"'])
</div>