<!-- footer section start -->
@php
    $policies = getContent('policy_pages.element');
    $footer = getContent('footer.content', true);
    $footerElement = getContent('footer.element', false, null, true);
    $links = getContent('short_links.element', false, null, true);
    $subscriber = getContent('subscribe.content', true);
    $socials = getContent('social_icon.element');
@endphp
<footer class="footer @if (request()->routeIs('home') || request()->routeIs('category') || request()->routeIs('subCategory') || request()->routeIs('search')) d-none @endif">
    <div class="footer__top">
        <div class="container">
            <div class="row mb-none-30">
                <div class="col-lg-4 col-sm-8 mb-50">
                    <div class="footer-widget">
                        <a href="{{ route('home') }}"><img class="mb-4" src="{{ siteLogo() }}" alt="image"></a>
                        <p>{{ __(@$footer->data_values->about_us) }}</p>
                        <ul class="social-links mt-3">
                            @foreach ($socials as $social)
                                <li><a href="{{ @$social->data_values->url }}" target="_blank">@php echo @$social->data_values->social_icon @endphp</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-4 mb-50">
                    <div class="footer-widget">
                        <h4 class="footer-widget__title">@lang('Short Links')</h4>
                        <ul class="link-list">
                            @foreach ($links as $link)
                                <li>
                                    <a href="{{ route('links', $link->slug) }}">{{ __($link->data_values->title) }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-sm-4 mb-50">
                    <div class="footer-widget">
                        <h4 class="footer-widget__title">@lang('Category')</h4>
                        <ul class="link-list">
                            @foreach ($categories as $category)
                                <li><a href="{{ route('category', $category->id) }}">{{ __($category->name) }}</a></li>
                            @endforeach
                        </ul>
                    </div><!-- footer-widget end -->
                </div>
                <div class="col-lg-4 col-sm-8 mb-50">
                    <div class="footer-widget">
                        <h4 class="footer-widget__title">{{ __(@$footer->data_values->subscribe_title) }}</h4>
                        <p>{{ __(@$footer->data_values->subscribe_subtitle) }}</p>
                        <form class="subscribe-form mt-3">
                            @csrf
                            <input name="email" type="email" placeholder="@lang('Email Address')">
                            <button type="submit"><i class="fas fa-paper-plane"></i></button>
                        </form>
                        <div class="download-links">
                            @foreach ($footerElement as $footer)
                                <a class="download-links__item" href="{{ @$footer->data_values->link }}" target="_blank">
                                    <img src="{{ frontendImage('footer', @$footer->data_values->store_image, '150x45') }}" alt="@lang('image')">
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer__bottom">
        <div class="container">
            <div class="row gy-4">
                <div class="col-md-6">
                    <div class="d-flex justify-content-center justify-content-lg-between">
                        <p>@lang('Copyright') &copy; @php echo date('Y') @endphp @lang('All Rights Reserved By ')
                            <a href="{{ route('home') }}" class="base--color">{{ __(gs('site_name')) }}</a>
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <ul class="links justify-content-md-end justify-content-around">
                        @foreach ($policies as $policy)
                            <li><a href="{{ route('policy.pages', $policy->slug) }}">{{ __(@$policy->data_values->title) }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="modal alert-modal" id="alertModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <span class="alert-icon"><i class="fas fa-question-circle"></i></span>
                <p class="modal-description">@lang('Subscription Alert!')</p>
                <p class="modal--text">@lang('Please subscribe a plan to view our paid items')</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn--dark btn--sm" data-bs-dismiss="modal" type="button">@lang('Cancel')</button>
                <a class="btn btn--base btn--sm" href="{{ route('subscription') }}">@lang('Subscribe Now')</a>
            </div>
        </div>
    </div>
</div>
