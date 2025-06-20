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
                                    <th scope="col">@lang('Ad Type')</th>
                                    <th scope="col">@lang('URL')</th>
                                    <th scope="col">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ads as $ad)
                                    <tr>
                                        <td>
                                            @if ($ad->type == 1)
                                                <span>@lang('Link')</span>
                                            @else
                                                <span>@lang('Video')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($ad->content->link)
                                                <a href="{{ $ad->content->link }}" target="_blank">{{ $ad->content->link }}</a>
                                            @else
                                                <a href="{{ $ad->videoAds }}" target="_blank">{{ strLimit($ad->videoAds, 100) }}</a>
                                            @endif
                                        </td>
                                        <td data-action="@lang('Action')">
                                            <div class="button--group">
                                                <button class="btn btn--sm btn-outline--primary editBtn" data-id="{{ $ad->id }}" data-type="{{ $ad->type }}" @if (@$ad->content->link) data-link="{{ $ad->content->link }}" @endif><i class="la la-pencil"></i>@lang('Edit')</button>

                                                <button class="btn btn--sm btn-outline--danger confirmationBtn" data-id="{{ $ad->id }}" data-question="@lang('Are you sure to remove this advertise?')" data-action="{{ route('admin.video.advertise.remove', $ad->id) }}"><i class="la la-trash"></i>@lang('Delete')</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($ads->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($ads) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="advertiseModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Type')</label>
                            <select class="form-control select2" id="type" name="type" required>
                                <option value="1">@lang('Link')</option>
                                <option value="2">@lang('Video')</option>
                            </select>
                        </div>
                        <div class="form-group link d-none">
                            <label>@lang('Link')<span class="text--danger">*</span></label>
                            <input class="form-control" name="link" type="text" placeholder="@lang('Link')">
                        </div>
                        <div class="form-group file d-none">
                            <label>@lang('File')<span class="text--danger">*</span></label>
                            <input class="form-control" name="video" type="file">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn--sm btn-outline--primary addBtn"><i class="la la-plus"></i> @lang('Add New')</button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            var modal = $('#advertiseModal');
            $('.addBtn').on('click', function() {
                modal.find('.modal-title').text(`@lang('Add Video Advertise')`);
                modal.find('form').attr('action', `{{ route('admin.video.advertise.store') }}`);
                var type = modal.find('select[name=type]').val('').change();
                if (type == 1) {
                    $('.link').removeClass('d-none');
                    $('.file').addClass('d-none');
                } else {
                    $('.link').addClass('d-none');
                    $('.file').removeClass('d-none');
                }
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                var data = $(this).data();
                modal.find('.modal-title').text(`@lang('Update Video Advertise')`);
                modal.find('form').attr('action', `{{ route('admin.video.advertise.store', '') }}/${data.id}`);
                modal.find('select[name=type]').val(data.type).change();
                if (data.type == 1) {
                    $('.link').removeClass('d-none');
                    $('.file').addClass('d-none');
                } else {
                    $('.link').addClass('d-none');
                    $('.file').removeClass('d-none');
                }
                modal.modal('show');
            });



            $('#type').on('change', function() {
                if ($(this).val() == 1) {
                    $('.link').removeClass('d-none');
                    $('.file').addClass('d-none');
                } else {
                    $('.link').addClass('d-none');
                    $('.file').removeClass('d-none');
                }
            }).change();

            function changeAdType(value) {

            }

            modal.on('hidden.bs.modal', function() {
                $('#advertiseModal form')[0].reset();
            });

        })(jQuery);
    </script>
@endpush
