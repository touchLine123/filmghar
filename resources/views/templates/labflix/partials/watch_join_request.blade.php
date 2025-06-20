<span class="alert-icon"><i class="fas fa-question-circle"></i></span>
<p class="modal-description">@lang('Confirmation Alert!')</p>
<p class="modal--text mb-3">{{ $username }} @lang('want to join party')</p>

<div class="d-flex justify-content-center gap-3">
    <button class="btn btn--dark btn--sm joinRejectBtn" data-memberid="{{ $memberId }}" type="button">@lang('Reject')</button>
    <button class="btn btn--base btn--sm joinAcceptBtn" data-memberid="{{ $memberId }}" type="button">@lang('Accept')</button>
</div>
