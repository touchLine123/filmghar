@extends($activeTemplate . 'layouts.master')

@section('content')
    <section class="section--bg ptb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card custom--card">
                        <div class="card-header">
                            <h5 class="card-title">@lang('Razorpay')</h5>
                        </div>
                        <div class="card-body p-5">
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
                            <form action="{{ $data->url }}" method="{{ $data->method }}">
                                <input type="hidden" custom="{{ $data->custom }}" name="hidden">
                                <script src="{{ $data->checkout_js }}"
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


@push('script')
    <script>
        (function($) {
            "use strict";
            $('input[type="submit"]').addClass("mt-4 btn btn--base w-100");
        })(jQuery);
    </script>
@endpush
