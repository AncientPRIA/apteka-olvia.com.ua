@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('stock.Verify Your Email Address') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('stock.A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    {{ __('stock.Before proceeding, please check your email for a verification link.') }}
                    {{ __('stock.If you did not receive the email') }}, <a href="{{ route('verification.resend_'.config('app.locale_current')) }}">{{ __('stock.click here to request another') }}</a>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
