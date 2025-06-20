@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="plan-section section--bg ptb-80">
        <div class="container">
            <div class="row justify-content-center gy-4">
                @php
                    $user = auth()->user();
                @endphp
                @forelse ($plans as $plan)
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <div class="plan-item">
                            @auth
                                @if ($user->exp > now() && $user->plan_id == $plan->id)
                                    <div class="package-expire">
                                        <h6 class="package-expire-title">@lang('Expire this after')</h6>
                                        <span class="package-expire-date">{{ diffForHumans($user->exp) }}</span>
                                    </div>
                                @endif
                            @endauth
                            <div class="plan-content">
                                <span class="sub-title">{{ __($plan->name) }}</span>
                                <h2 class="amount">{{ showAmount($plan->pricing) }}</h2>

                                <ul class="package-benifits">
                                    <li class="package-benifits-item">
                                        <span class="package-benifits-icon"><i class="las la-check"></i></span>@lang('Get ' . $plan->duration . ' days subscription')
                                    </li>
                                    <li class="package-benifits-item">
                                        <span class="package-benifits-icon"><i class="las la-check"></i></span>@lang('Show Ads - ') {{ __($plan->showAdStatus) }}
                                    </li>
                                    @if (gs('device_limit'))
                                        <li class="package-benifits-item">
                                            <span class="package-benifits-icon"><i class="las la-check"></i></span> @lang('Connect with ' . $plan->device_limit . ' device')
                                        </li>
                                    @endif
                                </ul>
                                @auth
                                    <button class="plan-btn subscriptionBtn" data-action="{{ route('user.subscribe.plan', $plan->id) }}" @disabled($user->exp > now())>@lang('Subscribe Now')</button>
                                @else
                                    <button class="plan-btn subscribeBtn" type="button">@lang('Subscribe Now')</button>
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                        <img src="{{ asset($activeTemplateTrue . 'images/no-results.png') }}" alt="">
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <div class="modal alert-modal" id="loginAlertModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <span class="alert-icon"><i class="fas fa-question-circle"></i></span>
                    <p class="modal-description">@lang('Confirmation Alert!')</p>
                    <p class="modal--text">@lang('You need to login first')</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn--dark btn--sm" data-bs-dismiss="modal" type="button">@lang('Cancel')</button>
                    <a class="btn btn--base btn--sm" href="{{ route('user.login') }}">@lang('Login Now')</a>
                </div>
            </div>
        </div>
    </div>

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
