@extends($activeTemplate . 'layouts.frontend')

@section('content')
    @php
        $login = getContent('login.content', true);
    @endphp

    <section class="pt-80 pb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="account-area">
                        <div class="left text-center">
                            <img src="{{ siteLogo() }}" alt="logo">
                        </div>
                        <div class="right">
                            <div class="text-center">
                                @include($activeTemplate . 'partials.social_login')
                            </div>
                            <form class="account-from verify-gcaptcha" action="{{ route('user.login') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label>@lang('Username')</label>
                                    <input class="form-control" name="username" type="text" value="{{ old('username') }}" placeholder="@lang('Username')">
                                </div>
                                <div class="form-group">
                                    <label>@lang('Password')</label>
                                    <input class="form-control" name="password" type="password" placeholder="@lang('Password')">
                                </div>
                                <x-captcha />
                                <div class="text-center">
                                    <button class="cmn-btn w-100" type="submit">@lang('Login')</button>
                                </div>
                                <p class="mt-3">@lang('Forgot password?') <a class="base--color" href="{{ route('user.password.request') }}">@lang('Reset now')</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
