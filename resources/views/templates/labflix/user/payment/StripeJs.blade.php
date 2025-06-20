@extends($activeTemplate . 'layouts.master')
@section('content')
    <section class="pt-80 pb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card custom--card">
                        <div class="card-header">
                            <h5 class="title">@lang('Stripe Storefront')</h5>
                        </div>
                        <div class="card-body p-5">
                            <form action="{{ $data->url }}" method="{{ $data->method }}">
                                <ul class="list-group text-center">
                                    <li class="list-group-item d-flex justify-content-between flex-wrap gap-2">
                                        @lang('You have to pay '):
                                        <strong>{{ showAmount($deposit->final_amount, currencyFormat: false) }} {{ __($deposit->method_currency) }}</strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between flex-wrap gap-2">
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
@push('script-lib')
    <script src="https://js.stripe.com/v3/"></script>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";
            $('button[type="submit"]').addClass("btn btn--base w-100 mt-3");
        })(jQuery);
    </script>
@endpush
