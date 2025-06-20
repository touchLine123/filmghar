<section class="movie-section section--bg section pt-80 pb-80" data-section="end">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="section-header">
                    <h2 class="section-title">@lang('Free Zone')</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mb-30-none">
            @foreach ($frees as $free)
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                    <div class="movie-item">
                        <div class="movie-thumb">
                            <img class="lazy-loading-img" data-src="{{ getImage(getFilePath('item_portrait') . '/' . $free->image->portrait) }}" src="{{ asset('assets/global/images/lazy.png') }}" alt="movie">
                            <div class="movie-thumb-overlay">
                                <a class="video-icon" href="{{ route('watch', $free->slug) }}"><i class="fas fa-play"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
