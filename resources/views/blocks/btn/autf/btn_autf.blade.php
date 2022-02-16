@php
	$user = user_verified();
@endphp
@if(!$user)
<div class="btn-autf {{isset($class)? $class:""}}" onclick="popup_show({cls:'Autf', scrollOff:'body' });">
	@svg("img/svg/user.svg")
	Вход | Регистрация
</div>
@else
	<a class="btn-autf {{isset($class)? $class:""}}" href="{{route('profile')}}" >
		@svg("img/svg/user.svg")
		Профиль
	</a>
@endif