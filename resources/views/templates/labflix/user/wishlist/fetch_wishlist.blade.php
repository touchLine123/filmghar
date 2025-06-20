@forelse($wishlists as $wishlist)
    <li class="wishlist-card-list__item">
        <div class="wishlist-card-wrapper">
            <a class="wishlist-card-list__link" href="{{ @$wishlist->item->url }}">
                <div class="wishlist-card">
                    <div class="wishlist-card__thumb">
                        @if ($wishlist->episode_id)
                            <img src="{{ getImage(getFilePath('episode') . '/' . @$wishlist->episode->image) }}" alt="@lang('image')">
                        @else
                            <img src="{{ getImage(getFilePath('item_portrait') . '/' . @$wishlist->item->image->portrait) }}" alt="@lang('image')">
                        @endif
                    </div>
                    <div class="wishlist-card__content">
                        <h5 class="wishlist-card__title">
                            @if ($wishlist->episode_id)
                                {{ __(@$wishlist->episode->item->title) }} - {{ __(@$wishlist->episode->title) }}
                            @else
                                {{ __(@$wishlist->item->title) }}
                            @endif
                        </h5>
                        <p class="wishlist-card__desc text-white">{{ strLimit(@$wishlist->item->description, 60) }}</p>
                    </div>
                </div>
            </a>
            <div class="wishlist-card-wrapper__icon">
                <button class="text--danger basicConfirmationBtn" data-action="{{ route('user.wishlist.remove', $wishlist->id) }}" data-question="@lang('Are you sure to remove this item?')" type="button"><i class="las la-times"></i></button>
            </div>
        </div>
    </li>
@empty
    <li class="text-center">
        <i class="las text-muted la-4x la-clipboard-list"></i><br>
        <h4 class="mt-2 text-muted">@lang('No items found yet!')</h4>
    </li>
@endforelse
