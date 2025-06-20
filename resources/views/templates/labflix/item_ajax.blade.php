@foreach($items as $item)
<div class="col-xxl-2 col-md-3 col-4 col-xs-6 mb-30">
	<div class="movie-card @if($item->item_type == 1 && $item->version == 1 || $item->item_type == 2) paid @endif " @if($item->item_type == 1 && $item->version == 0) data-text="@lang('Free')" @elseif($item->item_type == 3) data-text="@lang('Trailer')" @endif>
		<div class="movie-card__thumb thumb__2">
			<img src="{{ getImage(getFilePath('item_portrait').'/'.@$item->image->portrait) }}" alt="image">
            <a href="{{ route('watch',$item->id) }}" class="icon"><i class="fas fa-play"></i></a>
		</div>
	</div>
</div>
@if($loop->last)
<span class="data_id" data-id="{{ $item->id }}"></span>
<span class="category_id" data-category_id="{{ @$category->id }}"></span>
<span class="sub_category_id" data-sub_category_id="{{ @$sub_category->id }}"></span>
<span class="search" data-search="{{ @$search }}"></span>
@endif
@endforeach