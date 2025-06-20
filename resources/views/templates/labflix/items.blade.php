@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pt-80 pb-80">
        <div class="container-fluid">
            <div class="row mb-none-30 ajaxLoad">
                @forelse($items as $item)
                    <div class="col-xxl-2 col-md-3 col-4 col-xs-6 mb-30">
                        <div class="movie-card" data-text="{{ $item->versionName }}">
                            <div class="movie-card__thumb thumb__2">
                                <img src="{{ getImage(getFilePath('item_portrait') . '/' . $item->image->portrait) }}" alt="@lang('image')">
                                <a href="{{ route('watch', $item->slug) }}" class="icon"><i class="fas fa-play"></i></a>
                            </div>
                        </div>
                    </div>
                    @if ($loop->last)
                        <span class="data_id" data-id="{{ $item->id }}"></span>
                        <span class="category_id" data-category_id="{{ @$category->id }}"></span>
                        <span class="subcategory_id" data-subcategory_id="{{ @$subcategory->id }}"></span>
                        <span class="search" data-search="{{ @$search }}"></span>
                    @endif
                @empty
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-30 mx-auto">
                        <img src="{{ asset($activeTemplateTrue . 'images/no-results.png') }}" alt="@lang('image')">
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
@push('script')
    <script type="text/javascript">
        "use strict"
        var send = 0;
        $(window).scroll(function() {
            if ($(window).scrollTop() + $(window).height() > $(document).height() - 60) {
                if ($('.ajaxLoad').hasClass('loaded')) {
                    $('.loading').removeClass('loader');
                    return false;
                }
                $('.loading').addClass('loader');
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
                                $('.loading').removeClass('loader');
                                $('.footer').removeClass('d-none');
                                $('.ajaxLoad').addClass('loaded');
                                return false;
                            }
                            $('.loading').removeClass('loader');
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
