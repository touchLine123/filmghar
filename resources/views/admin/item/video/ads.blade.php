@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card mt-3">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <video class="video-player" playsinline controls data-poster="{{ getImage(getFilePath('item_landscape') . '/' . @$item->image->landscape) }}">
                                <source src="{{ $videoFile }}" type="video/mp4" />
                            </video>
                            <div class="mt-3 text-end">
                                <button class="btn btn--primary h-45 ad-time-btn" data-ad_time="0" type="button"><i class="las la-plus"></i> @lang('Add Advertisement Duration')</button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <form action="{{ route('admin.item.ads.duration.store', [$item->id, $episodeId]) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>@lang('Ad Time')</label>
                                </div>
                                <div class="ad-time-area">
                                    @if ($video->ads_time)
                                        @foreach ($video->ads_time as $time)
                                            <div class="col-md-12 single-ad-time mb-2">
                                                <div class="input-group clockpicker">
                                                    <input class="form-control single-input" id="single-input" name="ads_time[]" type="text" value="{{ $time }}" readonly>
                                                    <span class="input-group-text">@lang('minutes')</span>
                                                    <button class="input-group-text bg--danger remove-ad-time border-0" type="button">@lang('Remove')</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <button class="btn btn--primary h-45 w-100 submit-btn @if (!$video->ads_time) d-none @endif">@lang('Submit')</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.item.index') }}" />
@endpush
@push('style-lib')
    <link href="{{ asset('assets/global/css/plyr.min.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/plyr.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function() {
            "use strict";
            const controls = [
                'play-large',
                'restart',
                'rewind',
                'play',
                'fast-forward',
                'progress',
                'current-time',
                'duration',
                'mute',
                'volume',
                'captions',
                'settings',
                'pip',
                'airplay',
                'fullscreen'
            ];
            let player = new Plyr('.video-player', {
                controls,
                tooltips: {
                    controls: true
                },
                captions: {
                    active: true
                },
            });

            var currentTime = null;

            player.on('timeupdate', function(e) {
                var updatedTime = Math.floor(player.currentTime);
                if (currentTime !== updatedTime) {
                    currentTime = updatedTime
                    var minutes = Math.floor(currentTime / 60) + ":" + (currentTime % 60 ? currentTime % 60 : '00');
                    $('.ad-time-btn').data('ad_time', minutes)
                }
            });

            var i = 0;
            $('.ad-time-btn').on('click', function() {
                i++;
                if (i > 0) {
                    $('.submit-btn').removeClass('d-none')
                }
                var minutes = $(this).data('ad_time');
                if (!minutes) {
                    minutes = '0:00'
                }
                var html = `<div class="col-md-12 mb-2 single-ad-time">
                            <div class="input-group clockpicker">
                                <input class="form-control single-input" id="single-input" type="text" name="ads_time[]" value=${minutes} readonly>
                                <span class="input-group-text">@lang('minutes')</span>
                                <button class="input-group-text bg--danger border-0 remove-ad-time" type="button">@lang('Remove')</button>
                            </div>
                        </div>`;
                $('.ad-time-area').append(html);
            });
            $(document).on('click', '.remove-ad-time', function() {
                i--;
                if (i == 0) {
                    $('.submit-btn').addClass('d-none')
                }
                $(this).closest('.single-ad-time').remove();
            });
        })(jQuery)
    </script>
@endpush

@push('style')
    <style>
        .plyr__control--overlaid,
        .plyr--video .plyr__control:focus-visible,
        .plyr--video .plyr__control:hover,
        .plyr--video .plyr__control[aria-expanded=true] {
            background: #4634ff;
        }

        .plyr--full-ui input[type=range] {
            color: #4634ff
        }

        .plyr__menu__container .plyr__control[role=menuitemradio][aria-checked=true]:before {
            background: #4634ff;
        }
    </style>
@endpush
