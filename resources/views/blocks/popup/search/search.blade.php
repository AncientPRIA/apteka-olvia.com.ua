<div class="Popup Search">

    <div class="Popup_content search__container">

        <div class="search__content-wrapper no-category">
            <form action="/search"  class="search__form-search">
                <div class="search__form-group-search form-group-search">
                    <svg class="search__search-icon" width="26" height="27">
                        <use xlink:href="/public/img/svg/sprite-search.svg#search-icon"></use>
                    </svg>
                    <input id="search-input" class="search__search-input" placeholder="Введите нужный Вам товар..." type="text" autocomplete="off">
                    <svg class="search__search-btn search__search-close-icon" width="23" height="23" onclick="popup_close({cls:'Search', bodyUnfix: true});">
                        <use xlink:href="/public/img/svg/sprite-search.svg#close-icon"></use>
                    </svg>
                </div>
            </form>
            <div class="row search__row-main">
                <div class="search__col-catalog">
                    <div class="search__catalog">
                        <div class="search__catalog-item search__catalog-item_active">
                        <span class="search__catalog-item-title">
                            Все результаты
                        </span>
                            <span class="search__catalog-item-counter">
                            20
                        </span>
                        </div>
                        <div class="search__catalog-item">
                        <span class="search__catalog-item-title">
                            Красота и здоровье
                        </span>
                            <span class="search__catalog-item-counter">
                            2
                        </span>
                        </div>
                        <div class="search__catalog-item">
                        <span class="search__catalog-item-title">
                            Средства ухода и гигиены
                        </span>
                            <span class="search__catalog-item-counter">
                            12
                        </span>
                        </div>
                        <div class="search__catalog-item">
                        <span class="search__catalog-item-title">
                            Товары для дома
                        </span>
                            <span class="search__catalog-item-counter">
                            5
                        </span>
                        </div>
                        <div class="search__catalog-item">
                        <span class="search__catalog-item-title">
                            Медицинские изделия
                        </span>
                            <span class="search__catalog-item-counter">
                            1
                        </span>
                        </div>
                    </div>
                </div>
                <div class="row search__search-result-category-row">
                    {{--
                    <div class="search__col" style="">
                        <div class="search-result-category">
                            <div class="search-result-category__head">
                    <span class="search-result-category__head-title">
                        Товары для дома
                    </span>
                                <svg class="search-result-category__head-icon" width="28" height="16">
                                    <use xlink:href="/public/img/svg/sprite-search.svg#arrow-icon"></use>
                                </svg>
                            </div>
                            <div class="search-result-category__body">
                                <div class="search-result-category__item">
                                    <div class="search-result-category__item-button-buy">
                                        <svg class="search-result-category__item-button-buy-icon" width="19" height="12">
                                            <use xlink:href="/public/img/svg/sprite-search.svg#buy-icon"></use>
                                        </svg>
                                    </div>
                                    <div class="search-result-category__item-img-wrap">
                                        <div class="search-result-category__item-img-container">
                                          @php
                                                $sizes = array(
                                                                '1' => ['width' => 59, 'relative_path' => 'uploads/products/product-img.png', 'q'=> 60],
                                                                'id' => 'search-result-category__product-img',

                                                              );
                                                $mappings = array(
                                                                '>320' => '1',
                                                                'default' => '1'
                                                              );
                                                $sizes = Img::img($sizes);
                                                $picture = Img::picture_compose($sizes, $mappings, false, 'search-result-category__item-img', '', false);
                                            @endphp
                                            {!! $picture !!}

                                            <img class="search-result-category__item-img-watermark" src="/public/img/svg/logo.svg" alt="">
                                        </div>
                                    </div>
                                    <div class="search-result-category__item-info">
                                        <div class="search-result-category__item-title">
                                            Дезодорант для тела DRYDRY (Драй драй) 35 мл.
                                        </div>
                                        <div class="search-result-category__item-price-wrap">
                                            <div class="search-result-category__item-old-price">
                                                800 руб.
                                            </div>
                                            <div class="search-result-category__item-price">
                                                300 руб.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="search-result-category__item">
                                    <div class="search-result-category__item-button-buy">
                                        <svg class="search-result-category__item-button-buy-icon" width="19" height="12">
                                            <use xlink:href="/public/img/svg/sprite-search.svg#buy-icon"></use>
                                        </svg>
                                    </div>
                                    <div class="search-result-category__item-img-wrap">
                                        <div class="search-result-category__item-img-container">
                                          @php
                                                $sizes = array(
                                                                '1' => ['width' => 59, 'relative_path' => 'uploads/products/product-img.png', 'q'=> 60],
                                                                'id' => 'search-result-category__product-img',

                                                              );
                                                $mappings = array(
                                                                '>320' => '1',
                                                                'default' => '1'
                                                              );
                                                $sizes = Img::img($sizes);
                                                $picture = Img::picture_compose($sizes, $mappings, false, 'search-result-category__item-img', '', false);
                                            @endphp
                                            {!! $picture !!}

                                            <img class="search-result-category__item-img-watermark" src="/public/img/svg/logo.svg" alt="">
                                        </div>
                                    </div>
                                    <div class="search-result-category__item-info">
                                        <div class="search-result-category__item-title">
                                            Дезодорант для тела DRYDRY (Драй драй) 35 мл.
                                        </div>
                                        <div class="search-result-category__item-price-wrap">
                                            <div class="search-result-category__item-old-price">
                                                800 руб.
                                            </div>
                                            <div class="search-result-category__item-price">
                                                300 руб.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="search-result-category__item">
                                    <div class="search-result-category__item-button-buy">
                                        <svg class="search-result-category__item-button-buy-icon" width="19" height="12">
                                            <use xlink:href="/public/img/svg/sprite-search.svg#buy-icon"></use>
                                        </svg>
                                    </div>
                                    <div class="search-result-category__item-img-wrap">
                                        <div class="search-result-category__item-img-container">
                                          @php
                                                $sizes = array(
                                                                '1' => ['width' => 59, 'relative_path' => 'uploads/products/product-img.png', 'q'=> 60],
                                                                'id' => 'search-result-category__product-img',

                                                              );
                                                $mappings = array(
                                                                '>320' => '1',
                                                                'default' => '1'
                                                              );
                                                $sizes = Img::img($sizes);
                                                $picture = Img::picture_compose($sizes, $mappings, false, 'search-result-category__item-img', '', false);
                                            @endphp
                                            {!! $picture !!}

                                            <img class="search-result-category__item-img-watermark" src="/public/img/svg/logo.svg" alt="">
                                        </div>
                                    </div>
                                    <div class="search-result-category__item-info">
                                        <div class="search-result-category__item-title">
                                            Дезодорант для тела DRYDRY (Драй драй) 35 мл.
                                        </div>
                                        <div class="search-result-category__item-price-wrap">
                                            <div class="search-result-category__item-old-price">
                                                800 руб.
                                            </div>
                                            <div class="search-result-category__item-price">
                                                300 руб.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="search-result-category__item">
                                    <div class="search-result-category__item-button-buy">
                                        <svg class="search-result-category__item-button-buy-icon" width="19" height="12">
                                            <use xlink:href="/public/img/svg/sprite-search.svg#buy-icon"></use>
                                        </svg>
                                    </div>
                                    <div class="search-result-category__item-img-wrap">
                                        <div class="search-result-category__item-img-container">
                                          @php
                                                $sizes = array(
                                                                '1' => ['width' => 59, 'relative_path' => 'uploads/products/product-img.png', 'q'=> 60],
                                                                'id' => 'search-result-category__product-img',

                                                              );
                                                $mappings = array(
                                                                '>320' => '1',
                                                                'default' => '1'
                                                              );
                                                $sizes = Img::img($sizes);
                                                $picture = Img::picture_compose($sizes, $mappings, false, 'search-result-category__item-img', '', false);
                                            @endphp
                                            {!! $picture !!}

                                            <img class="search-result-category__item-img-watermark" src="/public/img/svg/logo.svg" alt="">
                                        </div>
                                    </div>
                                    <div class="search-result-category__item-info">
                                        <div class="search-result-category__item-title">
                                            Дезодорант для тела DRYDRY (Драй драй) 35 мл.
                                        </div>
                                        <div class="search-result-category__item-price-wrap">
                                            <div class="search-result-category__item-old-price">
                                                800 руб.
                                            </div>
                                            <div class="search-result-category__item-price">
                                                300 руб.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="search-result-category__more">
                                    <div class="search-result-category__more-content">
                                        еще
                                        <span class="search-result-category__more-count">
                                        1
                                    </span>
                                    </div>
                                    <div class="search-result-category__more-btn">
                                        <svg class="search-result-category__more-btn-icon" width="26" height="12">
                                            <use xlink:href="/public/img/svg/sprite-search.svg#carousel-btn-icon"></use>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    --}}
                </div>
                <div class="search__pagination-container"></div>
            </div>
        </div>
    </div>

</div>
<div class="Hider Register" onclick="popup_close({cls:'Register', bodyUnfix: true})"></div>


