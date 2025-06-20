<section class="bg_img dark--overlay section pb-80" data-section="recent_added" style="background-image: url({{ getImage('assets/images/item/landscape/' . @$single[0]->image->landscape) }});">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-xl-5 col-lg-6">
                <div class="single-movie-thumb">
                    <img class="lazy-loading-img" data-src="{{ getImage(getFilePath('item_landscape') . '/' . @$single[0]->image->landscape) }}" src="{{ asset('assets/global/images/lazy_one.png') }}" alt="image">
                    @if (@$single[0])
                        <a class="video-btn" href="{{ route('watch', @$single[0]->slug) }}">
                            <div class="icon">
                                <i class="fas fa-play"></i>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-xl-5 col-lg-6 pl-lg-4 mt-lg-0 mt-4">
                <div class="single-movie-content">
                    <h2>
                        <a href="{{ getImage(getFilePath('item_landscape') . '/' . @$single[0]->image->landscape) }}">{{ __(@$single[0]->title) }}</a>
                    </h2>
                    <ul class="movie-card__meta justify-content-start mt-2 mb-4">
                        <li><i class="far fa-eye color--primary"></i> <span>{{ __(numFormat(@$single[0]->view)) }}</span></li>
                        <li><i class="fas fa-star color--glod"></i> <span>({{ __(@$single[0]->ratings) }})</span></li>
                    </ul>
                    <p>{{ __(@$single[0]->preview_text) }}</p>
                    @if (@$single[0])
                        @if (@$single[0]->is_trailer == Status::TRAILER && @$single[0]->item_type == Status::SINGLE_ITEM)
                            <a class="cmn-btn mt-4" href="{{ route('watch', @$single[0]->slug) }}">@lang('Watch Trailer')</a>
                        @else
                            <a class="cmn-btn mt-4" href="{{ route('watch', @$single[0]->slug) }}">@lang('Watch Now')</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<div class="ad-section pt-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @php echo showAd(); @endphp
            </div>
        </div>
    </div>
</div>
