{{-- MARK FOR TRANSFER --}}

<form id="form-profile" class="form form-profile hider_class" action="#">
    {{-- Login --}}
    <div class="form-group input_mgb" data-validation="minlength:3|maxlength:32">
        <div class="input-container">
            <label class="label @if(isset($user->name) && $user->name !== null){{'label_active'}}@endif" for="name">{!! string($strings, 'form label name', 'Ваше Имя') !!}</label>
            <input class="input autofill-disable" readonly value="{{$user->name ?? ''}}" name="name" type="text" minlength="3" maxlength="32">
        </div>
        <div class="input_error"></div>
    </div>
    @php $user = user_verified() @endphp
    @if($user !== false && $user->auth_type === '0')

        <div class="form-group input_mgb" data-validation="email|minlength:3|maxlength:32">
            <div class="input-container">
                <label class="label @if(isset($user->email) && $user->email !== null){{'label_active'}}@endif" for="email">{!! string($strings, 'form label email', 'placeholder') !!}</label>
                <input class="input autofill-disable" readonly value="{{$user->email ?? ''}}" name="email" type="text" minlength="3" maxlength="32">
            </div>
            <div class="input_error"></div>
        </div>

        <div class="form-group input_mgb" data-validation="minlength:8|maxlength:32">
            <div class="input-container">
                <label class="label" for="password">{!! string($strings, 'form label password', 'placeholder') !!}</label>
                <input class="input autofill-disable" readonly name="password" type="password" minlength="8" maxlength="32">
            </div>
            <div class="input_error"></div>
        </div>

        <div class="form-group input_mgb" data-validation="minlength:8|maxlength:32">
            <div class="input-container">
                <label class="label" for="password_confirmation">{!! string($strings, 'form label password_confirmation', 'placeholder') !!}</label>
                <input class="input autofill-disable" readonly name="password_confirmation" type="password" minlength="8" maxlength="32">
            </div>
            <div class="input_error"></div>
        </div>
    @endif

    <div class="form-group input_mgb" data-validation="">
        <div class="input-container">
            <label class="label @if(isset($user->phone) && $user->phone !== null){{'label_active'}}@endif" for="phone">{!! string($strings, 'form label your phone', 'Ваш номер') !!}</label>
            <input class="input autofill-disable" value="{{$user->phone ?? ''}}" readonly name="phone" type="text">
        </div>
        <div class="input_error"></div>
    </div>

    <div class="form-group hidden input_mgb">
        <div class="input-container">
            <input class="input autofill-disable" readonly value="{{$user->id}}" name="user_id" type="hidden">
        </div>
    </div>


    <div class="form-group-btn">
        <div class="general-error"></div>
        <button class="btn btn-upt">{!! string($strings, 'form button accept', 'Применить') !!}</button>
    </div>


</form>