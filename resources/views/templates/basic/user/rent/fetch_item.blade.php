@forelse($rentedItems as $rentedItem)
    <li class="wishlist-card-list__item">
        <div class="wishlist-card-wrapper">
            <a class="wishlist-card-list__link" href="{{ route('watch', @$rentedItem->item->slug) }}">
                <div class="wishlist-card">
                    <div class="wishlist-card__thumb">
                        <img src="{{ getImage(getFilePath('item_portrait') . '/' . @$rentedItem->item->image->portrait) }}" alt="@lang('image')">
                    </div>
                    <div class="wishlist-card__content">
                        <h5 class="wishlist-card__title">{{ __($rentedItem->item->title) }}</h5>
                        <p class="wishlist-card__desc">{{ strLimit(@$rentedItem->item->description, 60) }}</p>
                    </div>
                </div>
            </a>
        </div>
    </li>
@empty
    <li class="text-center">
        <i class="las text-muted la-4x la-clipboard-list"></i><br>
        <h4 class="mt-2 text-muted">@lang('No items found yet!')</h4>
    </li>
@endforelse
