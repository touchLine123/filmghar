@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card-area pt-80 pb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-12">
                    <div class="card custom--card">
                        <div class="card-body">
                            <table class="table--responsive--lg table">
                                <thead>
                                    <tr>
                                        <th>@lang('TRX ID')</th>
                                        <th>@lang('Type')</th>
                                        <th>@lang('Gateway')</th>
                                        <th>@lang('Amount')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Time')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($deposits as $k => $deposit)
                                        <tr>
                                            <td>{{ $deposit->trx }}</td>
                                            <td>
                                                @if (@$deposit->subscription->plan)
                                                    <span>@lang('Subscription - ') {{ __(@$deposit->subscription->plan->name) }}</span>
                                                @elseif (@$deposit->subscription->item)
                                                    <span><a class="text--primary" href="{{ route('watch', @$deposit->subscription->item->slug) }}">{{ strLimit(@$deposit->subscription->item->title, 40) }}</a></span>
                                                @else
                                                    <span>@lang('App Purchase')</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold">
                                                    <span class="text--primary">
                                                        @if ($deposit->method_code < 5000)
                                                            {{ __(@$deposit->gateway->name) }}
                                                        @else
                                                            @lang('Google Pay')
                                                        @endif
                                                    </span>
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ showAmount($deposit->amount) }}</strong>
                                            </td>
                                            <td>
                                                {{ showDateTime($deposit->created_at) }}
                                            </td>
                                            <td>
                                                @php echo $deposit->statusBadge @endphp
                                                @if ($deposit->admin_feedback != null)
                                                    <button class="btn--info btn-rounded badge detailBtn" data-admin_feedback="{{ $deposit->admin_feedback }}"><i class="fa fa-info"></i></button>
                                                @endif
                                            </td>
                                            @php
                                                $details = [];
                                                if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000) {
                                                    foreach (@$deposit->detail ?? [] as $key => $info) {
                                                        $details[] = $info;
                                                        if ($info->type == 'file') {
                                                            $details[$key]->value = route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $info->value));
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <td>
                                                @if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000)
                                                    <a href="javascript:void(0)" class="cmn-btn btn--sm detailBtn" data-info="{{ json_encode($details) }}"
                                                       @if ($deposit->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $deposit->admin_feedback }}" @endif>
                                                        <i class="fas fa-desktop"></i>
                                                    </a>
                                                @else
                                                    <button type="button" class="success-btn btn--sm" data-bs-toggle="tooltip" title="@lang('Automatically processed')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center text-muted" colspan="100">{{ __($emptyMessage) }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ paginateLinks($deposits) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- APPROVE MODAL --}}
    <div class="modal fade" id="detailModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <span class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <ul class="list-group userData mb-2">
                    </ul>
                    <div class="feedback"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');

                var userData = $(this).data('info');
                var html = '';
                if (userData) {
                    userData.forEach(element => {
                        if (element.type != 'file') {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${element.name}</span>
                                <span">${element.value}</span>
                            </li>`;
                        } else {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${element.name}</span>
                                <span"><a href="${element.value}"><i class="fa-regular fa-file"></i> @lang('Attachment')</a></span>
                            </li>`;
                        }
                    });
                }

                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);


                modal.modal('show');
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

        })(jQuery);
    </script>
@endpush
