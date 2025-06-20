@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="movie-section section--bg ptb-80">
        <div class="container">
            <div class="row justify-content-center mb-30-none ajaxLoad">
                @forelse($items as $item)
                    @if ($loop->last)
                        <span class="data_id d-none" data-id="{{ $item->id }}"></span>
                        <span class="category_id d-none" data-category_id="{{ @$category->id }}"></span>
                        <span class="subcategory_id d-none" data-subcategory_id="{{ @$subcategory->id }}"></span>
                        <span class="search d-none" data-search="{{ @$search }}"></span>
                    @endif
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                        <div class="movie-item">
                            <div class="movie-thumb">
                                <img src="{{ getImage(getFilePath('item_portrait') . '/' . $item->image->portrait) }}" alt="movie">
                                <span class="movie-badge">{{ __($item->versionName) }}</span>
                                <div class="movie-thumb-overlay">
                                    <a class="video-icon" href="{{ route('watch', $item->slug) }}"><i class="fas fa-play"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-30">
                        <img src="{{ asset($activeTemplateTrue . 'images/no-results.png') }}" alt="">
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    <div class="custom_loading"></div>
@endsection

@push('script')
    <script>
        "use strict"
        var send = 0;
        $(window).scroll(function() {
            if ($(window).scrollTop() + $(window).height() > $(document).height() - 60) {
                if ($('.ajaxLoad').hasClass('loaded')) {
                    $('.custom_loading').removeClass('loader-area');
                    return false;
                }
                $('.custom_loading').addClass('loader-area');
                setTimeout(function() {
                    if (send == 0) {
                        send = 1;
                        var url = '{{ route('loadmore.load_data') }}';
                        var id = $('.data_id').last().data('id');
                        var category_id = $('.category_id').last().data('category_id');
                        var subcategory_id = $('.subcategory_id').last().data('subcategory_id');
                        var search = $('.search').last().data('search');
                        var data = {
                            id: id,
                            category_id: category_id,
                            subcategory_id: subcategory_id,
                            search: search
                        };
                        $.get(url, data, function(response) {
                            if (response == 'end') {
                                $('.custom_loading').removeClass('loader-area');
                                $('.footer').removeClass('d-none');
                                $('.ajaxLoad').addClass('loaded');
                                return false;
                            }
                            $('.custom_loading').removeClass('loader-area');
                            $('.sections').append(response);
                            $('.ajaxLoad').append(response);
                            send = 0;
                        });
                    }
                }, 1000);
            }
        });
    </script>
@endpush
