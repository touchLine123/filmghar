@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pt-80 pb-80">
        <div class="container">
            <div class="row justify-content-center mb-30-none">
                @include($activeTemplate . 'partials.plans')
            </div>
        </div>
    </section>

    <div class="modal alert-modal" id="subscriptionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <span class="alert-icon"><i class="fas fa-question-circle"></i></span>
                        <p class="modal-description">@lang('Confirmation Alert!')</p>
                        <p class="modal--text">@lang('Are you sure to subscribe this plan?')</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--dark btn--sm" data-bs-dismiss="modal" type="button">@lang('No')</button>
                        <button class="btn btn--base btn--sm" type="submit">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal alert-modal" id="loginAlertModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <span class="alert-icon"><i class="fas fa-question-circle"></i></span>
                        <p class="modal-description">@lang('Confirmation Alert!')</p>
                        <p class="modal--text">@lang('You need to login first')</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--dark btn--sm" data-bs-dismiss="modal" type="button">@lang('Cancel')</button>
                        <a class="btn btn--base btn--sm" href="{{ route('user.login') }}">@lang('Login Now')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.subscribeBtn').on('click', function(e) {
                let modal = $('#loginAlertModal');
                modal.modal('show');
            });

            $('.subscriptionBtn').on('click', function(e) {
                let modal = $('#subscriptionModal');
                let data = $(this).data();
                modal.find('form').attr('action', `${data.action}`);
                modal.modal('show');
            });
        })(jQuery)
    </script>
@endpush
