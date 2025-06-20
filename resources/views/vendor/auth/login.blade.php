@extends($activeTemplate . 'layouts.auth')
@section('app')
    @php
        $login = getContent('login.content', true);
    @endphp
    <section class="account-section bg-overlay-black bg_img" data-background="{{ getImage('assets/images/frontend/login/' . @$login->data_values->background_image, '1780x760') }}">
        <div class="container">
            <div class="row account-area align-items-center justify-content-center">
                <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-8">
                    <div class="account-form-area">
                        <div class="account-logo-area text-center">
                            <div class="account-logo">
                                <a href="{{ route('home') }}"><img src="{{ siteLogo() }}" alt="logo"></a>
                            </div>
                        </div>
                       
                        <form class="account-form verify-gcaptcha" method="POST" action="{{ route('vendor.login') }}">
                            @csrf
                            <div class="form-group">
                                <label>@lang('Username Or Email')</label>
                                <input class="form-control form--control" name="username" type="text" value="{{ old('username') }}" required>
                            </div>
                            <div class="form-group">
                                <label>{{ __('Password') }}</label>
                                <input class="form-control form--control" id="password" name="password" type="password" required>
                            </div>

                            <x-captcha />

                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="checkbox-wrapper mt-0">
                                    <div class="checkbox-item custom--checkbox">
                                        <input class="checkbox--input" type="checkbox" name="remember" id="remember"
                                               {{ old('remember') ? 'checked' : '' }}>
                                        <label class="checkbox--label" for="remember">
                                            @lang('Remember Me')
                                        </label>
                                    </div>
                                </div>
                                <a href="{{ route('vendor.password.reset') }}" class="text--base fs-12">@lang('Forgot Your Password?')</a>
                            </div>
                            <button class="submit-btn" id="recaptcha" type="submit">@lang('Login')</button>
                        </form>

                        @if (gs('registration'))
                            <div class="text-center">
                                <div class="account-item mt-10">
                                    <label>@lang("Don't Have An Account?") <a class="text--base" href="{{ route('vendor.register') }}">@lang('Register Now')</a></label>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script')
    <script>
        "use strict";

        function submitUserForm() {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {
                document.getElementById('g-recaptcha-error').innerHTML = '<span class="text-danger">@lang('Captcha field is required.')</span>';
                return false;
            }
            return true;
        }
    </script>
@endpush
