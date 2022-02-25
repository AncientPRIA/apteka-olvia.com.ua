@php
    $sizes = array(
                    '1' => ['width' => 1925, 'relative_path' => 'uploads/design/bg_footer_olvia.png', 'q'=> 60],
                    '2' => ['width' => 767, 'relative_path' => 'uploads/design/bg_footer_olvia_tab.png', 'q'=> 60],
                    '3' => ['width' => 575, 'relative_path' => 'uploads/design/bg_footer_olvia_mob.png', 'q'=> 80],
                    'id' => 'olvia_footer',
                  );
    $mappings = array(
                    '>992' => '1',
                    '>768' => '2',
                    '>320' => '3',
                    'default' => '1'
                  );
    $sizes = Img::img($sizes);
    $picture = Img::picture_compose($sizes, $mappings, true, '', 'bg_footer_olvia.png', true);

@endphp


<footer style="overflow: hidden">
    <div class="footer_bg Background_Is_Picture">
        {!! $picture !!}
        <div class="container footer">
            <div class="footer_first_list ">
                {{--	            <img src="{{URL::asset("uploads/design/logo-foot.svg")}}" class="footer_logo wow heartBeat" alt="olvia logo big">--}}
                <div class="footer_logo wow heartBea">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 105.72 105.8">
                        <defs>
                            <style>.a0c5bf6c-e011-4daa-b79d-a4cb2bf9e8cd {
                                    fill: #fff;
                                    fill-rule: evenodd;
                                }</style>
                        </defs>
                        <g id="bfbf3306-5d8b-4805-b5f3-5574284697a9" data-name="Слой 2">
                            <g id="f904b568-6f38-4888-aa08-ed7eb5453e03" data-name="Layer 1">
                                <g id="eba6b1e5-2438-4e44-8aa6-749194c5d4cc" data-name="logo">
                                    <path class="a0c5bf6c-e011-4daa-b79d-a4cb2bf9e8cd"
                                          d="M52.86,0a52.9,52.9,0,0,1,0,105.8v-3.08a49.82,49.82,0,0,0,0-99.64Zm0,105.8A52.9,52.9,0,0,1,52.86,0V3.08a49.82,49.82,0,0,0,0,99.64Z"/>
                                    <path class="a0c5bf6c-e011-4daa-b79d-a4cb2bf9e8cd"
                                          d="M58.58,55.79c6.31.4,10.12,1.52,12.07,3.16.09.07.16.1.15.21s-.07.07-.16.1a58.8,58.8,0,0,1-12.06,2.29V60.28c1.42-.08,3-.2,4.72-.33a28.44,28.44,0,0,0,4.36-.62c1-.24,1.31-.55.11-1a18.86,18.86,0,0,0-3.09-.7c-.39-.09-3-.25-6.1-.37ZM33.67,51.24c-2.8.83-4.24-2.45-3.5-4.16a3.81,3.81,0,0,1,2.37-2.28c2.67-.83,4.41.56,4.15,3L34.8,49.43c-.27.23.17.82.59.51l2.09-1.53,8.84,7.38a115.61,115.61,0,0,1,12.26,0v1.45c-3.28-.12-7.2-.2-10-.09l3,3,.14.13a1.1,1.1,0,0,0,.41.13c2,.05,4,0,6.38-.16v1.27a70.92,70.92,0,0,1-22.84-2.16c-1.38-.33,1.07-2.17,2.7-2.47Z"/>
                                    <path class="a0c5bf6c-e011-4daa-b79d-a4cb2bf9e8cd"
                                          d="M63.22,76.45c-1.32,1.17-5.08,1.78-9.14,1.84v3.36c4.57-.26,8.78-2,9.14-5.2,8.23-4.86,8.59-10.51,7.9-16.28a67.83,67.83,0,0,1-17,2.7v1.52a5.47,5.47,0,0,1,1.56.2,2.14,2.14,0,0,1,1.19.85,2.38,2.38,0,0,1,.5,1.52,2.77,2.77,0,0,1-1.9,2.48c.12.18.92,2.84,1.53,1.92l.75-1.15c.19-.28-.39-.35-.37-.42l.08-.35h1.66l-.06.33c0-.17-.78.39-.79.41-.16.41-1.22,1.69-1.2,1.74a19.65,19.65,0,0,0,2.06,2.84,1.72,1.72,0,0,0,.8.29l0,.28H56.5v-.45l.63-.18L55.84,73l-1.18,1.73.38.1v.42l-1,0v2.16a47.06,47.06,0,0,0,9.14-1Zm-9.14-1.71v-4l1.53,2-1.51,2Zm0-5.7V65a1.64,1.64,0,0,1,.93.46,2.25,2.25,0,0,1,.5,1.61,2.42,2.42,0,0,1-.27,1.22,1.52,1.52,0,0,1-.75.65A3,3,0,0,1,54.08,69Zm0,9.25c-4.69.08-9.78-.58-12-2a54,54,0,0,0,12,1.1V75.27l-.75,0,.07-.42.68-.13v-4l-.83-1.09h-.59v2.71a3.08,3.08,0,0,0,.09,1,.64.64,0,0,0,.33.32,2.13,2.13,0,0,0,.87.12V74H49.17v-.25a2.19,2.19,0,0,0,.88-.12.71.71,0,0,0,.32-.32,2.71,2.71,0,0,0,.1-1V66.05a2.71,2.71,0,0,0-.1-1,.64.64,0,0,0-.33-.32,2.09,2.09,0,0,0-.87-.13v-.24h4.91V62.87a58.53,58.53,0,0,1-19.15-2.7c-.9,9.68,1.14,15.52,7.17,16.16.7,3.86,6.59,5.62,12,5.32V78.29Zm0-13.3V69a6,6,0,0,1-1,.07h-.39V64.92h.73A2.87,2.87,0,0,1,54.08,65Z"/>
                                    <path class="a0c5bf6c-e011-4daa-b79d-a4cb2bf9e8cd"
                                          d="M52.86,15.74a37.16,37.16,0,0,1,0,74.32v-1.9a35.26,35.26,0,0,0,0-70.52Zm0,0h0v1.9h0a35.26,35.26,0,0,0,0,70.52h0v1.9h0a37.16,37.16,0,0,1,0-74.32Z"/>
                                    <path class="a0c5bf6c-e011-4daa-b79d-a4cb2bf9e8cd"
                                          d="M85.9,31.2V28.11l4.75-3,1.4,2.19L89.33,29a.7.7,0,0,0,.48.32,5.46,5.46,0,0,0,1.4-.2,3.8,3.8,0,0,1,1.27-.1,1.65,1.65,0,0,1,.93.55,9.68,9.68,0,0,1,1.26,1.69l-1.49,1-.07-.11a1.57,1.57,0,0,0-.75-.7,1.76,1.76,0,0,0-1,0,3.09,3.09,0,0,1-1,.08,1.77,1.77,0,0,1-.73-.31,2.32,2.32,0,0,1,0,1.17,5.72,5.72,0,0,1-.71,1.5l-1.32,2.2-1.57-2.46,1.27-2a3.47,3.47,0,0,0,.51-1,.73.73,0,0,0-.13-.57L85.9,31.2Zm0-9V19.7l2.34,2.23L87,23.23Zm0-2.49v2.49l-2.05-2-.92,1L85.84,24l-1.18,1.24-2.92-2.78-1.14,1.2,3.24,3.09-1.32,1.38L79.06,24.8V21.47l4.15-4.34L85.9,19.7Zm0,8.41V31.2l-.94.6-1.4-2.19,2.34-1.5ZM79.06,16.53V14.06l1.67,1.07-1.12,1.75Zm0-2.47v2.47l-1.69-1.09L74,20.78l-2.19-1.41L75.19,14,73,12.61l1.12-1.75,5,3.2Zm0,7.41V24.8L77.4,23.21l1.66-1.74ZM54.62,13.11V11.29l.92,0-.83-3.05-.09.25V6.08l1.6,0,2.93,8.49-2.72-.07L56,13.15l-1.42,0ZM68.1,17.26l-2.5-.71,1.72-6.1-2.75-.78-1.72,6.11-2.5-.71L62.64,7l7.74,2.18ZM54.62,6.08V8.52l-.92,2.75.92,0v1.82l-1.52,0-.46,1.38L50,14.37l3.39-8.32,1.24,0ZM41.05,16.29V14.41l.37-.11c.79-.22,1.1-.61.94-1.18s-.58-.62-1.31-.43V10.93a4.31,4.31,0,0,1,2.43,0,2.43,2.43,0,0,1,1.45,1.77,2,2,0,0,1-.32,1.94,4.15,4.15,0,0,1-2.18,1.26Zm0-5.36v1.76l-.08,0-1.28.36.46,1.6.9-.26v1.88l-2.91.82L35.85,9l2.5-.71.88,3.12,1.34-.38.48-.12ZM27.3,24.18V21.55l.32-.31,1.31,1.38L27.3,24.18Zm0-8.22v-2.4l5.21-3,1,1.81-2.29,1.32,3.16,5.49-2.25,1.3L29,15ZM19.76,27.81l.62-.53a4,4,0,0,1,.95,1.47,3.08,3.08,0,0,1,.16,1.56,4.64,4.64,0,0,1-.73,1.74,6.9,6.9,0,0,1-1,1.23V27.81ZM27.3,13.56V16l-.6.35-1-1.8,1.64-.95Zm0,8-2.92,2.79-1.14-1.2,2.91-2.79L25,19.11,22.05,21.9l-.92-1,3.14-3L23,16.62l-3.27,3.13v3.52l4,4.25,3.49-3.34Zm-8.73,7.27,1.19-1v5.47a3.2,3.2,0,0,1-.55.45,3.57,3.57,0,0,1-2,.5,4.7,4.7,0,0,1-2.5-.86,4.36,4.36,0,0,1-2.1-2.67,4.24,4.24,0,0,1,.76-3.25,4.19,4.19,0,0,1,1.89-1.75,3.71,3.71,0,0,1,2.39-.07l-.81,2.2a2,2,0,0,0-.61-.09A1.61,1.61,0,0,0,14.92,30a3,3,0,0,0,1.19,1.19,3.2,3.2,0,0,0,1.84.68A1.69,1.69,0,0,0,19.29,30a2.58,2.58,0,0,0-.72-1.13Zm1.19-9.07v3.52L18,21.43Z"/>
                                    <path class="a0c5bf6c-e011-4daa-b79d-a4cb2bf9e8cd"
                                          d="M86.94,84.63V82.7l.92,1-.92.94Zm0-5a4.1,4.1,0,0,0,.38-.3,2.61,2.61,0,0,0-.14.63,3.3,3.3,0,0,0,.08.52,3,3,0,0,0,.16.52l1,2.15,1.48-2-1-2.31a1.6,1.6,0,0,1-.19-.66,1,1,0,0,1,.19-.54l.12-.16,2.23,1.8,1.31-1.79L87,73l-.09.13v2.63l1,.83-.55.75a2.93,2.93,0,0,1-.43.41l0,0Zm0-6.45v2.63l-.1-.08-.58.78a1.37,1.37,0,0,0-.34.79.64.64,0,0,0,.28.5.6.6,0,0,0,.4.15.58.58,0,0,0,.34-.11V79.6a2.09,2.09,0,0,1-.3.15,1.87,1.87,0,0,1-.73.15V74.54l1-1.39Zm0,9.55v1.93l-.53.54-.5-.54v-3Zm-1-8.16V79.9l-.22,0a1.93,1.93,0,0,1-1-.43,2,2,0,0,1-.69-1,1.76,1.76,0,0,1,0-1.11A5.56,5.56,0,0,1,84.85,76l1.06-1.44Zm0,7.06L83,78.48l-1.44,1.46.82,4.79L79.87,82v3l3.23,3.49,1.46-1.49-.81-4.75,2.16,2.33v-3Zm-6,9.34V86.75a1.59,1.59,0,0,1,.38.07,1.72,1.72,0,0,1,.9.76,2.07,2.07,0,0,1,.32,1,2,2,0,0,1-.2,1,2.21,2.21,0,0,1-.56.67q-.55.49-.75.63Zm0-8.93v3L78.8,83.91V82.77l.91-.93.16.17Zm0,4.74a2.26,2.26,0,0,0-.88.16,1.78,1.78,0,0,0,.37-.83,1.68,1.68,0,0,0-.26-1.15,1.86,1.86,0,0,0-.3-.37v3.75a.59.59,0,0,1,.41.3.67.67,0,0,1,.1.59,1.14,1.14,0,0,1-.51.6v1.87l1.07-.73Zm-1.07-4v1.14l-.55-.59.55-.55Zm0,1.79v3.75h0a1,1,0,0,0-.54.16V84.22a1.52,1.52,0,0,1,.59.34Zm0,5.24v1.87l-.59.41V90.2l.56-.38Zm-.59-5.58v4.24a1.79,1.79,0,0,0-.22.13l-.91.63.77,1.23.36-.25v1.88L76.86,93l-.22-.36V87.87l.5-.34a1.15,1.15,0,0,0,.49-.56.7.7,0,0,0-.11-.55.58.58,0,0,0-.43-.3.78.78,0,0,0-.45.11V84.46a2,2,0,0,1,1.34-.3A1,1,0,0,1,78.21,84.22Zm-1.57.24v1.77l-.21.13-.8.55.72,1.15.29-.19v4.77L74.17,88.7V86.15l2.24-1.54.23-.15Zm-2.47,10a2.73,2.73,0,0,0,.9-1,1.85,1.85,0,0,0,0-1.7,2.34,2.34,0,0,0-.92-1.12Zm0-8.29V88.7L73.08,87v-.08l1.09-.74Zm0,4.5v3.79a5.14,5.14,0,0,1-.7.4l-.39.19V93a.61.61,0,0,0,0-.42V90.42a2.18,2.18,0,0,1,.58,0A1.84,1.84,0,0,1,74.17,90.65Zm-1.09-3.76V87l0,0,0,0Zm0,3.53V92.6a.56.56,0,0,0,0-.12c-.17-.37-.52-.45-1.05-.22V90.67a4.12,4.12,0,0,1,1.09-.25Zm0,2.6v2L72,95.56V93.84l.44-.22A1.14,1.14,0,0,0,73.08,93ZM72,90.67v1.59l-.13.06-1,.48.58,1.3.55-.26v1.72l-1.87.91L68,91.65V89.49l1.16-.57,1.13,2.55,1-.51c.23-.11.46-.21.68-.29Zm-4,6.54.79-.24L68,94.18Zm0-7.72v2.16l-.79-1.78.79-.38Zm0,4.69L66.8,90l-5.65,1.75V92l1.23,4.36a1.11,1.11,0,0,1,0,.76.83.83,0,0,1-.54.38l-.31.09L62,99a6.26,6.26,0,0,0,.8-.16,3.54,3.54,0,0,0,1.51-.79,1.41,1.41,0,0,0,.37-1.1,10.93,10.93,0,0,0-.42-2l-.6-2.14,1.61-.5,1.48,5.26L68,97.21v-3Zm-6.83,2.73V95.07c0,.13,0,.26,0,.4A5.61,5.61,0,0,1,61.15,96.91Zm0-5.17V92l-.07-.25.07,0Zm0,3.33v1.84a4.19,4.19,0,0,1-.19.68A3.1,3.1,0,0,1,59.88,99a3.69,3.69,0,0,1-1.89.62l-.38,0V97.92h.18a1.32,1.32,0,0,0,1-.56,2.84,2.84,0,0,0,.23-1.72,2.33,2.33,0,0,0-.51-1.46,1.27,1.27,0,0,0-.93-.38V92.08a3.4,3.4,0,0,1,2.42.75A3.59,3.59,0,0,1,61.15,95.07Zm-3.54-3v1.71h-.19a1.29,1.29,0,0,0-1,.58,3.4,3.4,0,0,0,.26,3.16,1.32,1.32,0,0,0,.93.39v1.69A3.92,3.92,0,0,1,56,99.37a3,3,0,0,1-1.34-1.13A4.16,4.16,0,0,1,54,96.17a3.82,3.82,0,0,1,.71-2.84,3.51,3.51,0,0,1,2.55-1.23l.32,0ZM47.19,98.16V96.57l.76.09L47.48,94l-.29.7V92l1.7.21,1.78,7.54-2.23-.27-.22-1.24Zm0-6.12v2.63l-.75,1.81.75.09v1.59L45.81,98l-.48,1.14L43.94,99V97.27L46.55,92l.64.08ZM43.94,93l.29.07.1,0,.39-1.48-.78-.21Zm0-1.59V93a1.06,1.06,0,0,0-.55,0,1.52,1.52,0,0,0-.6.57,2.34,2.34,0,0,1-.54.6,1.27,1.27,0,0,1-.61.22,2,2,0,0,1,.64.75,5.77,5.77,0,0,1,.41,1.38l.41,2.18-2.34-.67-.34-2a4,4,0,0,0-.23-.93.62.62,0,0,0-.4-.29l-.72,2.77-1.25-.35V93.84l1-3.87,2.07.59-.71,2.69a.57.57,0,0,0,.48-.08,5.21,5.21,0,0,0,.75-.93,3,3,0,0,1,.71-.81,1.42,1.42,0,0,1,.88-.19,8.24,8.24,0,0,1,.93.15Zm0,5.88-.79,1.6.79.09V97.27Zm-6.12-6.19.22.1.6-1.42-.82-.38Zm0-1.7v1.7l-3.07-1.41-.44,1.06,3,1.4-.56,1.36-3-1.4-.55,1.31L36.57,95l-.63,1.5L32,94.66v-4.1l1.36-3.22,4.44,2Zm0,4.46L37,97l.82.24V93.84ZM32,88.42l.53.35.92-1.52L32,86.3Zm0-2.12v2.12l-1.32-.87-2.78,4.64-1.09-.72V89.79l2.05-3.43L27,85.14l.91-1.52L32,86.3Zm0,4.26L30.58,94l1.44.67v-4.1Zm-5.19-6.65.72-.81-.72-.7Zm-8.71-8.65,2-.58,1.38,2-3.4,5.39v-3.6l1.24-1.7-1.24.44V75.26Zm8.71,7.14-4.14-4.05-4.57,5.12v.36l1.4,1.37,3.57-4,1.73,1.69-3.58,4,1.57,1.54,4-4.51V82.4Zm0,7.39v1.68L26.1,91Zm-10-9.55L15.4,78.18l-1.13.41L13,76.74l5.14-1.48V77.2l-1.23.45.89,1.28.34-.47v3.6l-.69,1.09-1.32-1.91.72-1Zm1.29,3.23v.36L18,83.66Z"/>
                                </g>
                            </g>
                        </g>
                    </svg>
                </div>
                <div class="footer_menu">
{{--                    @if(request()->url() !== route('home'))--}}
{{--                        <div>--}}
{{--                            <a href="/" class="footer_title menu_link menu_link-footer">--}}
{{--                                Главная--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                    @endif--}}

                    <div class="footer_menu_list">

                        <ul class="menu_list_left">
                            {{--                            <li class="menu_item  wow slideInDown">--}}
                            {{--                                <div class="menu_item_svg">--}}
                            {{--                                    @svg("img/svg/procent.svg")--}}
                            {{--                                </div>--}}
                            {{--                                <a href="{{route('discount_products')}}" class="menu_link">Специальные предложения</a>--}}
                            {{--                            </li>--}}

                            <li class="menu_item wow slideInDown">
                                <div class="menu_item_svg"></div>
                                @if(request()->url() !== route('home'))
                                        <a href="/" class="menu_link menu_link-footer">
                                            Главная
                                        </a>
                                @endif
                            </li>
                            <li class="menu_item wow slideInDown">
                                <div class="menu_item_svg">
                                    @svg("img/svg/checkbox.svg")
                                </div>
                                <a href="{{route('products')}}" class="menu_link menu_link-footer">Товары</a>
                            </li>
                            <li class="menu_item wow slideInDown">
                                <div class="menu_item_svg">
                                    @svg("img/svg/geo-mark.svg")
                                </div>
                                <a href="{{route('shops_locations')}}" class="menu_link menu_link-footer">Наши
                                    аптеки</a>
                            </li>

                        </ul>

                        <ul class="menu_list_right">
                            <li class="menu_item wow slideInDown">
                                <a href="{{route('blog')}}" class="menu_link menu_link-footer">Блог</a>
                            </li>
                            {{--
                            <li class="menu_item wow slideInDown">
                                <a href="#" class="menu_link">Отзывы</a>
                            </li>
                            --}}
                            <li class="menu_item wow slideInDown">
                                <a href="{{route('contact')}}" class="menu_link menu_link-footer">Контакты</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="subscription wow slideInRight">
                    <div class="subscription_title">
                        Подписаться
                    </div>

                    <div class="subscription_sub_title">
                        Будь в курсе о появлениях новых товаров и скидок
                    </div>

                    <form class="subscription_form" id="form-subscribe">
                        @php
                            $input_param=[
                                "name"=>"email",
                                "atr" =>"",
                                "form_group_class"=>"",
                                "validation" => "required|email|maxlength:32",
                                "input_class"=>"",
                                "title"=>"Email"
                            ];
                        @endphp
                        @include("blocks.input.input")

                        @php
                            $btn_param=[
                                "name"=>"btn",
                                "btn_class"=>"tab_btn btn_white btn-subscribe-submit",
                                "text"=>"Подписаться"
                            ];
                        @endphp
                        @include("blocks.btn.btn")
                    </form>
                </div>
            </div>

            <div class="footer_info">
                <div class="footer_info_time">
                    <div class="footer_info_svg">

                    </div>
                    <div class="footer_info_time_text">
                        {!! string($strings, 'global_work_time', "placeholder") !!}
                    </div>
                </div>

                <div class="footer_info_phone">
                    <div class="footer_info_svg">
                        @svg("img/svg/phone-2.svg")
                    </div>
                    <div class="footer_info_phone_list">
                        @php
                            $phones = setting('site.phones');
                            $phones = explode('|', $phones);
                        @endphp
                        @foreach($phones as $phone)
                            <a href="tel:+{{phone_strip($phone)}}">{{$phone}}</a>
                        @endforeach
                    </div>
                </div>

                {{--<div class="footer_info_social">--}}
                {{--<a href="" class="footer_info_social_item">--}}
                {{--@svg("img/svg/facebook.svg")--}}
                {{--</a>--}}
                {{--<a href="" class="footer_info_social_item">--}}
                {{--@svg("img/svg/vk.svg")--}}
                {{--</a>--}}
                {{--<a href="" class="footer_info_social_item">--}}
                {{--@svg("img/svg/twitter.svg")--}}
                {{--</a>--}}
                {{--<a href="" class="footer_info_social_item">--}}
                {{--@svg("img/svg/vk.svg")--}}
                {{--</a>--}}
                {{--<a href="" class="footer_info_social_item">--}}
                {{--@svg("img/svg/youtube.svg")--}}
                {{--</a>--}}
                {{--<a href="" class="footer_info_social_item">--}}
                {{--@svg("img/svg/instagram.svg")--}}
                {{--</a>--}}
                {{--</div>--}}

                @php
                    $links = [];

                    $social = setting('site.social_link_fb');
                    if($social !== null){
                    $links[] = [
                                        "social_link" => $social,
                                        "path_svg" => "img/svg/facebook.svg",
                                        "social_item_class" => "footer_info_social_item wow zoomIn",
                                    ];
                    }
                    $social = setting('site.social_link_vk');
                    if($social !== null){
                    $links[] = [
                                        "social_link" => $social,
                                        "path_svg" => "img/svg/vk.svg",
                                        "social_item_class" => "footer_info_social_item wow zoomIn",
                                    ];

                    }
                    $social = setting('site.social_link_tw');
                    if($social !== null){
                    $links[] = [
                                        "social_link" => $social,
                                        "path_svg" => "img/svg/twitter.svg",
                                        "social_item_class" => "footer_info_social_item wow zoomIn",
                                    ];
                    }
                    $social = setting('site.social_link_ig');
                    if($social !== null){
                    $links[] = [
                                        "social_link" => $social,
                                        "path_svg" => "img/svg/instagram.svg",
                                        "social_item_class" => "footer_info_social_item wow zoomIn",
                                    ];
                    }
                    $social = setting('site.social_link_yt');
                    if($social !== null){
                    $links[] = [
                                        "social_link" => $social,
                                        "path_svg" => "img/svg/youtube.svg",
                                        "social_item_class" => "footer_info_social_item wow zoomIn",
                                    ];

                    }
                @endphp


                @include("blocks.social.link-group.social_list",[
				"social_list_class"=>"footer_info_social",
                "links"=>$links
                ])

            </div>
        </div>
    </div>

    <div class="footer_bottomLine">
        <div class="container footer footer_bottomList">
            <div class="footer_copyright">
                © 2001 - 2021 All rights reserved.
            </div>
            <div class="footer_link_list">
                {{--<a href="" class="footer_link_item">--}}
                {{--Sitemap--}}
                {{--</a>--}}
                <a href="#" rel="nofollow" class="footer_link_item btn_Policy">
                    {!! string($strings, 'privacy_policy_button_text', 'Политика обработки данных') !!}
                </a>
                {{--                <a href="#" class="footer_link_item btn_Term">
                                    Terms & Conditions
                                </a>--}}

            </div>
            @include("blocks.default.created.created")
        </div>

    </div>

    @include('blocks.default.default_top_btn.default_top_btn')

</footer>