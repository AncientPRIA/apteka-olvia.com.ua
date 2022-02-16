@include('layouts.head')
@include('includes.sections.header.header',
	[
	    'header_top_bar' => true,
		'header_include'=>"blog",
		'params' => [
		    'bg' => 'design/home/bg.png',
		    'breadcrumbs_items'=> $breadcrumbs
		]
	]
)

<script>
    var page = {!! $posts->currentPage() !!};
    var category_id = {!! $category_id ?? 0 !!};
</script>

@php

    $title_blog = string($strings, 'blog_title', '<span>Статьи и новости</span>');

    if($category !== null){

        $title_blog = $category->name;

    }
@endphp

<div class="container">
    @component("blocks.title.section_title",
        [
            "data" =>[
                "title" => "<h1>".$title_blog."</h1>",
                "section_class_title" => "title-gen"
            ]
        ])

        @component("blocks.slider.slider_section",
                [
                    "data"=>[
                        "class_slider_container"=>"category-tab-slider",
                        "slider_arrow_next"=>"slider-category-next",
                        "slider_arrow_prev"=>"slider-category-prev",
                        "slider_list_class"=>"cards_list cards_list_category"
                    ],
                    "template"=> "slider_tab_item_with_href",
                    "data_items"=> $categories
                ]
                )
        @endcomponent

        <div class="blog_container">
            <div class="blog_body">
                <div class="blog_content">
                    @include("blocks.blog.blog_section")
                </div>



                @component("blocks.default.default_pagination_&_loadMore.default_pagination_&_loadMore",
                [
                    "data"=>[
                        "class_navigation"=>"product_navigation",
                        "class_load_more"=>"product_load_more",
                        "class_load_more_svg"=>"product_load_more_svg"
                    ],
                    "load_more"=>"true",
                    "pagination"=>"true",
                    "data_items"=>$posts
                ]
                )
                @endcomponent
            </div>


            <div class="blog_sidebar">

                @component("blocks.blog.sidebar.blog_sidebar",
                    [
                        "data" =>[
                            "sidebar_title" => "Интересное"
                        ],
                         "template"=> "blog_sidebar_item",
                         "data_items"=> $posts_sidebar_1
                    ])
                @endcomponent
            </div>
        </div>

    @endcomponent
</div>



@include('blocks.modal_basket.modal_basket')
@include('blocks.popup.popup')
@include('layouts.footer')