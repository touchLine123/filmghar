@props([
    'fileType' => '',
    'videoType' => '',
    'video' => '',
    'selectValue' => '',
    'videoFile' => '',
])

@php
    $type = str_replace('-', '_', $videoType);
    $videoTypeName = 'video_type_' . $type;
    $fileName = $type . '_video';
    $linkName = $type . '_link';
    $videoLink = $selectValue == 1 ? null : $video;

@endphp

<h5 class="my-4">@lang('Video File '){{ $fileType }}</h5>
<div class="form-group col-md-12">
    <label>@lang('Video Type')</label>
    <select class="form-control" name="{{ $videoTypeName }}" required>
        <option value="1" @selected($selectValue == 1)>@lang('Video')</option>
        <option value="0" @selected($selectValue == 0)>@lang('Link')</option>
    </select>
</div>
<div class="form-group" id="{{ $fileName }}">
    <div class="upload {{ $videoType }}-video">
        <div>
            <svg class="feather feather-upload" fill="currentColor" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                <path d="M14,13V17H10V13H7L12,8L17,13M19.35,10.03C18.67,6.59 15.64,4 12,4C9.11,4 6.6,5.64 5.35,8.03C2.34,8.36 0,10.9 0,14A6,6 0 0,0 6,20H19A5,5 0 0,0 24,15C24,12.36 21.95,10.22 19.35,10.03Z" />
            </svg>
            <h4> @lang('Darg Drop Video')</h4>
            <p>@lang('or Click to choose File')</p>
            <button class="btn btn--primary" type="button">@lang('Upload')</button>
        </div>
    </div>
    <input class="upload-video-file {{ $videoType }}" name="{{ $fileName }}" type="file" />
</div>
<div class="form-group" id="{{ $linkName }}">
    <label>@lang('Insert Link')</label>
    <input class="form-control" name="{{ $linkName }}" value="{{ @$videoLink }}" type="text" placeholder="@lang('Insert Link')" />
</div>
