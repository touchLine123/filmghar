@extends($activeTemplate . 'layouts.auth')
@section('app')
    @php
        $reset = getContent('reset_password.content', true);
    @endphp
    <section class="account-section bg-overlay-black bg_img" data-background="{{ frontendImage('reset_password', @$reset->data_values->background_image, '1780x760') }}">
        <div class="container">
            <div class="row account-area align-items-center justify-content-center">
                <div class="col-md-8 col-lg-7 col-xl-5">
                    <div class="card custom--card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <h5 class="title">@lang('Reset Password')</h5>
                                <a class="text-white" href="{{ route('home') }}">@lang('Go Home')</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <p>@lang('Your account is verified successfully. Now you can change your password. Please enter a strong password and don\'t share it with anyone.')</p>
                            </div>
                            <form method="POST" action="{{ route('user.password.update') }}">
                                @csrf
                                <input name="email" type="hidden" value="{{ $email }}">
                                <input name="token" type="hidden" value="{{ $token }}">
                                <div class="form-group">
                                    <label class="form-label">@lang('Password')</label>
                                    <input type="password" class="form-control form--control @if (gs('secure_password')) secure-password @endif" name="password" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('Confirm Password')</label>
                                    <input class="form-control form--control" name="password_confirmation" type="password" required>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn--base w-100" type="submit"> @lang('Submit')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
