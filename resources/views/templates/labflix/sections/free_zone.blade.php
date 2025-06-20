<section class="section pb-80" data-section="end">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-header">
                    <h2 class="section-title">@lang('Free Zone')</h2>
                </div>
            </div>
        </div>
        <div class="row mb-none-30">
            @foreach ($frees as $free)
                <div class="col-xxl-2 col-md-3 col-4 col-xs-6 mb-30">
                    <div class="movie-card" data-text="{{ $free->versionName }}">
                        <div class="movie-card__thumb thumb__2">
                            <img class="lazy-loading-img" class="lazy-loading-img" data-src="{{ getImage(getFilePath('item_portrait') . '/' . @$free->image->portrait) }}" src="{{ asset('assets/global/images/lazy.png') }}" alt="image">
                            <a class="icon" href="{{ route('watch', $free->slug) }}"><i class="fas fa-play"></i></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
