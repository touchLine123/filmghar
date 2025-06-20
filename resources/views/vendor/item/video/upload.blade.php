@extends('vendor.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="" id="uploadForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <ul class="nav nav-pills mb-3 video-quality" id="pills-tab" role="tablist">
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
                                <h5 class="my-4">@lang('Video File 360P')</h5>
                                <div class="form-group col-md-12">
                                    <label>@lang('Video Type')</label>
                                    <select class="form-control" name="video_type_three_sixty" required>
                                        <option value="1">@lang('Video')</option>
                                        <option value="0">@lang('Link')</option>
                                    </select>
                                </div>
                                <div class="form-group" id="three_sixty_video">
                                    <div class="upload three-sixty-video" data-block="video-drop-zone">
                                        <div>
                                            <svg class="feather feather-upload" fill="currentColor" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M14,13V17H10V13H7L12,8L17,13M19.35,10.03C18.67,6.59 15.64,4 12,4C9.11,4 6.6,5.64 5.35,8.03C2.34,8.36 0,10.9 0,14A6,6 0 0,0 6,20H19A5,5 0 0,0 24,15C24,12.36 21.95,10.22 19.35,10.03Z" />
                                            </svg>
                                            <h4> @lang('Darg Drop Video')</h4>
                                            <p>@lang('or Click to choose File')</p>
                                            <button class="btn btn--primary" type="button">@lang('Upload')</button>
                                        </div>
                                    </div>
                                    <input class="upload-video-file three-sixty" name="three_sixty_video" type="file" accept=".mp4,.mkv,.3gp" />
                                </div>
                                <div class="form-group" id="three_sixty_link">
                                    <label>@lang('Insert Link')</label>
                                    <input class="form-control" name="three_sixty_link" type="text" placeholder="@lang('Inert Link')" />
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-480p" role="tabpanel" aria-labelledby="pills-480p-tab" tabindex="0">
                                <h5 class="my-4">@lang('Video File 480P')</h5>
                                <div class="form-group col-md-12">
                                    <label>@lang('Video Type')</label>
                                    <select class="form-control" name="video_type_four_eighty" required>
                                        <option value="1">@lang('Video')</option>
                                        <option value="0">@lang('Link')</option>
                                    </select>
                                </div>
                                <div class="form-group" id="four_eighty_video">
                                    <div class="upload four-eighty-video" data-block="video-drop-zone">
                                        <div>
                                            <svg class="feather feather-upload" fill="currentColor" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M14,13V17H10V13H7L12,8L17,13M19.35,10.03C18.67,6.59 15.64,4 12,4C9.11,4 6.6,5.64 5.35,8.03C2.34,8.36 0,10.9 0,14A6,6 0 0,0 6,20H19A5,5 0 0,0 24,15C24,12.36 21.95,10.22 19.35,10.03Z" />
                                            </svg>
                                            <h4> @lang('Darg Drop Video')</h4>
                                            <p>@lang('or Click to choose File')</p>
                                            <button class="btn btn--primary" type="button">@lang('Upload')</button>
                                        </div>
                                    </div>
                                    <input class="upload-video-file four-eighty" name="four_eighty_video" type="file" accept=".mp4,.mkv,.3gp" />
                                </div>
                                <div class="form-group" id="four_eighty_link">
                                    <label>@lang('Insert Link')</label>
                                    <input class="form-control" name="four_eighty_link" type="text" placeholder="@lang('Inert Link')" />
                                </div>
                            </div>
                            <div class="tab-pane fade show active" id="pills-720p" role="tabpanel" aria-labelledby="pills-720p-tab" tabindex="0">
                                <h5 class="my-4">@lang('Video File 720P')</h5>
                                <div class="form-group">
                                    <label>@lang('Video Type')</label>
                                    <select class="form-control" name="video_type_seven_twenty" required>
                                        <option value="1">@lang('Video')</option>
                                        <option value="0">@lang('Link')</option>
                                    </select>
                                </div>
                                <div class="form-group" id="seven_twenty_video">
                                    <div class="upload seven-twenty-video" data-block="video-drop-zone">
                                        <div>
                                            <svg class="feather feather-upload" fill="currentColor" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M14,13V17H10V13H7L12,8L17,13M19.35,10.03C18.67,6.59 15.64,4 12,4C9.11,4 6.6,5.64 5.35,8.03C2.34,8.36 0,10.9 0,14A6,6 0 0,0 6,20H19A5,5 0 0,0 24,15C24,12.36 21.95,10.22 19.35,10.03Z" />
                                            </svg>
                                            <h4> @lang('Darg Drop Video')</h4>
                                            <p>@lang('or Click to choose File')</p>
                                            <button class="btn btn--primary" type="button">@lang('Upload')</button>
                                        </div>
                                    </div>
                                    <input class="upload-video-file seven-twenty" name="seven_twenty_video" type="file" accept=".mp4,.mkv,.3gp" />
                                </div>
                                <div class="form-group" id="seven_twenty_link">
                                    <label>@lang('Insert Link')</label>
                                    <input class="form-control" name="seven_twenty_link" type="text" placeholder="@lang('Inert Link')" />
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-1080p" role="tabpanel" aria-labelledby="pills-1080p-tab" tabindex="0">
                                <h5 class="my-4">@lang('Video File 1080P')</h5>
                                <div class="form-group col-md-12">
                                    <label>@lang('Video Type')</label>
                                    <select class="form-control" name="video_type_thousand_eighty" required>
                                        <option value="1">@lang('Video')</option>
                                        <option value="0">@lang('Link')</option>
                                    </select>
                                </div>
                                <div class="form-group" id="thousand_eighty_video">
                                    <div class="upload thousand-eighty-video" data-block="video-drop-zone">
                                        <div>
                                            <svg class="feather feather-upload" fill="currentColor" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M14,13V17H10V13H7L12,8L17,13M19.35,10.03C18.67,6.59 15.64,4 12,4C9.11,4 6.6,5.64 5.35,8.03C2.34,8.36 0,10.9 0,14A6,6 0 0,0 6,20H19A5,5 0 0,0 24,15C24,12.36 21.95,10.22 19.35,10.03Z" />
                                            </svg>
                                            <h4> @lang('Darg Drop Video')</h4>
                                            <p>@lang('or Click to choose File')</p>
                                            <button class="btn btn--primary" type="button">@lang('Upload')</button>
                                        </div>
                                    </div>
                                    <input class="upload-video-file thousand-eighty" name="thousand_eighty_video" type="file" accept=".mp4,.mkv,.3gp" />
                                </div>
                                <div class="form-group" id="thousand_eighty_link">
                                    <label>@lang('Insert Link')</label>
                                    <input class="form-control" name="thousand_eighty_link" type="text" placeholder="@lang('Inert Link')" />
                                </div>
                            </div>
                        </div>
                        <div class="">
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

@push('script')
    <script>
        (function($) {
            "use strict"
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
                            notify('success', response.success);
                            window.location.href = "{{ route('vendor.item.index') }}"
                        }
                    }
                });
            });
        })(jQuery)
    </script>
@endpush
