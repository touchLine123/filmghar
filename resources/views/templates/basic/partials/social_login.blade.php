@php
    $text = Route::is('user.register') ? 'Register' : 'Login';
@endphp
<div class="social-auth-list">
    @if (@gs('socialite_credentials')->google->status == Status::ENABLE)
        <div class="continue-google">
            <a href="{{ route('user.social.login', 'google') }}" class="w-100 social-login-btn">
                <span class="google-icon icon">
                    <img src="{{ asset($activeTemplateTrue . 'images/google.svg') }}" alt="Google">
                </span> @lang("$text with Google")
            </a>
        </div>
    @endif
    @if (@gs('socialite_credentials')->facebook->status == Status::ENABLE)
        <div class="continue-facebook">
            <a href="{{ route('user.social.login', 'facebook') }}" class="w-100 social-login-btn">
                <span class="facebook-icon icon">
                    <img src="{{ asset($activeTemplateTrue . 'images/facebook.svg') }}" alt="Facebook">
                </span> @lang("$text with Facebook")
            </a>
        </div>
    @endif
    @if (@gs('socialite_credentials')->linkedin->status == Status::ENABLE)
        <div class="continue-facebook">
            <a href="{{ route('user.social.login', 'linkedin') }}" class="w-100 social-login-btn">
                <span class="linked-icon icon">
                    <img src="{{ asset($activeTemplateTrue . 'images/linkdin.svg') }}" alt="Linkedin">
                </span> @lang("$text with Linkedin")
            </a>
        </div>
    @endif
</div>

@if (@gs('socialite_credentials')->linkedin->status || @gs('socialite_credentials')->facebook->status == Status::ENABLE || @gs('socialite_credentials')->google->status == Status::ENABLE)
    <div class="auth-devide">
        <span>@lang('OR')</span>
    </div>
@endif
@push('style')
    <style>
        .social-login-btn {
            border: 1px solid rgb(140 141 161 / 20%);
            color: rgb(255 255 255 / 70%);
            padding: 8px 12px;
            border-radius: 4px;
            transition: all linear 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 15px;
        }

        .social-login-btn:hover {
            background-color: rgb(255 255 255 / 5%);
        }

        .social-auth-list {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            width: 100%;
            flex-wrap: wrap;
        }

        .social-auth-list>div {
            flex-shrink: 0;
            width: 100%;
        }

        .social-auth-list>div .icon {
            display: flex;
        }

        .auth-devide {
            text-align: center;
            position: relative;
            display: block;
            width: 100%;
            margin-block: 24px;
            z-index: 1;
        }

        .auth-devide::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            height: 1px;
            width: 100%;
            background-color: rgb(255 255 255 / 10%);
            z-index: -1;
        }

        .auth-devide span {
            padding-inline: 5px;
            background: #131722;
        }
    </style>
@endpush
