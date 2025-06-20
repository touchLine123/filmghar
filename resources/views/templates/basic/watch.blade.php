@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="movie-details-section section--bg ptb-80">
        <div class="container">
            <div class="row @if (blank($episodes)) justify-content-center @endif mb-30-none">
                <div class="col-xl-8 col-lg-8 mb-30">
                    <div class="movie-item">
                        <div class="main-video">
                            <video class="video-player plyr-video" playsinline controls data-poster="{{ getImage(getFilePath('item_landscape') . '/' . $item->image->landscape) }}">
                                @foreach ($videos as $video)
                                    <source src="{{ $video->content }}" type="video/mp4" size="{{ $video->size }}" />
                                @endforeach
                                @foreach ($subtitles ?? [] as $subtitle)
                                    <track kind="captions" label="{{ $subtitle->language }}" src="{{ getImage(getFilePath('subtitle') . '/' . $subtitle->file) }}" srclang="{{ $subtitle->code }}" />
                                @endforeach
                            </video>
                            @if ($item->version == Status::RENT_VERSION && !$watchEligable)
                                <div class="main-video-lock">
                                    <div class="main-video-lock-content">
                                        <span class="icon"><i class="las la-lock"></i></span>
                                        <p class="title">@lang('Purchase Now')</p>
                                        <p class="price">
                                            <span class="price-amount">{{ showAmount($item->rent_price) }}</span>
                                            <span class="small-text ms-3">@lang('For') {{ $item->rental_period }} @lang('Days')</span>
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="ad-video position-relative d-none">
                            <video class="ad-player" style="display: none" id="ad-video"></video>
                            <div class="ad-links d-none">
                                @foreach ($adsTime ?? [] as $ads)
                                    <source src="{{ $ads }}" type="video/mp4" />
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-between align-items-center flex-wrap  skip-video">
                                <span class="advertise-text d-none">@lang('Advertisement') - <span class="remains-ads-time">00:00</span></span>
                                <button class="skipButton d-none" id="skip-button" data-skip-time="0">@lang('Skip Ad')</button>
                            </div>
                        </div>

                        <div class="movie-content">
                            <div class="movie-content-inner d-sm-flex justify-content-between align-items-center flex-wrap">
                                <div class="movie-content-left">
                                    <h3 class="title">{{ __($item->title) }}</h3>
                                    <span class="sub-title">@lang('Category') : <span class="cat">{{ @$item->category->name }}</span>
                                        @if ($item->sub_category)
                                            @lang('Sub Category'): {{ @$item->sub_category->name }}
                                        @endif
                                    </span>
                                </div>
                                <div class="movie-content-right">
                                    <div class="movie-widget-area align-items-center">

                                        @auth
                                            @if ($watchEligable && gs('watch_party'))
                                                <button type="button" class="watch-party-btn watchPartyBtn">
                                                    <i class="las la-desktop base--color"></i>
                                                    <span>@lang('Watch party')</span>
                                                </button>
                                            @endif
                                        @endauth

                                        <span class="movie-widget"><i class="lar la-star text--warning"></i> {{ getAmount($item->ratings) }}</span>
                                        <span class="movie-widget"><i class="lar la-eye text--danger"></i> {{ getAmount($item->view) }} @lang('views')</span>

                                        @php
                                            $wishlist = $item->wishlists->where('user_id', auth()->id())->count();
                                        @endphp

                                        <span class="movie-widget addWishlist {{ $wishlist ? 'd-none' : '' }}" data-id="{{ $item->id }}" data-type="item"><i class="las la-plus-circle"></i></span>
                                        <span class="movie-widget removeWishlist {{ $wishlist ? '' : 'd-none' }}" data-id="{{ $item->id }}" data-type="item"><i class="las la-minus-circle"></i></span>
                                    </div>

                                    <ul class="post-share d-flex align-items-center justify-content-sm-end mt-2 flex-wrap">
                                        <li class="caption">@lang('Share') : </li>

                                        <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Facebook')">
                                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"><i class="lab la-facebook-f"></i></a>
                                        </li>
                                        <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Linkedin')">
                                            <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title={{ __(@$item->title) }}&amp;summary=@php echo strLimit(strip_tags($item->description), 130); @endphp"><i class="lab la-linkedin-in"></i></a>
                                        </li>
                                        <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Twitter')">
                                            <a href="https://twitter.com/intent/tweet?text={{ __(@$item->title) }}%0A{{ url()->current() }}"><i class="lab la-twitter"></i></a>
                                        </li>
                                        <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Pinterest')">
                                            <a href="http://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&description={{ __(@$item->title) }}&media={{ getImage(getFilePath('item_landscape') . '/' . @$item->image->landscape) }}"><i class="lab la-pinterest"></i></a>
                                        </li>
                                    </ul>

                                </div>
                            </div>
                            <div class="movie-widget-area">
                            </div>
                            <p class="movie-widget__desc">{{ __($item->preview_text) }}</p>
                        </div>
                    </div>

                    <div class="product-tab mt-40">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="product-tab-desc" data-bs-toggle="tab" href="#product-desc-content" role="tab" aria-controls="product-desc-content" aria-selected="true">@lang('Description')</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="product-tab-team" data-bs-toggle="tab" href="#product-team-content" role="tab" aria-controls="product-team-content" aria-selected="false">@lang('Team')</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="product-desc-content" role="tabpanel" aria-labelledby="product-tab-desc">
                                <div class="product-desc-content">
                                    {{ __($item->description) }}
                                </div>
                            </div>
                            <div class="tab-pane fade fade" id="product-team-content" role="tabpanel" aria-labelledby="product-tab-team">
                                <div class="product-desc-content">
                                    <ul class="team-list">
                                        <li><span>@lang('Director'):</span> {{ __($item->team->director) }}</li>
                                        <li><span>@lang('Producer'):</span> {{ __($item->team->producer) }}</li>
                                        <li><span>@lang('Cast'):</span> {{ __($item->team->casts) }}</li>
                                        <li><span>@lang('Genres'):</span> {{ __(@$item->team->genres) }}</li>
                                        <li><span>@lang('Language'):</span> {{ __(@$item->team->language) }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                @if (!blank($episodes))
                    <div class="col-xl-4 col-lg-4 mb-30">
                        <div class="widget-box">
                            <div class="widget-wrapper movie-small-list pt-0">
                                @foreach ($episodes as $episode)
                                    @php
                                        $status = checkLockStatus($episode, $userHasSubscribed, $hasSubscribedItem);
                                    @endphp
                                    <div class="widget-item widget-item__overlay d-flex align-items-center justify-content-between" data-img="{{ getImage(getFilePath('episode') . '/' . $episode->image) }}">
                                        <div class="widget-item__content d-flex align-items-center movie-small flex-wrap">
                                            <div class="widget-thumb">
                                                <a href="{{ route('watch', [$item->slug, $episode->id]) }}">
                                                    <img src="{{ getImage(getFilePath('episode') . '/' . $episode->image) }}" alt="movie">
                                                </a>
                                            </div>
                                            <div class="widget-content">
                                                <h4 class="title">{{ __($episode->title) }}</h4>
                                                <div class="widget-btn">
                                                    @if ($status)
                                                        <a class="custom-btn" href="{{ route('watch', [$item->slug, $episode->id]) }}">@lang('Play Now')</a>
                                                    @else
                                                        <a class="custom-btn" href="{{ route('subscription') }}">@lang('Subscribe to watch')</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="widget-item__lock">
                                            <span class="widget-item__lock-icon">
                                                @if ($status)
                                                    <i class="fas fa-unlock"></i>
                                                @else
                                                    <i class="fas fa-lock"></i>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
    <section class="movie-section ptb-80">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="section-header">
                        <h2 class="section-title">@lang('Related Items')</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mb-30-none">
                @foreach ($relatedItems as $related)
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                        <div class="movie-item">
                            <div class="movie-thumb">
                                <img src="{{ getImage(getFilePath('item_portrait') . '/' . $related->image->portrait) }}" alt="movie">
                                <span class="movie-badge">{{ __($related->versionName) }}</span>
                                <div class="movie-thumb-overlay">
                                    <a class="video-icon" href="{{ route('watch', $related->slug) }}"><i class="fas fa-play"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    <div class="watch-party-modal modal fade" id="watchPartyModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="las la-times"></i></button>
                <h3 class="title">@lang('Watch Party')</h3>
                <h6 class="tagline">@lang('Watch movies together with your friends and families.')</h6>
                <button class="btn btn--base startPartyBtn">@lang('Now Start Your Party') <i class="las la-long-arrow-alt-right"></i></button>
            </div>
        </div>
    </div>


    <div class="modal alert-modal" id="rentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('user.subscribe.video', $item->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <span class="alert-icon"><i class="fas fa-question-circle"></i></span>
                        <p class="modal-description">@lang('Confirmation Alert!')</p>
                        <p class="modal--text">@lang('Please purchase to this rent item for') {{ $item->rental_period }} @lang('days')</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--dark btn--sm" data-bs-dismiss="modal" type="button">@lang('Cancel')</button>
                        <button class="btn btn--base btn--sm" type="submit">@lang('Purchase Now')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/plyr.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/plyr.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/hls.min.js') }}"></script>
@endpush

@push('style')
    <style>
        .main-video:has(.main-video-lock) {
            position: relative;
        }

        .main-video-lock {
            position: absolute;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
            background-color: rgba(0, 0, 0, 0.555);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-video-lock-content {
            padding: 20px;
            background: rgb(0 0 0 / 70%);
            border-radius: 4px;
            width: 100%;
            height: 100%;
            cursor: pointer;
            display: grid;
            place-content: center;
        }

        .main-video-lock-content .title {
            text-align: center;
            color: #fff;
            font-size: 14px;
        }

        .main-video-lock-content .icon {
            font-size: 56px;
            display: block;
            text-align: center;
            line-height: 1;
            color: hsl(var(--base));
        }

        .main-video-lock-content .price {
            font-size: 36px;
            display: block;
            text-align: center;
            color: white;
            background: rgb(238 0 5 / 5%);
            margin-top: 10px;
            border-radius: inherit;
            line-height: 1;
            padding: 7px 0;
        }

        .main-video-lock-content .price .price-amount {
            color: hsl(var(--base));
            font-weight: 700;
            letter-spacing: -2;
        }

        .main-video-lock-content .price .small-text {
            font-size: 14px;
        }

        .main-video-lock-content .price span {
            line-height: 1;
        }

        .watch-party-modal .modal-dialog {
            max-width: 500px;
        }
    </style>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $(document).find('.plyr__controls').addClass('d-none');
            $(document).find('.ad-video').find('.plyr__controls').addClass('d-none');
        });

        (function($) {
            "use strict";

            let rent = "{{ Status::RENT_VERSION }}";

            $('.main-video-lock').on('click', function(e) {
                var modal = $('#rentModal');
                modal.modal('show');
            });

            const controls = [
                'play-large',
                'rewind',
                'play',
                'fast-forward',
                'progress',
                'mute',
                'settings',
                'pip',
                'airplay',
                'fullscreen'
            ];

            let player = new Plyr('.video-player', {
                controls,
                ratio: '16:9'
            });


            var data = [
                @foreach ($videos as $video)
                    {
                        src: "{{ $video->content }}",
                        type: 'video/mp4',
                        size: "{{ $video->size }}",
                    },
                @endforeach
            ];


            player.on('qualitychange', event => {
                $.each(data, function() {
                    initData();
                })
            });

            player.on('play', () => {
                let watchEligable = "{{ @$watchEligable }}";
                if (!Number(watchEligable)) {
                    var modal = $('#alertModal');
                    modal.modal('show');
                    player.pause();
                    return false;
                }
                $(document).find('.plyr__controls').removeClass('d-none');
            });

            const skipButton = $('#skip-button');

            const adItems = [
                @foreach ($adsTime as $key => $ads)
                    {
                        timing: "{{ $key }}",
                        source: "{{ $ads }}"
                    },
                @endforeach
            ];

            const adPlayer = new Plyr('.ad-player', {
                clickToPlay: false,
                ratio: '16:9'
            });

            let firstAd = false;
            const result = adItems.filter((obj) => {
                if (obj.timing == 0) {
                    firstAd = true;
                    return obj;
                }
            });

            if (firstAd) {
                adPlayer.source = {
                    type: 'video',
                    sources: [{
                        src: $('.ad-links').children('source:first').attr('src'),
                        type: 'video/mp4'
                    }],
                };
                player.pause();
                $('.main-video').addClass('d-none');
                $('.ad-video').removeClass('d-none');
                $(document).find('.ad-video').find('.plyr__controls').hide();
                adPlayer.play();
            }

            let skipTime = Number("{{ gs('skip_time') }}");

            player.on('timeupdate', function() {
                const currentTime = Math.floor(player.currentTime);
                for (let i = 0; i < adItems.length; i++) {
                    const adItem = adItems[i];

                    if (currentTime >= adItem.timing && !adItem.played) {
                        skipButton.addClass('d-none');
                        adPlayer.source = {
                            type: 'video',
                            sources: [{
                                src: $('.ad-links').children('source').eq(i).attr('src'),
                                type: 'video/mp4'
                            }],
                            poster: "{{ getImage(getFilePath('item_landscape') . '/' . $item->image->landscape) }}",
                        };
                        player.pause();
                        $('.main-video').addClass('d-none');
                        $('.ad-video').removeClass('d-none');
                        $(document).find('.ad-video').find('.plyr__controls').hide();
                        adPlayer.play();
                        adPlayer.on('play', () => {
                            $('.advertise-text').removeClass('d-none');
                        })
                        adPlayer.on('timeupdate', () => {
                            const currentTime = Math.floor(adPlayer.currentTime);
                            const duration = Math.floor(adPlayer.duration);
                            if (!isNaN(currentTime) && !isNaN(duration)) {
                                const remainingTime = duration - currentTime;
                                const formattedTime = formatTime(remainingTime);
                                $('.remains-ads-time').text(formattedTime);
                            }
                            if (adPlayer.currentTime >= skipTime) {
                                skipButton.removeClass('d-none');
                            }
                        });
                        adItem.played = true;
                        break;
                    }
                }
            });

            function formatTime(timeInSeconds) {
                const date = new Date(null);
                date.setSeconds(timeInSeconds);
                return date.toISOString().substr(11, 8);
            }

            adPlayer.on('ended', () => {
                player.play();
                $('.ad-video').addClass('d-none');
                $('.main-video').removeClass('d-none');
                $('.advertise-text').addClass('d-none');
            });

            skipButton.on('click', function() {
                adPlayer.pause();
                $('.ad-video').addClass('d-none');
                $('.main-video').removeClass('d-none');
                player.play();
                skipButton.addClass('d-none');
                $('.advertise-text').addClass('d-none');
            });


            $('.watchPartyBtn').on('click', function(e) {
                let modal = $("#watchPartyModal");
                modal.modal('show')
            });

            $('.copy-code').on('click', function() {
                var copyText = $('.party-code');
                copyText = copyText[0];
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand("copy");
                copyText.blur();
            });

            $('.startPartyBtn').on('click', function(e) {
                let processBtn = `<span class="processing">@lang('Processing') <i class="las la-spinner"></i> </span>`;
                let startBtn = `@lang('Now Start Your Party') <i class="las la-long-arrow-alt-right"></i>`;
                $.ajax({
                    type: "POST",
                    url: `{{ route('user.watch.party.create') }}`,
                    data: {
                        _token: "{{ csrf_token() }}",
                        item_id: "{{ @$item->id }}",
                        episode_id: "{{ @$episodeId }}"
                    },
                    beforeSend: function() {
                        $('.startPartyBtn').html('');
                        $('.startPartyBtn').html(processBtn);
                        $('.startPartyBtn').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.error) {
                            notify('error', response.error)
                            $('.startPartyBtn').html('');
                            $('.startPartyBtn').html(startBtn);
                            $('.startPartyBtn').prop('disabled', false);

                            return;
                        }
                        setTimeout(() => {
                            window.location.href = response.redirect_url
                        }, 3000);
                    }
                });
            });


            function initData() {
                const video = document.querySelector('video');
                $.each(data, function() {
                    if (!Hls.isSupported()) {
                        video.src = this.src;
                    } else {
                        if (isM3U8(this.src)) {
                            const hls = new Hls();
                            hls.loadSource(this.src);
                            hls.attachMedia(video);
                            window.hls = hls;
                        }
                    }
                    window.player = player;
                })
            }

            initData();

            function isM3U8(url) {
                return /\.m3u8$/.test(url);
            }
        })(jQuery);
    </script>
@endpush
@push('context')
    oncontextmenu="return false"
@endpush
