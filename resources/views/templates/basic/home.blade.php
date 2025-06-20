@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $bannerContent = getContent('banner.content', true);
    @endphp

    @if ($advertise && !auth()->id())
        <div class="modal" id="adModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true"">
                                            <div class=" modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body position-relative p-0">
                    <div class="ads-close-btn position-absolute">
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal" type="button"
                            aria-label="Close"></button>
                    </div>
                    <a href="{{ $advertise->content->link }}" target="_blank">
                        <img src="{{ getImage(getFilePath('ads') . '/' . @$advertise->content->image) }}" alt="@lang('image')">
                    </a>
                </div>
            </div>
        </div>
        </div>
    @endif
    <style>
        .accordion-body {
            background: #131722 !important;
            color: #fff;
        }
        button.accordion-button {
            background: #131722 !important;
            color: #fff;
        }
    </style>
    <section class="banner-section bg-overlay-black bg_img"
        data-background="{{ getImage('assets/images/frontend/banner/' . @$bannerContent->data_values->background_image, '1778x755') }}">
        <div class="container">
            <div class="row align-items-center">
             
                <div class="col-xl-12 col-lg-12">
                    <div class="banner-slider">
                        <div class="swiper-wrapper">
                            @foreach ($sliders as $slider)
                                <div class="swiper-slide">
                                    <div class="movie-item">
                                        <div class="movie-thumb">
                                            <img style="width: 300px; height: 450px;" class="lazy-loading-img"
                                                data-src="{{ getImage(getFilePath('item_portrait').@$slider->item->image->portrait) }}"
                                                src="{{ getImage(getFilePath('item_portrait').@$slider->item->image->portrait) }}" alt="movie">
                                            <div class="movie-thumb-overlay">
                                                <a class="video-icon" href="{{ route('watch', @$slider->item->slug) }}"><i
                                                        class="fas fa-play"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="movie-section ptb-80 section" data-section="single20">
        <div class="container">
            <div class="row">
                <div class="col-xl-6">
                    <div class="section-header">
                        <h2 class="section-title">Film Ghar Collection</h2>
                    </div>
                </div>

                <div class="col-xl-6 d-flex justify-content-end align-items-center mb-30">
                    <div class="movie-slider-arrow">
                        <div class="slider-prev">
                            <i class="fas fa-angle-left"></i>
                        </div>
                        <div class="slider-next">
                            <i class="fas fa-angle-right"></i>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row justify-content-center align-items-center mb-30-none">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-30">
                    <div class="movie-slider">
                        <div class="swiper-wrapper">
                            @foreach ($movies as $featured)
                                <div class="swiper-slide">
                                    <div class="movie-item">
                                        <div class="movie-thumb">
                                            <img class="lazy-loading-img"
                                                data-src="{{ getImage(getFilePath('item_portrait') . $featured->image->portrait) }}"
                                                src="{{ getImage(getFilePath('item_portrait') . $featured->image->portrait) }}" alt="movie">
                                            <span class="movie-badge">{{ __($featured->versionName) }}</span>
                                            <div class="movie-thumb-overlay">
                                                <a class="video-icon" href="{{ route('watch', $featured->slug) }}"><i
                                                        class="fas fa-play"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <section class="movie-section ptb-80 section" data-section="single10">
        <div class="container">
            <div class="row">
                <div class="col-xl-6">
                    <div class="section-header">
                        <h2 class="section-title">Most Watched In Film Ghar</h2>
                    </div>
                </div>

                <div class="col-xl-6 d-flex justify-content-end align-items-center mb-30">
                    <div class="movie-slider-arrow">
                        <div class="slider-prev">
                            <i class="fas fa-angle-left"></i>
                        </div>
                        <div class="slider-next">
                            <i class="fas fa-angle-right"></i>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row justify-content-center align-items-center mb-30-none">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-30">
                    <div class="movie-slider">
                        <div class="swiper-wrapper">
                            @foreach ($movies as $featured)
                                <div class="swiper-slide">
                                    <div class="movie-item">
                                        <div class="movie-thumb">
                                            <img class="lazy-loading-img"
                                                data-src="{{ getImage(getFilePath('item_portrait') . $featured->image->portrait) }}"
                                                src="{{ getImage(getFilePath('item_portrait') . $featured->image->portrait) }}" alt="movie">
                                            <span class="movie-badge">{{ __($featured->versionName) }}</span>
                                            <div class="movie-thumb-overlay">
                                                <a class="video-icon" href="{{ route('watch', $featured->slug) }}"><i
                                                        class="fas fa-play"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="movie-section ptb-80 section" data-section="single1">
        <div class="container">
            <div class="row">
                <div class="col-xl-6">
                    <div class="section-header">
                        <h2 class="section-title">Trending Movie</h2>
                    </div>
                </div>

                <div class="col-xl-6 d-flex justify-content-end align-items-center mb-30">
                    <div class="movie-slider-arrow">
                        <div class="slider-prev">
                            <i class="fas fa-angle-left"></i>
                        </div>
                        <div class="slider-next">
                            <i class="fas fa-angle-right"></i>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row justify-content-center align-items-center mb-30-none">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-30">
                    <div class="movie-slider">
                        <div class="swiper-wrapper">
                            @foreach ($movies as $featured)
                                <div class="swiper-slide">
                                    <div class="movie-item">
                                        <div class="movie-thumb">
                                            <img class="lazy-loading-img"
                                                data-src="{{ getImage(getFilePath('item_portrait') . $featured->image->portrait) }}"
                                                src="{{ getImage(getFilePath('item_portrait') . $featured->image->portrait) }}" alt="movie">
                                            <span class="movie-badge">{{ __($featured->versionName) }}</span>
                                            <div class="movie-thumb-overlay">
                                                <a class="video-icon" href="{{ route('watch', $featured->slug) }}"><i
                                                        class="fas fa-play"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- <div class="sections"></div>
                <div class="custom_loading"></div> -->


    <section class="plan-section section--bg">
        <div class="container">
            <div class="row">
                <div class="col-xl-6">
                    <div class="section-header">
                        <h2 class="section-title">Why Choosing Film Ghar</h2>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="plan-item text-center p-4 h-100 d-flex flex-column align-items-center justify-content-center">
                        <img class="mb-3" width="100" src="{{ asset('assets/images/devices.png') }}" alt="Devices">
                        <h5 class="mb-2">Enjoy Movies on Devices</h5>
                        <p class="small mb-0">
                            Watch your favorite movies on Mobile, Tablet, TV with Chromecast*, Airplay support*.
                        </p>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="plan-item text-center p-4 h-100 d-flex flex-column align-items-center justify-content-center">
                        <img class="mb-3" width="100" src="{{ asset('assets/images/computer.png') }}" alt="Manage Device">
                        <h5 class="mb-2">Manage Device</h5>
                        <p class="small mb-0">
                            Add or remove devices anytime from the Film Ghar app. Upgrade plan if needed.
                        </p>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="plan-item text-center p-4 h-100 d-flex flex-column align-items-center justify-content-center">
                        <img class="mb-3" width="100" src="{{ asset('assets/images/credit-card.png') }}" alt="Easy Payment">
                        <h5 class="mb-2">Easy Payment</h5>
                        <p class="small mb-0">
                            Pay with VISA, Mastercard, Esewa, Khalti and Apple In-app purchase.
                        </p>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="plan-item text-center p-4 h-100 d-flex flex-column align-items-center justify-content-center">
                        <img class="mb-3" width="100" src="{{ asset('assets/images/international.png') }}" alt="Global Access">
                        <h5 class="mb-2">Watch from Anywhere</h5>
                        <p class="small mb-0">
                            Film Ghar is available globally, but charges may vary by country taxes.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <section class="plan-section section--bg ptb-50">
        <div class="container mt-4">
            <h3 class="text-white mb-4">Frequently Asked Questions</h3>

            <div class="accordion" id="accordionExample">

                <!-- Disclaimer -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingDisclaimer">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDisclaimer" aria-expanded="false" aria-controls="collapseDisclaimer">
                            Disclaimer
                        </button>
                    </h2>
                    <div id="collapseDisclaimer" class="accordion-collapse collapse" aria-labelledby="headingDisclaimer" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            Unauthorized reproduction, distribution and misuse of any content on MSM VIDEO Pty Ltd, MSM VIDEO LLC, MSM VIDEO Pvt Ltd and MSM VIDEO Inc is illegal worldwide. Such criminal copyright infringement is subject to investigation by Federal and State laws and may impose penalty of USD 1 Million plus any legal fees and imprisonment as per the law. Watch responsibly. Enjoy MSM VIDEO.
                        </div>
                    </div>
                </div>

                <!-- Start Subscription -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSubscription">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSubscription" aria-expanded="false" aria-controls="collapseSubscription">
                            Start Subscription
                        </button>
                    </h2>
                    <div id="collapseSubscription" class="accordion-collapse collapse" aria-labelledby="headingSubscription" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            To start your subscription, visit our website or app, choose your desired plan (monthly, 3-month, 6-month, or annual), and follow the steps to complete your payment.
                        </div>
                    </div>
                </div>

                <!-- Payment Declined -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingPaymentDeclined">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePaymentDeclined" aria-expanded="false" aria-controls="collapsePaymentDeclined">
                            Payment Declined
                        </button>
                    </h2>
                    <div id="collapsePaymentDeclined" class="accordion-collapse collapse" aria-labelledby="headingPaymentDeclined" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            Ensure that your payment details are correct and that your card has sufficient funds. If the problem persists, contact your bank or try a different payment method.
                        </div>
                    </div>
                </div>

                <!-- Refund Policy -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingRefundPolicy">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRefundPolicy" aria-expanded="false" aria-controls="collapseRefundPolicy">
                            Refund Policy
                        </button>
                    </h2>
                    <div id="collapseRefundPolicy" class="accordion-collapse collapse" aria-labelledby="headingRefundPolicy" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            Due to the nature of the streaming services, we don’t offer refunds. However, you can continue streaming until the end of your billing cycle.
                        </div>
                    </div>
                </div>

                <!-- Multiple Devices -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingMultipleDevices">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMultipleDevices" aria-expanded="false" aria-controls="collapseMultipleDevices">
                            Multiple Devices Simultaneously
                        </button>
                    </h2>
                    <div id="collapseMultipleDevices" class="accordion-collapse collapse" aria-labelledby="headingMultipleDevices" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            Depending on your subscription plan, you can stream on multiple devices at the same time. Check your plan details for more information.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <section class="plan-section section--bg ptb-80">
        <div class="container">
            <div class="row justify-content-center align-items-center mb-30-none">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="download-links">
                        <a class="download-links__item" href="https://play.google.com/store/games" target="_blank">
                            <img src="{{ asset('assets/images/frontend/basic_footer/63fa0e1917ea41677331993.png') }}"
                                alt="Google Play Store">
                        </a>
                        <a class="download-links__item" href="https://www.apple.com/app-store/" target="_blank">
                            <img src="{{ asset('assets/images/frontend/basic_footer/63fa0e23aeeaa1677332003.png') }}"
                                alt="App Store">
                        </a>
                    </div>
                </div>
                <div class="col-md-4"></div>

            </div>
        </div>
    
    </section>


@endsection