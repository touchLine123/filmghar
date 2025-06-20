<section class="section pb-80" data-section="latest_series">
    <div class="container-fluid">
        <div class="row gy-3">
            <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6">
                <div class="section-header">
                    <h2 class="section-title">@lang('Trending Videos')</h2>
                </div>
                <ul class="movie-small-list">
                    @foreach ($trendings as $trending)
                        <li class="movie-small">
                            <div class="movie-small__thumb">
                                <img class="lazy-loading-img" data-src="{{ getImage(getFilePath('item_portrait') . '/' . $trending->image->portrait) }}" src="{{ asset('assets/global/images/lazy.png') }}" alt="image">
                            </div>
                            <div class="movie-small__content">
                                <h5>{{ __($trending->title) }}</h5>
                                <ul class="movie-card__meta">
                                    <li><i class="far fa-eye color--primary"></i> <span>{{ __(numFormat($trending->view)) }}</span></li>
                                    <li><i class="fas fa-star color--glod"></i> <span>({{ __($trending->ratings) }})</span></li>
                                </ul>
                                <a class="text-small base--color" href="{{ route('watch', $trending->slug) }}">
                                    @if ($trending->is_trailer == Status::TRAILER && $trending->item_type == Status::SINGLE_ITEM)
                                        @lang('Watch Trailer')
                                    @else
                                        @lang('Watch Now')
                                    @endif
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6">
                <div class="section-header">
                    <h2 class="section-title">@lang('Top Rated')</h2>
                </div>
                <ul class="movie-small-list">
                    @foreach ($topRateds as $topRated)
                        <li class="movie-small">
                            <div class="movie-small__thumb">
                                <img class="lazy-loading-img" data-src="{{ getImage(getFilePath('item_portrait') . '/' . $topRated->image->portrait) }}" src="{{ asset('assets/global/images/lazy.png') }}" alt="image">
                            </div>
                            <div class="movie-small__content">
                                <h5>{{ __($topRated->title) }}</h5>
                                <ul class="movie-card__meta">
                                    <li><i class="far fa-eye color--primary"></i> <span>{{ __(numFormat($topRated->view)) }}</span></li>
                                    <li><i class="fas fa-star color--glod"></i> <span>({{ __($topRated->ratings) }})</span></li>
                                </ul>
                                <a class="text-small base--color" href="{{ route('watch', $topRated->slug) }}">
                                    @if (@$topRated->is_trailer == Status::TRAILER && @$topRated->item_type == Status::SINGLE_ITEM)
                                        @lang('Watch Trailer')
                                    @else
                                        @lang('Watch Now')
                                    @endif
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-xxl-6 col-xl-4">
                <div class="single-movie">
                    <div class="single-movie__thumb">
                        <img class="w-100 lazy-loading-img" data-src="{{ getImage(getFilePath('item_landscape') . '/' . @$mostViewsTrailer->image->landscape) }}" alt="image" ssrc="{{ asset('assets/global/images/lazy_one.png') }}">
                    </div>
                    @if (@$mostViewsTrailer)
                        <a class="video-btn" href="{{ route('watch', @$mostViewsTrailer->slug) }}">
                            <div class="icon">
                                <i class="fas fa-play"></i>
                            </div>
                            <span>@lang('Watch Trailer')</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<div class="ad-section pb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @php echo showAd(); @endphp
            </div>
        </div>
    </div>
</div>
