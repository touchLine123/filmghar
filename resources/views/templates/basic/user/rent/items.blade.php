@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card-area pt-80 pb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-9 position-relative">
                    <div class="loader-wrapper">
                        <div class="loader-pre"></div>
                    </div>
                    <div class="">
                        <ul class="wishlist-card-list">
                            @include($activeTemplate . 'user.rent.fetch_item')
                        </ul>
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
    <x-basic-confirmation-modal />
    
@endsection
@push('style')
    <style>
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
                var url = `{{ route('user.rented.item') }}?lastId=${lastId}`;
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
            })
        })(jQuery)
    </script>
@endpush
