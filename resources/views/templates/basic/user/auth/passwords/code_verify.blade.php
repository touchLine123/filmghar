@extends($activeTemplate . 'layouts.auth')
@section('app')
    @php
        $codeVerify = getContent('code_verify.content', true);
    @endphp
    <section class="account-section bg-overlay-black bg_img"
             data-background="{{ frontendImage('code_verify', @$codeVerify->data_values->background_image, '1780x760') }}">
        <div class="container">
            <div class="row account-area align-items-center justify-content-center">
                <div class="col-md-8 col-lg-7 col-xl-5">
                    <div class="d-flex justify-content-center">
                        <div class="verification-code-wrapper">
                            <div class="verification-area">
                                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                                    <h5 class="mb-0">@lang('Verify Email Address')</h5>
                                    <a href="{{ route('home') }}" class="text--base">@lang('Go Home')</a>
                                </div>
                                <form action="{{ route('user.password.verify.code') }}" method="POST" class="submit-form">
                                    @csrf
                                    <p>@lang('A 6 digit verification code sent to your email address') : {{ showEmailAddress($email) }}</p>
                                    <input type="hidden" name="email" value="{{ $email }}">

                                    @include($activeTemplate . 'partials.verification_code')

                                    <div class="form-group">
                                        <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                                    </div>

                                    <div class="form-group">
                                        @lang('Please check including your Junk/Spam Folder. if not found, you can')
                                        <a href="{{ route('user.password.request') }}" class="text--base">@lang('Try to send again')</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
