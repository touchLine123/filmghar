@foreach ($partyMembers as $partyMember)
    <div class="chat-item block-unblock d-flex align-items-center justify-content-between" id="member-{{ $partyMember->id }}">
        <div class="left">
            <div class="thumb">
                <img src="{{ getImage(getFilePath('userProfile') . '/' . @$partyMember->user->username, getFileSize('userProfile')), true }}" alt="@lang('image')">
            </div>
            <span class="username"><span>@</span>{{ @$partyMember->user->username }}</span>
        </div>
        @if (@$partyMember->status == Status::WATCH_PARTY_REQUEST_ACCEPTED && @$partyRoom->user_id == @$hostId)
            <button class="btn-transparent changeStatusBtn" data-action="{{ route('user.watch.party.status', $partyMember->id) }}" data-question="@lang('Are you sure to remove this user?')">@lang('Remove')</button>
        @endif
    </div>
@endforeach
