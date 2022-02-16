<style>* {
        -ms-box-sizing: border-box;
        box-sizing: border-box
    }

    a, abbr, acronym, address, applet, article, aside, audio, b, big, blockquote, body, canvas, caption, center, cite, code, dd, del, details, dfn, div, dl, dt, em, embed, fieldset, figcaption, figure, footer, form, h1, h2, h3, h4, h5, h6, header, hgroup, html, i, iframe, img, ins, kbd, label, legend, li, mark, menu, nav, object, ol, output, p, pre, q, ruby, s, samp, section, small, span, strike, strong, sub, summary, sup, table, tbody, td, tfoot, th, thead, time, tr, tt, u, ul, var, video {
        margin: 0;
        padding: 0;
        border: 0;
        font-size: 100%;
        font: inherit;
        vertical-align: baseline
    }

    button {
        outline: none
    }

    .slick-slide {
        outline: none !important
    }

    article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {
        display: block
    }

    ol, ul {
        list-style: none
    }

    a {
        text-decoration: none
    }

    blockquote, q {
        quotes: none
    }

    blockquote:after, blockquote:before {
        content: "";
        content: none
    }

    q:after, q:before {
        content: "";
        content: none
    }

    table {
        border-collapse: collapse;
        border-spacing: 0
    }

    .header-top-bar {
        background-color: #fff;
        -webkit-box-pack: justify;
        -webkit-justify-content: space-between;
        -ms-flex-pack: justify;
        justify-content: space-between;
        height: 52px
    }

    .header-top-bar, .time-work {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center
    }

    .time-work__text {
        color: #999;
        font-family: Roboto, sans-serif;
        font-size: 13px;
        font-weight: 400;
        line-height: 12.97px
    }

    .watch-svg {
        width: 17px;
        height: 17px;
        fill: #48845c;
        margin-right: 6px
    }

    .phone-svg {
        width: 14px;
        height: 14px;
        fill: #156330;
        margin-right: 7px
    }

    .phone-list {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -webkit-flex-direction: column;
        -ms-flex-direction: column;
        flex-direction: column;
        position: absolute;
        top: 57px;
        visibility: hidden;
        opacity: 0;
        -webkit-transform: translateX(-50%);
        -ms-transform: translateX(-50%);
        transform: translateX(-50%);
        -webkit-transition: visibility .3s ease-in-out, opacity .3s ease-in-out, -webkit-transform .3s ease-in-out;
        transition: visibility .3s ease-in-out, opacity .3s ease-in-out, -webkit-transform .3s ease-in-out;
        transition: visibility .3s ease-in-out, opacity .3s ease-in-out, transform .3s ease-in-out;
        transition: visibility .3s ease-in-out, opacity .3s ease-in-out, transform .3s ease-in-out, -webkit-transform .3s ease-in-out;
        will-change: opacity, transform;
        color: #fff
    }

    .phone-list__item svg {
        display: none
    }

    .phone-list__link {
        color: #fff;
        font-family: Roboto, sans-serif;
        font-size: 20px;
        font-weight: 400;
        line-height: 12.97px;
        text-decoration: none;
        cursor: pointer
    }

    .phone-list-active {
        padding: 12px;
        top: 52px;
        left: 0;
        width: 100%;
        -webkit-box-align: start;
        -webkit-align-items: flex-start;
        -ms-flex-align: start;
        align-items: flex-start;
        z-index: 9;
        background-color: #ff5454;
        -webkit-transform: translateX(0);
        -ms-transform: translateX(0);
        transform: translateX(0);
        opacity: 1;
        visibility: visible;
        -webkit-transition: visibility .3s ease-in-out, opacity .3s ease-in-out, -webkit-transform .3s ease-in-out;
        transition: visibility .3s ease-in-out, opacity .3s ease-in-out, -webkit-transform .3s ease-in-out;
        transition: visibility .3s ease-in-out, opacity .3s ease-in-out, transform .3s ease-in-out;
        transition: visibility .3s ease-in-out, opacity .3s ease-in-out, transform .3s ease-in-out, -webkit-transform .3s ease-in-out
    }

    .phone-list-active .phone-list__item {
        padding-bottom: 10px
    }

    .header-top-bar-phone {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -webkit-justify-content: center;
        -ms-flex-pack: center;
        justify-content: center;
        width: 52px;
        height: 100%;
        background-color: rgba(0, 0, 0, 0);
        -webkit-transition: background-color .45s;
        transition: background-color .45s;
        margin-left: -10px
    }

    .header-top-bar-phone svg {
        width: 18px;
        height: 18px;
        fill: #f66
    }

    .header-top-bar-phone.active {
        background-color: #ff5454;
        -webkit-transition: background-color .45s;
        transition: background-color .45s
    }

    .header-top-bar-phone.active svg {
        fill: #fff
    }

    .social-list {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center
    }

    .social-list__item {
        text-decoration: none;
        margin-right: 6px;
        margin-left: 6px;
        cursor: pointer;
        -webkit-transform: scale(1);
        -ms-transform: scale(1);
        transform: scale(1)
    }

    .social-list__item, .social-list__item:hover {
        -webkit-transition: -webkit-transform .3s ease;
        transition: -webkit-transform .3s ease;
        transition: transform .3s ease;
        transition: transform .3s ease, -webkit-transform .3s ease
    }

    .social-list__item:hover {
        -webkit-transform: scale(.9);
        -ms-transform: scale(.9);
        transform: scale(.9)
    }

    .social-list__item svg {
        width: 14px;
        height: 14px;
        fill: #f66
    }

    header {
        height: 100%;
        position: relative;
        overflow: hidden
    }

    header .container_relative {
        z-index: 2
    }

    .header_bg {
        background: #fff;
        padding-bottom: 15%;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: 50%
    }


    .header-content {
        width: 74%;
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        margin-left: 25%;
        min-height: 320px;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -webkit-flex-direction: column;
        -ms-flex-direction: column;
        flex-direction: column;
        -webkit-box-pack: center;
        -webkit-justify-content: center;
        -ms-flex-pack: center;
        justify-content: center
    }

    .header-title {
        font-size: 38px
    }

    .header-subtitle, .header-title {
        color: #fff;
        font-family: Roboto, sans-serif;
        font-weight: 700
    }

    .header-subtitle {
        font-size: 28px
    }

    .header-desc {
        font-size: 18px;
        font-family: MuseoSansCyrl, sans-serif;
        font-weight: 700;
        color: #fff;
        max-width: 600px
    }

    .video_container {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background-size: cover !important;
        background-position: 50% 50% !important;
        z-index: 0;
        overflow: hidden
    }

    .video_container:before {
        content: "";
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background-color: rgba(28, 116, 48, .4);
        z-index: 1
    }

    .video {
        min-width: 100%;
        min-height: calc(100% + 2px);
        width: auto;
        height: auto;
        position: absolute;
        top: 50%;
        left: 50%;
        -webkit-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%)
    }

    .header_bg_white {
        display: block;
        position: absolute;
        background-size: cover;
        background-position: bottom;
        background-repeat: no-repeat;
        bottom: -2px;
        left: 0;
        right: 0;
        padding-bottom: 8%;
        z-index: 2
    }

    .menu-column-left {
        height: auto;
        width: 50%;
        max-width: 50%;
        margin-right: 12px
    }

    .menu-column-right {
        width: 50%;
        max-width: 50%;
        margin-left: 12px
    }

    .menu-bottom {
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -webkit-flex-direction: column;
        -ms-flex-direction: column;
        flex-direction: column
    }

    .menu-bottom .menu-column-left {
        width: 100%;
        max-width: 100%;
        margin-right: 0;
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: justify;
        -webkit-justify-content: space-between;
        -ms-flex-pack: justify;
        justify-content: space-between;
        padding-bottom: 12px
    }

    .menu-bottom .menu-column-right {
        width: 100%;
        max-width: 100%;
        margin-left: 0
    }

    .menu-top {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center;
        margin-top: 6px;
        margin-bottom: 20px
    }

    .menu-top__home-link {
        width: 100%;
        height: 100%;
        display: block
    }

    .menu {
        display: none;
        position: absolute;
        top: 0;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -webkit-flex-direction: column;
        -ms-flex-direction: column;
        flex-direction: column;
        z-index: 1000;
        background-color: #7cb933;
        width: 106%;
        left: -10px;
        -webkit-box-align: start;
        -webkit-align-items: flex-start;
        -ms-flex-align: start;
        align-items: flex-start;
        padding: 9px
    }

    .menu-link__icon {
        width: 10px;
        height: 10px;
        margin-right: 6px;
        display: block
    }

    .menu-link__icon svg {
        width: 100%;
        height: 100%;
        fill: #fff
    }

    .menu-link {
        padding: 12px;
        border-bottom: 1px solid;
        color: #fff;
        font-size: 13px;
        font-family: Roboto, sans-serif;
        font-weight: 700
    }

    .menu-bottom, .menu-link {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center
    }

    .menu-bottom .menu-column-right, .mobail_btn {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: justify;
        -webkit-justify-content: space-between;
        -ms-flex-pack: justify;
        justify-content: space-between
    }

    .mobail_btn .btn-cart {
        margin-left: 12px;
        margin-right: 12px
    }

    .menu-column-right .btn-autf, .menu-column-right .btn-cart, .menu-column-right .btn-favorit {
        display: none
    }

    .container_relative {
        position: relative
    }

    .menu-cat-btn {
        width: 100%;
        border-radius: 10px;
        background-color: #7cb933;
        font-weight: 700;
        font-size: 13px;
        color: #fff;
        padding: 10px;
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center;
        font-family: Roboto, sans-serif;
        cursor: pointer;
        z-index: 610
    }

    .menu-cat-btn svg {
        fill: #fff;
        width: 16px;
        height: 14px;
        margin-right: 6px
    }

    .menu-sub-item {
        font-size: 16px;
        padding-top: 10px;
        padding-bottom: 10px
    }

    .menu-cat-big {
        width: 100%;
        max-width: 1150px;
        top: 174px;
        overflow-x: auto;
        border-radius: 10px;
        min-height: 330px;
        max-height: 100%;
        background-color: #7cb933;
        position: absolute;
        z-index: 9999999;
        left: 50%;
        -webkit-transform: scale(1) translate(-50%, 18px);
        -ms-transform: scale(1) translate(-50%, 18px);
        transform: scale(1) translate(-50%, 18px);
        box-shadow: 0 7px 27px 2px rgba(0, 0, 0, .75);
        visibility: visible;
        opacity: 1;
        will-change: opacity, transform;
        -webkit-transition: visibility .3s ease-in-out, opacity .3s ease-in-out, -webkit-transform .3s ease-in-out;
        transition: visibility .3s ease-in-out, opacity .3s ease-in-out, -webkit-transform .3s ease-in-out;
        transition: visibility .3s ease-in-out, opacity .3s ease-in-out, transform .3s ease-in-out;
        transition: visibility .3s ease-in-out, opacity .3s ease-in-out, transform .3s ease-in-out, -webkit-transform .3s ease-in-out
    }

    .menu-cat-big:before {
        position: absolute;
        background-color: #7cb933;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
        width: 25px;
        height: 25px;
        content: "";
        top: -8px;
        left: 11px;
        z-index: -1
    }

    .menu-cat-big__left {
        width: 100%;
        height: 100%;
        overflow-x: auto;
        border-right: 1px solid #fff
    }

    .menu-sub-item__nav {
        margin-right: 5px
    }

    .menu-sub-item__nav, .menu-sub-item__nav-wrap {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center
    }

    .menu-sub-item__nav-wrap {
        width: 90%
    }

    .menu-sub-item__icon {
        width: 14px;
        height: 14px;
        margin-right: 6px
    }

    .menu-cat-big__right {
        display: none
    }

    .menu-sub-item a {
        color: #fff
    }

    .menu-sub-item-level-1, .menu-sub-item-level-2 {
        padding-bottom: 5px;
        padding-top: 5px
    }

    .menu-sub-item-level-1 > a, .menu-sub-item-level-1 > div a, .menu-sub-item-level-2 > a, .menu-sub-item-level-2 > div a {
        color: #fff;
        font-family: Roboto, sans-serif;
        font-weight: 700;
        font-size: 16px;
        padding-top: 10px;
        padding-bottom: 10px;
        display: block;
        width: 90%
    }

    .menu-sub-level-2 {
        display: none
    }

    .menu-sub-level-2.show_lvl_1 {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical !important;
        -webkit-box-direction: normal !important;
        -webkit-flex-flow: column nowrap !important;
        -ms-flex-flow: column nowrap !important;
        flex-flow: column nowrap !important
    }

    .toggle-menu-big {
        width: 10%;
        display: block;
        padding: 4px;
        cursor: pointer;
        -webkit-transform: rotate(0deg);
        -ms-transform: rotate(0deg);
        transform: rotate(0deg);
        transform-z-index: 9999
    }

    .toggle-menu-big, .toggle-menu-big_open {
        -webkit-transition: -webkit-transform .3s ease-in-out;
        transition: -webkit-transform .3s ease-in-out;
        transition: transform .3s ease-in-out;
        transition: transform .3s ease-in-out, -webkit-transform .3s ease-in-out
    }

    .toggle-menu-big_open {
        -webkit-transform: rotate(90deg);
        -ms-transform: rotate(90deg);
        transform: rotate(90deg)
    }

    .menu-sub-item-level-2 {
        width: 100%;
        margin-right: 20px;
        margin-bottom: 12px;
        border-bottom: 1px solid #fff
    }

    .menu-sub-level-1 {
        width: 100%;
        overflow-x: hidden;
        height: 100%;
        padding: 20px
    }

    .menu-sub-item-level-1 {
        padding: 10px;
        border-bottom: 1px solid #fff
    }

    .menu-sub-item-level-1:last-child {
        border: none
    }

    .menu-sub-level-1 {
        display: none
    }

    .menu-cat-big_hide {
        visibility: hidden;
        opacity: 0;
        will-change: opacity, transform;
        z-index: -1;
        -webkit-transform: scale(.8) translateY(-40px);
        -ms-transform: scale(.8) translateY(-40px);
        transform: scale(.8) translateY(-40px);
        -webkit-transition: visibility .3s ease-in-out, opacity .3s ease-in-out, -webkit-transform .3s ease-in-out;
        transition: visibility .3s ease-in-out, opacity .3s ease-in-out, -webkit-transform .3s ease-in-out;
        transition: visibility .3s ease-in-out, opacity .3s ease-in-out, transform .3s ease-in-out;
        transition: visibility .3s ease-in-out, opacity .3s ease-in-out, transform .3s ease-in-out, -webkit-transform .3s ease-in-out
    }

    .menu-sub-item-level-3 {
        padding-left: 10px
    }

    .form-search {
        width: 565px;
        height: 45px
    }

    .input-search {
        outline: none;
        width: 100%;
        border: none;
        border-radius: 20px;
        background-color: #fff;
        color: #868686;
        padding-left: 10px;
        padding-right: 10px;
        height: 35px
    }

    .form-group-search {
        position: relative
    }

    .search-btn {
        width: 26px;
        height: 16px;
        position: absolute;
        top: 9px;
        right: 13px;
        z-index: 5;
        cursor: pointer;
        border: none;
        background: rgba(0, 0, 0, 0);
        display: block
    }

    .search-btn svg {
        width: 100%;
        height: 100%;
        fill: #b3b34e
    }

    .cmn-toggle-switch {
        float: right;
        display: block;
        position: relative;
        overflow: hidden;
        margin: 0;
        padding: 0;
        width: 56px;
        height: 48px;
        font-size: 0;
        text-indent: -9999px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        box-shadow: none;
        border-radius: none;
        border: none;
        cursor: pointer;
        -webkit-transition: all background .3s;
        transition: all background .3s
    }

    .cmn-toggle-switch:focus {
        outline: none
    }

    .cmn-toggle-switch__htx {
        background-color: rgba(0, 0, 0, 0);
        border-radius: 12px;
        border: 3px solid #fff;
        z-index: 1010
    }

    .cmn-toggle-switch span {
        display: block;
        position: absolute;
        top: 19px;
        left: 18px;
        right: 18px;
        height: 3px;
        background: #fff
    }

    .cmn-toggle-switch span:after, .cmn-toggle-switch span:before {
        position: absolute;
        display: block;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: #fff;
        content: ""
    }

    .cmn-toggle-switch span:before {
        top: -14px
    }

    .cmn-toggle-switch span:after {
        bottom: -14px
    }

    .cmn-toggle-switch__htx span {
        -webkit-transition: background .3s ease;
        transition: background .3s ease
    }

    .cmn-toggle-switch__htx span:after, .cmn-toggle-switch__htx span:before {
        -webkit-transition-duration: .3s, .3s;
        transition-duration: .3s, .3s;
        -webkit-transition-delay: .3s, 0;
        transition-delay: .3s, 0
    }

    .cmn-toggle-switch__htx span:before {
        -webkit-transition-property: top, -webkit-transform;
        transition-property: top, -webkit-transform;
        transition-property: top, transform;
        transition-property: top, transform, -webkit-transform
    }

    .cmn-toggle-switch__htx span:after {
        -webkit-transition-property: bottom, -webkit-transform;
        transition-property: bottom, -webkit-transform;
        transition-property: bottom, transform;
        transition-property: bottom, transform, -webkit-transform
    }

    .cmn-toggle-switch__htx.active {
        background-color: #fff
    }

    .cmn-toggle-switch__htx.active span {
        background: none
    }

    .cmn-toggle-switch__htx.active span:before {
        top: 0;
        background-color: #5c0b4d;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg)
    }

    .cmn-toggle-switch__htx.active span:after {
        bottom: 0;
        background-color: #5c0b4d;
        -webkit-transform: rotate(-45deg);
        -ms-transform: rotate(-45deg);
        transform: rotate(-45deg)
    }

    .cmn-toggle-switch__htx.active span:after, .cmn-toggle-switch__htx.active span:before {
        -webkit-transition-delay: 0, .3s;
        transition-delay: 0, .3s
    }

    .btn-cart {
        border-radius: 100%;
        width: 35px;
        height: 35px;
        background-color: #7cb933;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -webkit-justify-content: center;
        -ms-flex-pack: center;
        justify-content: center;
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        cursor: pointer;
        position: relative;
        -webkit-tap-highlight-color: transparent
    }

    .btn-cart svg {
        width: 14px;
        height: 14px;
        fill: #fff
    }

    .cart-counter {
        background-color: #f66;
        width: 16px;
        height: 16px;
        border-radius: 100%;
        position: absolute;
        top: -5px;
        right: -4px;
        color: #fff;
        font-size: 10px;
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -webkit-justify-content: center;
        -ms-flex-pack: center;
        justify-content: center
    }

    .btn-cart, .btn-cart:hover {
        -webkit-transition: background-color .3s ease-in-out;
        transition: background-color .3s ease-in-out
    }

    .btn-cart:hover {
        background-color: #f66
    }

    .btn-autf {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center;
        color: #fff;
        font-family: Roboto, sans-serif;
        font-size: 0;
        font-weight: 700;
        cursor: pointer
    }

    .btn-autf svg {
        width: 35px;
        height: 35px;
        fill: #fff;
        margin-right: 6px
    }

    .btn-autf svg path {
        fill: #fff
    }

    .btn-close {
        width: 15px;
        cursor: pointer;
        -webkit-transform: scale(1);
        -ms-transform: scale(1);
        transform: scale(1);
        -webkit-transition: all .3s;
        transition: all .3s
    }

    @media screen and (min-width: 600px) {
        .menu-cat-big {
            top: 187px
        }
    }

    @media screen and (min-width: 768px) {
        .menu-cat-big {
            top: 217px
        }

        .menu-cat-wrap {
            width: 23%
        }

        .menu-cat-big {
            max-height: 330px;
            overflow-x: unset
        }

        .menu-cat-big__left {
            width: 25%;
            height: 330px
        }

        .menu-cat-big__right {
            width: 75%;
            height: 100%
        }

        .menu-sub-level-1 {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            -webkit-flex-flow: column wrap;
            -ms-flex-flow: column wrap;
            flex-flow: column wrap;
            position: absolute;
            left: 25%;
            width: 75%;
            overflow-x: auto;
            height: 330px;
            padding: 20px;
            top: 0;
            visibility: hidden;
            opacity: 0;
            -webkit-transition: opacity .2s ease-in-out, visibility .2s ease-in-out;
            transition: opacity .2s ease-in-out, visibility .2s ease-in-out
        }

        .menu-sub-level-2 {
            display: none
        }

        .show_lvl_1 {
            visibility: visible;
            opacity: 1;
            -webkit-transition: opacity .2s ease-in-out, visibility .2s ease-in-out;
            transition: opacity .2s ease-in-out, visibility .2s ease-in-out
        }

        .menu-sub-item-level-2 {
            width: 29%;
            z-index: 2;
            max-height: 100%;
            -webkit-transition: max-height .6s ease;
            transition: max-height .6s ease;
            will-change: max-height
        }

        .menu-sub-item-level-3 {
            padding-left: 0
        }

        .menu-sub-level-2 {
            display: block
        }

        .menu-sub-level-2.show_lvl_1 {
            z-index: 1;
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical !important;
            -webkit-box-direction: normal !important;
            -webkit-flex-flow: column nowrap !important;
            -ms-flex-flow: column nowrap !important;
            flex-flow: column nowrap !important
        }
    }

    @media screen and (min-width: 769px) {
        .header-top-bar-phone {
            display: none
        }

        .phone-list {
            list-style: none;
            padding: 0;
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            -webkit-flex-direction: row;
            -ms-flex-direction: row;
            flex-direction: row;
            position: relative;
            top: 0;
            margin-left: 0;
            visibility: visible;
            opacity: 1;
            -webkit-transform: translateX(-50%);
            -ms-transform: translateX(0);
            transform: translateX(0)
        }

        .phone-list, .phone-list__item {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex
        }

        .phone-list__item {
            padding-left: 27px;
            font-size: 9px;
            -webkit-box-align: center;
            -webkit-align-items: center;
            -ms-flex-align: center;
            align-items: center
        }

        .phone-list__link {
            color: #999;
            font-family: Roboto, sans-serif;
            font-size: 9px;
            font-weight: 400;
            line-height: 12.97px;
            text-decoration: none;
            cursor: pointer
        }

        .phone-list__link, .phone-list__link:hover {
            -webkit-transition: color .3s ease;
            transition: color .3s ease
        }

        .phone-list__link:hover {
            color: #f66
        }

        .phone-list__call {
            color: #f66;
            font-family: Roboto;
            font-size: 9px;
            font-weight: 700;
            line-height: 13.99px;
            border-bottom: 1px dotted #f66;
            cursor: pointer
        }

        .phone-list__call, .phone-list__call:hover {
            -webkit-transition: color .3s ease, border-bottom .3s ease;
            transition: color .3s ease, border-bottom .3s ease
        }

        .phone-list__call:hover {
            border-bottom: 1px dotted #999;
            color: #999
        }
    }

    @media (min-width: 992px) {
        .menu-column-left {
            height: 46px;
            width: 24%;
            max-width: 254px;
            padding-bottom: 0
        }

        .menu-column-right {
            width: 74%;
            max-width: 839px
        }

        .menu-bottom {
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            -webkit-flex-direction: row;
            -ms-flex-direction: row;
            flex-direction: row
        }

        .menu-bottom .menu-column-left {
            width: 24%;
            max-width: 254px;
            margin-left: 12px;
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: start;
            -webkit-align-items: flex-start;
            -ms-flex-align: start;
            align-items: flex-start;
            -webkit-box-pack: justify;
            -webkit-justify-content: space-between;
            -ms-flex-pack: justify;
            justify-content: space-between;
            padding-bottom: 0
        }

        .menu-bottom .menu-column-right {
            width: 74%;
            max-width: 839px;
            margin-left: 12px
        }

        .menu {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -webkit-align-items: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            -webkit-flex-direction: row;
            -ms-flex-direction: row;
            flex-direction: row;
            -webkit-box-pack: justify;
            -webkit-justify-content: space-between;
            -ms-flex-pack: justify;
            justify-content: space-between;
            position: relative;
            top: 0;
            background-color: rgba(0, 0, 0, 0);
            width: auto;
            left: 0
        }

        .menu-link {
            padding: 0;
            border-bottom: none
        }

        .mobail_btn {
            display: none
        }

        .menu-column-right .btn-autf, .menu-column-right .btn-cart, .menu-column-right .btn-favorit {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex
        }

        .cmn-toggle-switch {
            display: none
        }
    }

    @media screen and (min-width: 996px) {
        .phone-list__item {
            font-size: 13px
        }

        .phone-list__item svg {
            display: block
        }

        .phone-list__link {
            font-size: 13px !important
        }

        .phone-list__call {
            font-size: 14px !important
        }

        .header_bg {
            background-position: 100%
        }

        .header-content {
            -webkit-box-pack: end;
            -webkit-justify-content: flex-end;
            -ms-flex-pack: end;
            justify-content: flex-end
        }

        .menu-cat-wrap {
            width: 91%
        }

        .menu-cat-big {
            top: 174px
        }

        .transparent_header .menu-cat-big {
            top: 160px
        }

        .search-btn {
            -webkit-transform: scale(1);
            -ms-transform: scale(1);
            transform: scale(1)
        }

        .search-btn, .search-btn:hover {
            -webkit-transition: -webkit-transform .3s ease-in-out;
            transition: -webkit-transform .3s ease-in-out;
            transition: transform .3s ease-in-out;
            transition: transform .3s ease-in-out, -webkit-transform .3s ease-in-out
        }

        .search-btn:hover {
            -webkit-transform: scale(1.2);
            -ms-transform: scale(1.2);
            transform: scale(1.2)
        }
    }

    @media (min-width: 996px) {
        .btn-close:hover {
            -webkit-transform: scale(1.2);
            -ms-transform: scale(1.2);
            transform: scale(1.2);
            -webkit-transition: all .3s;
            transition: all .3s
        }
    }

    @media screen and (min-width: 1200px) {
        .form-search {
            -webkit-flex-shrink: 0;
            -ms-flex-negative: 0;
            flex-shrink: 0
        }
    }

    @media (min-width: 1200px) {
        .btn-autf {
            font-size: 15px
        }

        .btn-autf svg {
            width: 15px;
            height: 15px
        }
    }</style>