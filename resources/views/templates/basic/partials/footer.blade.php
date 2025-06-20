@php
    $footerContent = getContent('basic_footer.content', true);
    $footerElement = getContent('basic_footer.element', orderById: true);
    $socialIcons = getContent('social_icon.element', orderById: true);
    $policyPages = getContent('policy_pages.element');
    $socialLinks = getContent('short_links.element', orderById: true);
@endphp
<footer class="footer-section footer bg-overlay-black bg_img pt-80" data-background="{{ getImage('assets/images/frontend/basic_footer/' . @$footerContent->data_values->background_image, '1920x789') }}">
    <div class="container">
       
        <div class="footer-bottom-area">
            <div class="row justify-content-center mb-30-none">
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 mb-30">
                    <div class="footer-widget">
                        <h3 class="widget-title">@lang('About Us')</h3>
                        <p>{{ __(@$footerContent->data_values->about_us) }}</p>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 mb-30">
                    <div class="footer-widget">
                        <h3 class="widget-title">@lang('Categories')</h3>
                        <ul class="footer-links">
                            @foreach ($categories as $category)
                                <li><a href="{{ route('category', $category->id) }}">{{ __($category->name) }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-4 mb-30">
                    <div class="footer-widget">
                        <div class="social-area">
                            <ul class="footer-social">
                                @foreach ($socialIcons as $item)
                                    <li><a href="{{ @$item->data_values->url }}" target="_blank">@php echo @$item->data_values->social_icon @endphp</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-6 col-sm-8 mb-30">
                    <div class="footer-widget">
                        <div class="footer-logo">
                            <a class="site-logo" href="{{ route('home') }}"><img src="{{ siteLogo() }}" alt="logo"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright-area">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-12 text-center">
                    <div class="copyright-wrapper d-flex align-items-center justify-content-between flex-wrap">
                        <div class="copyright">
                            <p>@lang('Copyright') &copy; {{ date('Y') }} @lang('All Rights Reserved By')
                                <a class="text--base" href="{{ route('home') }}">{{ gs('site_name') }}</a>
                            </p>
                        </div>
                        <div class="copyright-link-area">
                            <ul class="copyright-link">
                                @foreach ($policyPages as $policy)
                                    <li><a href="{{ route('policy.pages', $policy->slug) }}">{{ __(@$policy->data_values->title) }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>


