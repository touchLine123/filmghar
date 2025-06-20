@forelse($histories as $history)
    <li class="wishlist-card-list__item">
        <div class="wishlist-card-wrapper">
            <a class="wishlist-card-list__link" href="{{ route('watch', [@$history->item ? @$history->item->slug : @$history->episode->item->slug]) }}">
                <div class="wishlist-card">
                    <div class="wishlist-card__thumb">
                        @if ($history->item_id)
                            <img src="{{ getImage(getFilePath('item_portrait') . '/' . @$history->item->image->portrait) }}" alt="@lang('image')">
                        @else
                            <img src="{{ getImage(getFilePath('episode') . '/' . @$history->episode->image) }}" alt="@lang('image')">
                        @endif
                    </div>
                    <div class="wishlist-card__content">
                        <h5 class="wishlist-card__title">
                            @if ($history->item_id)
                                {{ __(@$history->item->title) }}
                            @else
                                {{ __(@$history->episode->item->title) }} - {{ __(@$history->episode->title) }}
                            @endif
                        </h5>
                        <p class="wishlist-card__desc text-white">{{ strLimit(@$history->item->description, 60) }}</p>
                    </div>
                </div>
            </a>
            <div class="wishlist-card-wrapper__icon">
                <button class="text--danger basicConfirmationBtn" data-action="{{ route('user.remove.history', $history->id) }}" data-question="@lang('Are you sure to remove this item?')" type="button"><i class="las la-times"></i></button>
            </div>
        </div>
    </li>
@empty
    <li class="text-center">
        <i class="las text-muted la-4x la-clipboard-list"></i><br>
        <h4 class="mt-2 text-muted">@lang('No items found yet!')</h4>
    </li>
@endforelse
