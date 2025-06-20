@foreach($items as $item)
    @if($loop->last)
        <span class="data_id d-none" data-id="{{ $item->id }}"></span>
        <span class="category_id d-none" data-category_id="{{ @$category->id }}"></span>
        <span class="sub_category_id d-none" data-sub_category_id="{{ @$sub_category->id }}"></span>
        <span class="search d-none" data-search="{{ @$search }}"></span>
    @endif

    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
        <div class="movie-item">
            <div class="movie-thumb">
                <img src="{{ getImage(getFilePath('item_portrait').'/'.$item->image->portrait) }}" alt="movie">
                @if($item->item_type == 1 && $item->version == 0)
                    <span class="movie-badge">@lang('Free')</span>
                @elseif($item->item_type == 3)
                    <span class="movie-badge">@lang('Trailer')</span>
                @endif

                <div class="movie-thumb-overlay">
                    <a class="video-icon" href="{{ route('watch',$item->id) }}"><i class="fas fa-play"></i></a>
                </div>
            </div>
        </div>
    </div>
@endforeach
