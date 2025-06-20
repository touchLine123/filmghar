@props(['poster' => '', 'videoFile' => ''])
<div class="row d-flex justify-content-center mt-3">
    <div class="col-md-12 col-lg-6">
        <video class="video-player plyr-video" playsinline controls data-poster="{{ $poster }}">
            <source src="{{ $videoFile }}" type="video/mp4" />
        </video>
    </div>
</div>
