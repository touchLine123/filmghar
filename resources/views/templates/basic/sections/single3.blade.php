<section class="trailer-section bg-overlay-black bg_img section pb-80" data-section="free_zone" style="background: url('{{ getImage(getFilePath('item_landscape') . '/' . @$single[2]->image->landscape) }}')">
    <div class="trailer-overlay"></div>
    <div class="container">
        <div class="row justify-content-center align-items-center mb-30-none">
            <div class="col-xl-6 col-lg-6 mb-30">
                <div class="trailer-content">
                    <h1 class="title text-white">{{ __(@$single[2]->title) }}</h1>
                    <p>{{ __(@$single[2]->preview_text) }}</p>
                    <div class="trailer-btn">
                        @if (@$single[2]->is_trailer == Status::TRAILER && @$single[2]->item_type == Status::SINGLE_ITEM)
                            <a class="btn--base" href="{{ route('watch', @$single[2]->slug ?? 0) }}">@lang('Watch Trailer')</a>
                        @else
                            <a class="btn--base" href="{{ route('watch', @$single[2]->slug ?? 0) }}">@lang('Watch Now')</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 mb-30">
                <div class="trailer-video-wrapper">
                    <div class="trailer-thumb">
                        <img class="lazy-loading-img" data-src="{{ getImage(getFilePath('item_landscape') . '/' . @$single[2]->image->landscape) }}" src="{{ asset('assets/global/images/lazy_one.png') }}" alt="trailer">
                        <div class="trailer-thumb-overlay">
                            <a class="video-icon" data-rel="lightcase:myCollection" href="{{ route('watch', @$single[2]->slug ?? 0) }}">
                                <i class="fas fa-play"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
