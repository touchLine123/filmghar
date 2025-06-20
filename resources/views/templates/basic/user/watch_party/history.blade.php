@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card-area pt-80 pb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-9 position-relative">
                    <div class="text-end mb-3">
                        <button type="button" class="btn btn--base btn--sm joinPartyBtn">@lang('Join Party')</button>
                    </div>
                    <div class="loader-wrapper">
                        <div class="loader-pre"></div>
                    </div>
                    <div class="">
                        <div class="">
                            <ul class="wishlist-card-list">
                                @include($activeTemplate . 'user.watch_party.fetch_party', ['parties' => $parties])
                            </ul>
                        </div>
                    </div>
                </div>
                @if (@$total > 20 && @$lastId)
                    <div class="load-more-button d-flex justify-content-center mt-5">
                        <button class="btn btn--base" id="load-more-btn" data-last_id="{{ @$lastId }}" type="buttton">@lang('Load More')</button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal alert-modal" id="joinWatchParty" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="" method="POST" id="partyJoinForm">
                    <div class="modal-body">
                        <h5 class="party-modal-title">@lang('Do you want to join the party?')</h5>
                        <div class="form-group mb-0">
                            <input type="text" placeholder="Enter Party Code" name="party_code" class="form--control">
                        </div>
                    </div>
                    <div class="modal-footer flex-nowrap">
                        <button class="btn btn--danger btn--sm" data-bs-dismiss="modal" type="button">@lang('No')</button>
                        <button class="btn btn--base btn--sm" type="submit">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-basic-confirmation-modal />
@endsection
@push('style')
    <style>
        .party-modal-title {
            font-size: 18px;
            margin-bottom: 24px;
        }

        .alert-modal .btn {
            padding-inline: 34px;
        }

        .alert-modal .modal-description {
            font-size: 18px;
            color: #ffffff;
        }

        .alert-modal .modal-footer {
            border: 0;
            justify-content: center;
            padding: 0 1rem;
        }

        .wishlist-image {
            height: 50px;
            width: 50px;
        }

        .wishlist-card__desc {
            font-size: 14px;
        }

        .wishlist-card-wrapper__icon button {
            background: transparent;
            color: red;
            font-size: 20px;
        }

        .wishlist-card-list__item {
            border-bottom: 1px solid #353535;
        }

        .wishlist-card-list__item:last-child {
            border-bottom: none;
        }
    </style>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";
            $('.loader-wrapper').addClass('d-none');
            $('#load-more-btn').on('click', function(e) {
                $(this).attr('disabled', true);
                $('.loader-wrapper').removeClass('d-none');
                var lastId = $(this).data('last_id');
                var url = `{{ route('user.watch.party.history') }}?lastId=${lastId}`;
                $.ajax({
                    type: "GET",
                    url: url,
                    success: function(response) {
                        if (response.error) {
                            notify('error', response.error);
                            $('#load-more-btn').hide()
                            return;
                        }
                        $('#load-more-btn').data('last_id', response.lastId);
                        $('.wishlist-card-list').append(response.data)
                    }
                }).done(function() {
                    $('.loader-wrapper').addClass('d-none')
                    $('#load-more-btn').removeAttr('disabled', true);
                });
            });


            $('.joinPartyBtn').on('click', function(e) {
                let modal = $('#joinWatchParty');
                modal.modal('show');
            });
        })(jQuery)
    </script>
@endpush
