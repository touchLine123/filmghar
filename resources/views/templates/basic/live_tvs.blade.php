@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="section--bg ptb-80">
        <div class="container">
            <div class="row justify-content-center gy-4">
                @forelse ($tvs as $tv)
                    <div class="col-lg-2 col-sm-3 col-6">
                        <div class="tv-card">
                            <div class="tv-card__thumb">
                                <a href="{{ route('watch.tv', $tv->id) }}"><img src="{{ getImage(getFilePath('television') . '/' . $tv->image, getFileSize('television')) }}" class="w-100" alt=""></a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <img src="{{ asset($activeTemplateTrue . 'images/no-results.png') }}" alt="@lang('image')">
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
