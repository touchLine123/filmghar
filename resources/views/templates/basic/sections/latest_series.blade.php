<section class="movie-section section--bg section pb-80" data-section="single2">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="section-header">
                    <h2 class="section-title">@lang('Latest Series')</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mb-30-none">

            @foreach ($latestSerieses as $latestSeries)
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                    <div class="movie-item">
                        <div class="movie-thumb">
                            <img class="lazy-loading-img" data-src="{{ getImage(getFilePath('item_portrait') . '/' . $latestSeries->image->portrait) }}" src="{{ asset('assets/global/images/lazy.png') }}" alt="movie">
                            <span class="movie-badge">{{ $latestSeries->versionName }}</span>
                            <div class="movie-thumb-overlay">
                                <a class="video-icon" href="{{ route('watch', $latestSeries->slug) }}"><i class="fas fa-play"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<div class="add-area ad-section section--bg ptb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12 text-center">
                <div class="add-thumb">
                    @php echo showAd(); @endphp
                </div>
            </div>
        </div>
    </div>
</div>
