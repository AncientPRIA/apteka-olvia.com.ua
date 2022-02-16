
<div class="recommendations_list">
    <div class="recommendations_col_left">

        <div class="recommendations_item_group_column ">

            @php
                $index = 0;
                $image = $data_items[$index]['image'];

                $sizes = array(
                                '1' => ['width' => 540, 'relative_path' => 'uploads/'.$image, 'q'=> 60],
                                '2' => ['width' => 320, 'relative_path' => 'uploads/'.$image, 'q'=> 60],
                                'id' => 'categories_featured_'.$data_items[$index]['id'],
                              );
                $mappings = array(
                                '>992' => '1',
                                '>320' => '2',
                                'default' => '2'
                              );
                $sizes = Img::img($sizes);
                $picture = Img::picture_compose($sizes, $mappings, true, '', $image, true);
            @endphp
            <a href="{{route('products').'/'.$data_items[$index]->get_path()}}" class="recommendations_item recommendations_sm_column Background_Is_Picture wow slideInDown">
                {!! $picture !!}

                <div class="recommendations_item_info">
                    {{--
                    <div class="recommendations_countProduct">
                        (25 товаров)
                    </div>--}}
                    <div class="recommendations_title">
                        {{$data_items[$index]['name']}}
                    </div>
                </div>

            </a>

            @php
                $index = 1;
                $image = $data_items[$index]['image'];

                $sizes = array(
                                '1' => ['width' => 540, 'relative_path' => 'uploads/'.$image, 'q'=> 60],
                                '2' => ['width' => 320, 'relative_path' => 'uploads/'.$image, 'q'=> 60],
                                'id' => 'categories_featured_'.$data_items[$index]['id'],
                              );
                $mappings = array(
                                '>992' => '1',
                                '>320' => '2',
                                'default' => '2'
                              );
                $sizes = Img::img($sizes);
                $picture = Img::picture_compose($sizes, $mappings, true, '', $image, true);
            @endphp
            <a href="{{route('products').'/'.$data_items[$index]->get_path()}}" class="recommendations_item recommendations_sm_column Background_Is_Picture wow slideInUp" data-wow-delay="0.2s">
                {!! $picture !!}

                <div class="recommendations_item_info">
                    {{--<div class="recommendations_countProduct">
                        (25 товаров)
                    </div>--}}
                    <div class="recommendations_title">
                        {{$data_items[$index]['name']}}
                    </div>
                </div>
            </a>

        </div>

        @php
            $index = 2;
            $image = $data_items[$index]['image'];

            $sizes = array(
                            '1' => ['width' => 540, 'relative_path' => 'uploads/'.$image, 'q'=> 60],
                            '2' => ['width' => 320, 'relative_path' => 'uploads/'.$image, 'q'=> 60],
                            'id' => 'categories_featured_'.$data_items[$index]['id'],
                          );
            $mappings = array(
                            '>992' => '1',
                            '>320' => '2',
                            'default' => '2'
                          );
            $sizes = Img::img($sizes);
            $picture = Img::picture_compose($sizes, $mappings, true, '', $image, true);
        @endphp
        <a href="{{route('products').'/'.$data_items[$index]->get_path()}}" class="recommendations_item recommendations_basis_column Background_Is_Picture wow slide zoomIn" data-wow-delay="0.4s">
            {!! $picture !!}

            <div class="recommendations_item_info">
                {{--<div class="recommendations_countProduct">
                    (25 товаров)
                </div>--}}
                <div class="recommendations_title">
                    {{$data_items[$index]['name']}}
                </div>
            </div>
        </a>
    </div>

    <div class="recommendations_col_right">
        @php
            $index = 3;
            $image = $data_items[$index]['image'];

            $sizes = array(
                            '1' => ['width' => 540, 'relative_path' => 'uploads/'.$image, 'q'=> 60],
                            '2' => ['width' => 320, 'relative_path' => 'uploads/'.$image, 'q'=> 60],
                            'id' => 'categories_featured_'.$data_items[$index]['id'],
                          );
            $mappings = array(
                            '>992' => '1',
                            '>320' => '2',
                            'default' => '2'
                          );
            $sizes = Img::img($sizes);
            $picture = Img::picture_compose($sizes, $mappings, true, '', $image, true);
        @endphp
        <a href="{{route('products').'/'.$data_items[$index]->get_path()}}" class="recommendations_item recommendations_basis_row Background_Is_Picture wow slideInDown" data-wow-delay="0.4s">
            {!! $picture !!}

            <div class="recommendations_item_info">
                {{--<div class="recommendations_countProduct">
                    (25 товаров)
                </div>--}}
                <div class="recommendations_title">
                    {{$data_items[$index]['name']}}
                </div>
            </div>
        </a>

        <div class="recommendations_item_group_row">

            @php
                $index = 4;
                $image = $data_items[$index]['image'];

                $sizes = array(
                                '1' => ['width' => 540, 'relative_path' => 'uploads/'.$image, 'q'=> 60],
                                '2' => ['width' => 320, 'relative_path' => 'uploads/'.$image, 'q'=> 60],
                                'id' => 'categories_featured_'.$data_items[$index]['id'],
                              );
                $mappings = array(
                                '>992' => '1',
                                '>320' => '2',
                                'default' => '2'
                              );
                $sizes = Img::img($sizes);
                $picture = Img::picture_compose($sizes, $mappings, true, '', $image, true);
            @endphp
            <a href="{{route('products').'/'.$data_items[$index]->get_path()}}" class="recommendations_item recommendations_sm_row Background_Is_Picture wow slideInUp" data-wow-delay="0.2s">
                {!! $picture !!}

                <div class="recommendations_item_info">
                   {{-- <div class="recommendations_countProduct">
                        (25 товаров)
                    </div>--}}
                    <div class="recommendations_title">
                        {{$data_items[$index]['name']}}
                    </div>
                </div>
            </a>

            @php
                $index = 5;
                $image = $data_items[$index]['image'];

                $sizes = array(
                                '1' => ['width' => 540, 'relative_path' => 'uploads/'.$image, 'q'=> 60],
                                '2' => ['width' => 320, 'relative_path' => 'uploads/'.$image, 'q'=> 60],
                                'id' => 'categories_featured_'.$data_items[$index]['id'],
                              );
                $mappings = array(
                                '>992' => '1',
                                '>320' => '2',
                                'default' => '2'
                              );
                $sizes = Img::img($sizes);
                $picture = Img::picture_compose($sizes, $mappings, true, '', $image, true);
            @endphp
            <a href="{{route('products').'/'.$data_items[$index]->get_path()}}" class="recommendations_item recommendations_sm_row Background_Is_Picture wow slideInRight">
                {!! $picture !!}

                <div class="recommendations_item_info">
                    {{--<div class="recommendations_countProduct">
                        (25 товаров)
                    </div>--}}
                    <div class="recommendations_title">
                        {{$data_items[$index]['name']}}
                    </div>
                </div>
            </a>

        </div>
    </div>

</div>