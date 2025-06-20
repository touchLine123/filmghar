@extends($activeTemplate . 'layouts.master')
@section('content')
    <section class="section--bg ptb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card custom--card">
                        <div class="card-header">
                            <h5 class="card-title">@lang('Stripe Storefront')</h5>
                        </div>
                        <div class="card-body p-5">
                            <form action="{{ $data->url }}" method="{{ $data->method }}">
                                <ul class="list-group text-center">
                                    <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 px-0">
                                        @lang('You have to pay '):
                                        <strong>{{ showAmount($deposit->final_amount, currencyFormat: false) }} {{ __($deposit->method_currency) }}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 px-0">
                                        @lang('You will get '):
                                        <strong>{{ showAmount($deposit->amount) }}</strong>
                                    </li>
                                </ul>
                                <script src="{{ $data->src }}"
                                        class="stripe-button"
                                        @foreach ($data->val as $key => $value)
                            data-{{ $key }}="{{ $value }}" @endforeach></script>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('style')
    <style>
        .list-group-item {
            background-color: transparent;
            color: #fff;
            border-bottom: 1px solid #474747;
            border-radius: 0 !important;
        }
    </style>
@endpush

@push('script-lib')
    <script src="https://js.stripe.com/v3/"></script>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";
            $('button[type="submit"]').removeClass().addClass("btn btn--base w-100 mt-3").text("Pay Now");
        })(jQuery);
    </script>
@endpush
