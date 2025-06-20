@php
    $breadcrumb = getContent('breadcrumb.content',true);
@endphp

<section class="inner-banner-section banner-section bg-overlay-black bg_img" data-background="{{ getImage('assets/images/frontend/breadcrumb/'.@$breadcrumb->data_values->background_image, '1778x755') }}">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-xl-12 text-center">
                <div class="banner-content">
                    <h1 class="title text-white">@lang($pageTitle)</h1>
                    <div class="breadcrumb-area">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('Home')</a></li>
                                <li class="breadcrumb-item active" aria-current="page">@lang($pageTitle)</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
