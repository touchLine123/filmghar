@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pt-80 pb-80">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-lg-7 col-xl-5 mx-auto">
                    <div class="d-flex justify-content-center">
                        <div class="verification-code-wrapper">
                            <div class="verification-area">
                                <h5 class="pb-3 text-center">@lang('Verify Email Address')</h5>
                                <form action="{{ route('user.password.verify.code') }}" method="POST" class="submit-form">
                                    @csrf
                                    <p class="my-3">@lang('A 6 digit verification code sent to your email address') : {{ showEmailAddress($email) }}</p>
                                    <input type="hidden" name="email" value="{{ $email }}">

                                    @include($activeTemplate . 'partials.verification_code')

                                    <div class="form-group">
                                        <button type="submit" class="cmn-btn w-100">@lang('Submit')</button>
                                    </div>

                                    <div class="form-group">
                                        @lang('Please check including your Junk/Spam Folder. if not found, you can')
                                        <a href="{{ route('user.password.request') }}" class="base--color">@lang('Try to send again')</a>
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

@push('style')
    <style>
        .verification-code::after {
            background-color: #0d0d31 !important;
        }
    </style>
@endpush
