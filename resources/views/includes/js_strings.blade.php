{{-- for errors always use 'error_{validation key}' format --}}
<script>

    var js_strings = {
        // Errors
        'error_required'                : '{!! string($strings, 'js error_required', 'Необходимо заполнить это поле') !!}',
        'error_email'                   : '{!! string($strings, 'js error_email', 'E-mail не корректен') !!}',
        'error_phone'                   : '{!! string($strings, 'js error_phone', 'Телефон не корректен') !!}',
        'error_minlength'               : '{!! string($strings, 'js error_minlength', 'Минимальная длинна поля %s символов') !!}',
        'error_maxlength'               : '{!! string($strings, 'js error_maxlength', 'Максимальная длина поля %s символов') !!}',
        'error_password'                : '{!! string($strings, 'js error_password', 'Пароль не корректен') !!}',
        'error_password_confirmation'   : '{!! string($strings, 'js error_password_confirmation', 'Пароль не совпадает') !!}',
        //'error_agree'               : '{!! string($strings, 'js error agree', 'Вы должны согласиться с условиями') !!}',
        'error_wrong_credentials'       : '{!! string($strings, 'js error_wrong_credentials', 'Неверный email или пароль') !!}',
        'error_wrong_email'             : '{!! string($strings, 'js error_wrong_email', 'Неверный email') !!}',
        'error_verification'            : '{!! string($strings, 'js error_verification', 'Вы не верифицированы, проверьте почту') !!}',
        'error_exists'                  : '{!! string($strings, 'js error_exists', 'Этот email уже занят') !!}',
        'error_not_found'               : '{!! string($strings, 'js error_not_found', 'Объект не найден') !!}',
        'error_mail_fail'               : '{!! string($strings, 'js error_mail_fail', 'Не удалось отправить сообщение. Повторите ещё раз или свяжитесь с администратором') !!}',

        // Profile
        'registration_success'          : '{!! string($strings, 'js registration_success', 'Вы зарегестрированы, перезагрузите страницу') !!}',
        'login_success'                 : '{!! string($strings, 'js login_success', 'placeholder') !!}',
        'profile_update_success'        : '{!! string($strings, 'js profile_update_success', 'Ваши данные обновлены') !!}',

        // Cart
        'error_shop_empty'              : '{!! string($strings, 'js error_shop_empty', 'Вы не выбрали аптеку') !!}',
        'order_success'                 : '{!! string($strings, 'js order_success', 'Ваш заказ отправлен') !!}',


        //Modal politica
        'field_modal'                   : '{!! string($strings, 'js field modal', 'We use cookies to ensure the best performance and the most relevant ads. <br> By using this site, you accept the use of cookies. <button class = "btn-more"> Learn more </button>') !!}',

        // Other
        'success_check_availability'    : '{!! string($strings, 'js success_check_availability', 'Запрос отправлен') !!}',
        'success_review_submit'         : '{!! string($strings, 'js success_review_submit', 'Отзыв добавлен') !!}',
        'success_contact_submit'        : '{!! string($strings, 'js success_contact_submit', 'Сообщение отправлено') !!}',
        'success_callback_submit'       : '{!! string($strings, 'js success_callback_submit', 'Мы скоро вам позвоним!') !!}',
        'success_password_reset'        : '{!! string($strings, 'js success_password_reset', 'Ссылка на сброс пароля отправлена на почту') !!}',
        // ...
    };

</script>