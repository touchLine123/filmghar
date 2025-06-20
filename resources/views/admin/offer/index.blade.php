@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card  ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('S.N')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('IAP Code')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Duration')</th>
                                    <th>@lang('Ads')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $plan)
                                    <tr>
                                        <td>{{ $plans->firstItem() + $loop->index }}</td>
                                        <td>{{ __($plan->name) }}</td>
                                        <td>{{ $plan->app_code }}</td>
                                        <td>{{ showAmount($plan->pricing) }}</td>
                                        <td>{{ $plan->duration }} @lang('days')</td>
                                        <td>
                                            <span>{{ $plan->showAdStatus }}</span>
                                        </td>
                                        <td>
                                            @php
                                                echo $plan->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn--sm btn-outline--primary editBtn" data-plan="{{ $plan }}"><i class="la la-pencil"></i>@lang('Edit')</button>
                                                @if ($plan->status == Status::ENABLE)
                                                    <button class="btn btn--sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure disbale this plan')?" data-action="{{ route('admin.plan.status', $plan->id) }}"><i class="la la-eye-slash"></i>@lang('Disable')</button>
                                                @else
                                                    <button class="btn btn--sm btn-outline--success confirmationBtn" data-question="@lang('Are you sure enable this plan')?" data-action="{{ route('admin.plan.status', $plan->id) }}"><i class="la la-eye"></i>@lang('Enable')</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($plans->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($plans) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Plan Modal -->
    <div class="modal fade" id="planModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input class="form-control" name="name" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Price')</label>
                            <div class="input-group">
                                <input class="form-control" name="price" type="number" step="any" required>
                                <span class="input-group-text">{{ gs('cur_text') }}</span>
                            </div>
                        </div>
                        <!-- <div class="form-group">
                            <label>@lang('App Purchase Code')</label><span title="@lang('You need to add this code to the Google Play Store and App Store')"><i class="la la-info-circle"></i></span>
                            <input class="form-control" name="app_code" type="text" required>
                        </div> -->
                        <!-- <div class="form-group">
                            <label>@lang('Icon')</label>
                            <div class="input-group">
                                <input class="form-control iconPicker icon" name="icon" type="text" autocomplete="off" required>
                                <span class="input-group-text input-group-addon" data-icon="las la-home" role="iconpicker"></span>
                            </div>
                        </div> -->
                        <div class="form-group">
                            <label>@lang('Duration')</label>
                            <div class="input-group">
                                <input class="form-control" name="duration" type="number" required>
                                <span class="input-group-text">@lang('Days')</span>
                            </div>
                        </div>
                        @if (gs('device_limit'))
                            <div class="form-group">
                                <label>@lang('Device Limit')</label>
                                <div class="input-group">
                                    <input class="form-control" name="device_limit" type="number" required>
                                    <span class="input-group-text">@lang('Qty')</span>
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label>@lang('No Of Movies')</label>
                            <div class="input-group">
                                <input class="form-control" name="no_of_movies" type="number" required>
                                <span class="input-group-text">@lang('Qty')</span>
                            </div>
                        </div>

                        <div class="form-group mt-2">
                            <label for="inputName">@lang('Show Ads')</label>
                            <input name="show_ads" data-width="100%" data-height="40px" data-offstyle="-danger" data-onstyle="-success" data-bs-toggle="toggle" data-on="@lang('YES')" data-off="@lang('NO')" type="checkbox">
                        </div>
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn--sm btn-outline--primary addBtn"><i class="la la-plus"></i>@lang('Add New')</button>
@endpush

@push('style-lib')
    <link href="{{ asset('assets/admin/css/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/fontawesome-iconpicker.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"
            let modal = $('#planModal');
            $('.addBtn').on('click', function() {
                $('.modal-title').text(`@lang('Add New Offer')`);
                modal.find('form').attr('action', `{{ route('admin.offer.store') }}`);
                modal.find('input[name=show_ads]').bootstrapToggle('off');
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                $('.modal-title').text(`@lang('Update Offer')`);
                var plan = $(this).data('plan');
                modal.find('form').attr('action', `{{ route('admin.offer.store', '') }}/${plan.id}`);
                modal.find('[name=name]').val(plan.name);
                modal.find('[name=price]').val(Math.abs(plan.pricing));
                modal.find('[name=duration]').val(plan.duration);
                modal.find('[name=icon]').val(plan.icon);
                modal.find('[name=app_code]').val(plan.app_code);
                modal.find('[name=no_of_movies]').val(plan.no_of_movies);
                modal.find('[name=device_limit]').val(plan.device_limit);
                if (plan.show_ads == 1) {
                    modal.find('input[name=show_ads]').bootstrapToggle('on');
                } else {
                    modal.find('input[name=show_ads]').bootstrapToggle('off');
                }
                modal.modal('show');
            });

            modal.on('hidden.bs.modal', function() {
                $('#planModal form')[0].reset();
            });

            $('.iconPicker').iconpicker().on('iconpickerSelected', function(e) {
                $(this).closest('.form-group').find('.iconpicker-input').val(`<i class="${e.iconpickerValue}"></i>`);
            });
        })(jQuery);
    </script>
@endpush
