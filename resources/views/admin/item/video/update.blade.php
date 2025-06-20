@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ @$route }}" id="uploadForm" method="POST" enctype="multipart/form-data">
                        @csrf<ul class="nav nav-pills mb-3 video-quality" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-360p-tab" data-bs-toggle="pill" data-bs-target="#pills-360p" type="button" role="tab" aria-controls="pills-360p" aria-selected="true">@lang('Video File 360P')</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-480p-tab" data-bs-toggle="pill" data-bs-target="#pills-480p" type="button" role="tab" aria-controls="pills-480p" aria-selected="false">@lang('Video File 480P')</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-720p-tab" data-bs-toggle="pill" data-bs-target="#pills-720p" type="button" role="tab" aria-controls="pills-720p" aria-selected="false">@lang('Video File 720P')</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-1080p-tab" data-bs-toggle="pill" data-bs-target="#pills-1080p" type="button" role="tab" aria-controls="pills-1080p" aria-selected="false">@lang('Video File 1080P')</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade" id="pills-360p" role="tabpanel" aria-labelledby="pills-360p-tab" tabindex="0">

                                <x-update-video-form video="{{ $video->three_sixty_video }}" selectValue="{{ $video->video_type_three_sixty }}" videoType="three-sixty" fileType="360P" />

                                @if ($videoFile['three_sixty'])
                                    <x-admin-video-player :poster="$posterImage" :videoFile="$videoFile['three_sixty']" />
                                @endif
                            </div>
                            <div class="tab-pane fade" id="pills-480p" role="tabpanel" aria-labelledby="pills-480p-tab" tabindex="0">

                                <x-update-video-form video="{{ $video->four_eighty_video }}" selectValue="{{ $video->video_type_four_eighty }}" videoType="four-eighty" fileType="480P" />

                                @if ($videoFile['four_eighty'])
                                    <x-admin-video-player :poster="$posterImage" :videoFile="$videoFile['four_eighty']" />
                                @endif
                            </div>

                            <div class="tab-pane fade show active" id="pills-720p" role="tabpanel" aria-labelledby="pills-720p-tab" tabindex="0">
                                <x-update-video-form video="{{ $video->seven_twenty_video }}" selectValue="{{ $video->video_type_seven_twenty }}" videoType="seven-twenty" fileType="720P" />

                                @if ($videoFile['seven_twenty'])
                                    <x-admin-video-player :poster="$posterImage" :videoFile="$videoFile['seven_twenty']" />
                                @endif
                            </div>

                            <div class="tab-pane fade" id="pills-1080p" role="tabpanel" aria-labelledby="pills-1080p-tab" tabindex="0">
                                <x-update-video-form video="{{ $video->thousand_eighty_video }}" selectValue="{{ $video->video_type_thousand_eighty }}" videoType="thousand-eighty" fileType="1080P" />

                                @if ($videoFile['thousand_eighty'])
                                    <x-admin-video-player :poster="$posterImage" :videoFile="$videoFile['thousand_eighty']" />
                                @endif
                            </div>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('style')
    <style type="text/css">
        .upload {
            margin-right: auto;
            margin-left: auto;
            width: 100%;
            height: 200px;
            margin-top: 20px;
            border: 3px dashed #929292;
            line-height: 200px;
            font-size: 18px;
            line-height: unset !important;
            display: table;
            text-align: center;
            margin-bottom: 20px;
            color: #929292;
        }

        .upload:hover {
            border: 3px dashed #04abf2;
            cursor: pointer;
            color: #04abf2;
        }

        .upload.hover {
            border: 3px dashed #04abf2;
            cursor: pointer;
            color: #04abf2;
        }

        .upload>div {
            display: table-cell;
            vertical-align: middle;
        }

        .upload>div h4 {
            padding: 0;
            margin: 0;
            font-size: 25px;
            font-weight: 700;
            font-family: Lato, sans-serif;
        }

        .upload>div p {
            padding: 0;
            margin: 0;
            font-family: Lato, sans-serif;
        }

        .upload-video-file {
            opacity: 0;
            position: fixed;
        }

        .video-quality .nav-link {
            border: 1px solid #0d6efd;
        }

        .video-quality {
            gap: 10px !important;
        }
    </style>
@endpush

@push('breadcrumb-plugins')
    <a class="btn btn--sm btn-outline--primary" href="{{ $prevUrl }}"><i class="la la-undo"></i> @lang('Back')</a>
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
            color: #4634ff;
        }

        .plyr__menu__container .plyr__control[role=menuitemradio][aria-checked=true]:before {
            background: #4634ff;
        }
    </style>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/plyr.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/plyr.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/hls.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $(".three-sixty").on("click", function(e) {
                e.stopPropagation();
            });
            $(".four-eighty").on("click", function(e) {
                e.stopPropagation();
            });
            $(".seven-twenty").on("click", function(e) {
                e.stopPropagation();
            });
            $(".thousand-eighty").on("click", function(e) {
                e.stopPropagation();
            });

            $(".three-sixty-video").on("click", function(e) {
                $('.three-sixty').trigger("click");
            });
            $(".four-eighty-video").on("click", function(e) {
                $('.four-eighty').trigger("click");
            });
            $(".seven-twenty-video").on("click", function(e) {
                $('.seven-twenty').trigger("click");
            });
            $(".thousand-eighty-video").on("click", function(e) {
                $('.thousand-eighty').trigger("click");
            });

            $("[name=video_type_three_sixty]").on('change', function() {
                if ($(this).val() == '0') {
                    $("#three_sixty_link").show();
                    $("#three_sixty_video").hide();
                } else {
                    $("#three_sixty_link").hide();
                    $("#three_sixty_video").show();
                }
            }).change();

            $("[name=video_type_four_eighty]").on('change', function() {
                if ($(this).val() == '0') {
                    $("#four_eighty_link").show();
                    $("#four_eighty_video").hide();
                } else {
                    $("#four_eighty_link").hide();
                    $("#four_eighty_video").show();
                }
            }).change();

            $("[name=video_type_seven_twenty]").on('change', function() {
                if ($(this).val() == '0') {
                    $("#seven_twenty_link").show();
                    $("#seven_twenty_video").hide();
                } else {
                    $("#seven_twenty_link").hide();
                    $("#seven_twenty_video").show();
                }
            }).change();

            $("[name=video_type_thousand_eighty]").on('change', function() {
                if ($(this).val() == '0') {
                    $("#thousand_eighty_link").show();
                    $("#thousand_eighty_video").hide();
                } else {
                    $("#thousand_eighty_link").hide();
                    $("#thousand_eighty_video").show();
                }
            }).change();


            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData($(this)[0]);

                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    url: "{{ @$route }}",
                    method: "POST",
                    data: formData,
                    async: false,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.error) {
                            notify('error', response.error);
                        } else {
                            window.location.href = "{{ route('admin.item.index') }}"
                        }
                    }
                });
            });



            let players = $('.video-player');
            $.each(players, function(index, singlePlayer) {
                const video = singlePlayer;
                var type = singlePlayer.getAttribute('data-video_type');
                const source = video.currentSrc;
                const player = new Plyr(singlePlayer, {
                    ratio: '16:9',
                });

                if (!Hls.isSupported()) {
                    video.src = source;
                } else {
                    if (isM3U8(source)) {
                        const hls = new Hls();
                        hls.loadSource(source);
                        hls.attachMedia(video);
                        window.hls = hls;
                    }
                }
                window.player = player;
            });

            function isM3U8(url) {
                return /\.m3u8$/.test(url);
            }
        })(jQuery)
    </script>
@endpush
