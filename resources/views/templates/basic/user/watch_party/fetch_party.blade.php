@forelse($parties as $party)
    <li class="wishlist-card-list__item">
        <div class="wishlist-card-wrapper">
            <a class="wishlist-card-list__link" href="@if ($party->status == Status::ENABLE) {{ route('user.watch.party.room', $party->party_code) }} @else javascript:void(0) @endif">
                <div class="wishlist-card">
                    <div class="wishlist-card__thumb">
                        @if ($party->episode_id)
                            <img src="{{ getImage(getFilePath('episode') . '/' . @$party->episode->image) }}" alt="@lang('image')">
                        @else
                            <img src="{{ getImage(getFilePath('item_portrait') . '/' . @$party->item->image->portrait) }}" alt="@lang('image')">
                        @endif
                    </div>
                    <div class="wishlist-card__content">
                        <h5 class="wishlist-card__title">
                            @if ($party->episode)
                                <span>{{ __(@$party->episode->title) }}</span>
                            @else
                                <span>{{ __(@$party->item->title) }}</span>
                            @endif
                        </h5>
                        <p class="wishlist-card__desc mb-0">{{ strLimit(@$party->item->description, 60) }}</p>
                        <span>
                            @php
                                echo $party->statusBadge;
                            @endphp
                        </span>
                    </div>
                </div>
            </a>
            @if ($party->status == Status::ENABLE)
                <div class="wishlist-card-wrapper__icon">
                    <button class="text--base basicConfirmationBtn" data-action="{{ route('user.watch.party.disabled', $party->id) }}" data-question="@lang('Are you sure to disbaled this party room?')" type="button"><i class="las la-times"></i></button>
                </div>
            @endif
        </div>
    </li>
@empty
    <li class="text-center">
        <i class="las text-muted la-4x la-clipboard-list"></i><br>
        <h4 class="mt-2 text-muted">@lang('No items found yet!')</h4>
    </li>
@endforelse
