@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pt-80 pb-80">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-lg-7 col-xl-5 mx-auto">
                    <div class="card custom--card">
                        <div class="card-body">
                            <div class="mb-2">
                                <p>@lang('To recover your account please provide your email or username to find your account.')</p>
                            </div>
                            <form method="POST" action="{{ route('user.password.email') }}" class="verify-gcaptcha">
                                @csrf
                                <div class="form-group">
                                    <label class="form-label">@lang('Email or Username')</label>
                                    <input class="form-control form--control" name="value" type="text" value="{{ old('value') }}" required autofocus="off">
                                </div>
                                <x-captcha />
                                <button class="cmn-btn w-100" type="submit">@lang('Submit')</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
