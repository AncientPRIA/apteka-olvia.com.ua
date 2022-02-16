<!DOCTYPE html>
<html lang="ru-Ru">
<head>
    <title>Seven</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="GENERATOR" content="Microsoft FrontPage 4.0">
    <meta name="ProgId" content="FrontPage.Editor.Document">
    <link rel="shortcut icon" href="./icon.ico">
    <link rel="stylesheet" href="{{ URL::asset('/css/reset.css') }}">
    <script src="{{ asset('js/stock/app.js') }}" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('includes.js_strings')
    @include('includes.js_locale')
</head>
<body>
<div class="row decor_restrict">
    <div class="decor_circle" style="background-image: url('{{asset('uploads/design/circle_part.png')}}')"></div>
    <div class="column-left">
        <img class="logo" src="{{asset('/img/logo.png')}}" alt="">
        <div class="form-header"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18.88 19.67"><defs><style>.afb01c62-28b9-420e-bd56-d0d22bf451d3{fill:#0cf;stroke:#0cf;stroke-miterlimit:22.93;stroke-width:0.5px;fill-rule:evenodd;}</style></defs><title>Ресурс 3</title><g id="bbb603ec-660a-46c3-b104-d22d2dd5d7b8" data-name="Слой 2"><g id="adaeefb4-cc95-49ae-b4d2-46daccbeeeba" data-name="Layer 1"><path class="afb01c62-28b9-420e-bd56-d0d22bf451d3" d="M9.36,10.86H9.5a4.36,4.36,0,0,0,3.16-1.24c1.72-1.77,1.43-4.81,1.4-5.1A4.09,4.09,0,0,0,11.89.83,5.28,5.28,0,0,0,9.49.25H9.41A5.33,5.33,0,0,0,7,.81,4.09,4.09,0,0,0,4.81,4.52c0,.29-.32,3.33,1.4,5.1a4.32,4.32,0,0,0,3.15,1.24ZM6,4.63v0A3.14,3.14,0,0,1,9.4,1.36h.06a3.15,3.15,0,0,1,3.4,3.23v0s.32,2.8-1.1,4.26a3.1,3.1,0,0,1-2.31.87h0a3.08,3.08,0,0,1-2.3-.87C5.69,7.43,6,4.65,6,4.63Z"/><path class="afb01c62-28b9-420e-bd56-d0d22bf451d3" d="M18.63,13.6v0s0-.09,0-.14c0-1.16-.08-3.87-2-4.74l0,0a11.85,11.85,0,0,1-3.71-2.21.51.51,0,0,0-.84.19.94.94,0,0,0,.14,1.1,13,13,0,0,0,4.09,2.44c1,.49,1.16,2,1.19,3.28v.15a13.22,13.22,0,0,1-.09,1.81,13.58,13.58,0,0,1-7.89,2.4,13.62,13.62,0,0,1-7.89-2.41,14.85,14.85,0,0,1-.1-1.81s0-.09,0-.14c0-1.34.15-2.8,1.19-3.28A12.68,12.68,0,0,0,6.73,7.75a.94.94,0,0,0,.15-1.1A.52.52,0,0,0,6,6.46,11.78,11.78,0,0,1,2.33,8.67l-.05,0c-1.94.88-2,3.59-2,4.74a.76.76,0,0,1,0,.15h0a11.82,11.82,0,0,0,.23,2.66.71.71,0,0,0,.23.37,14.19,14.19,0,0,0,8.74,2.8,14.17,14.17,0,0,0,8.73-2.8.78.78,0,0,0,.23-.37A12.91,12.91,0,0,0,18.63,13.6Z"/></g></g></svg>
            <spant class="form-header-text">{!! string($strings, 'login reset', 'Восстановление аккаунта') !!}</spant>
        </div>

        <form id="form-reset" class="form form-login" method="POST" action="{{ route('password.email_'.config('app.locale_current')) }}">
            @csrf

            <div class="form-group" data-validation="required|email|maxlength:32">
                <div class="input-container">
                    <label class="label" for="email">{!! string($strings, 'form label email', 'Ваш email') !!}</label>
                    <input id="email" class="input input-email autofill-disable @error('email') is-invalid @enderror" name="email" type="text" maxlength="32" >
                </div>
                <div class="input_error"></div>
            </div>

            <div class="form-group-btn">
                <div class="general-error"></div>
                <button class="btn btn-login" type="submit">{!! string($strings, 'form button reset', 'ОТПРАВИТЬ') !!}</button>
            </div>

            <div class="form-text">
                На Ваш email будет отправлен сгенерированный новый пароль
            </div>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            @error('email')
            <span class="alert invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror

        </form>

        <div class="footer-right">
            <div class="footer-right-top"><svg class="footer-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26.46 24.76"><defs><style>.af7dd540-0ca7-4b5b-9990-996115b142b9{fill:#6a6a6a;fill-rule:evenodd;}</style></defs><title>filter2time</title><g id="b62af4c9-4036-419e-84fc-3cb7da44a30b" data-name="Слой 2"><g id="fcf0d580-55da-49b8-83cf-14ed56d23400" data-name="Layer 1"><path class="af7dd540-0ca7-4b5b-9990-996115b142b9" d="M14.67,0A11.77,11.77,0,0,0,5,5,5.64,5.64,0,0,1,6.31,6.22a10,10,0,0,1,7.15-4.41l1.21,2,1.21-2a10.07,10.07,0,0,1,8.77,8.77l-2,1.21,2,1.21A10.07,10.07,0,0,1,20,20.28a3,3,0,0,1,.19,1.9A11.79,11.79,0,0,0,14.67,0Zm3,19.41c2.71,2.19.63,3.64-.57,4.84-1.38,1.38-6.51.07-11.59-5S-.86,9,.51,7.65c1.2-1.19,2.65-3.28,4.84-.56s1.11,3.48-.12,4.71c-.86.86.93,3,2.81,4.92S12.1,20.4,13,19.54c1.24-1.24,2-2.31,4.72-.13ZM9.4,13.63c0-2.41,2.83-2.84,2.83-3.79a.65.65,0,0,0-.71-.64,1.26,1.26,0,0,0-1,.69l-1.19-.8a2.53,2.53,0,0,1,2.31-1.4,2,2,0,0,1,2.23,2c0,2-2.63,2.43-2.68,3.28H14v1.37H9.47a4.73,4.73,0,0,1-.07-.71Zm5.27-1.84,2.53-4h2v3.64h.77v1.32h-.77v1.59H17.57V12.75h-2.9v-1Zm2.9-.36H16.3v0l1-1.5a3.86,3.86,0,0,0,.32-.68h0a5.45,5.45,0,0,0-.06.73Z"/></g></g></svg>
                <div class="footer-right-top-info"><a class="footer-link-phone" href="tel:+380507777100" rel="noindex">+38 050 77 77 100</a>
                    <div class="footer-link-text">{!! string($strings, 'login footer text', 'Горячая линия 24/7') !!}</div>
                </div>
            </div>
            <div class="footer-right-bottom">{!! string($strings, 'login footer copyright', 'ІНТЕРСКЛО © 2019. Усі права застережено.') !!}</div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    {{--<script src="{{ URL::asset('/js/login.js') }}"></script>--}}
    <script src="{{ URL::asset('/js/includes/form.js') }}"></script>
</div>

</body>
</html>