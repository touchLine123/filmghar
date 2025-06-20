@extends($activeTemplate . 'layouts.auth')
@section('app')
    @php
        $email = getContent('reset_password_email.content', true);
    @endphp
    <section class="account-section bg-overlay-black bg_img" data-background="{{ frontendImage('reset_password_email', @$email->data_values->background_image, '1780x760') }}">
        <div class="container">
            <div class="row account-area align-items-center justify-content-center">
                <div class="col-md-8 col-lg-7 col-xl-5">
                    <div class="card custom--card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between flex-wrap">
                                <h5 class="title">{{ __($pageTitle) }}</h5>
                                <a class="text-white" href="{{ route('home') }}">@lang('Go Home')</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <p>@lang('To recover your account please provide your email or username to find your account.')</p>
                            </div>
                            <form method="POST" action="{{ route('vendor.password.reset') }}" class="verify-gcaptcha">
                                @csrf
                                <div class="form-group">
                                    <label class="form-label">@lang('Email or Username')</label>
                                    <input class="form-control form--control" name="email" type="text" value="{{ old('value') }}" required autofocus="off">
                                </div>
                                <x-captcha />
                                <div class="form-group mb-0">
                                    <button class="btn btn--base w-100" type="submit">@lang('Submit')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
