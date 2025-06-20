@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Language')</th>
                                    <th>@lang('Code')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subtitles as $subtitle)
                                    <tr>
                                        <td>{{ $subtitles->firstItem() + $loop->index }}</td>
                                        <td>{{ __($subtitle->language) }}</td>
                                        <td>{{ __($subtitle->code) }}</td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn--sm btn-outline--primary editBtn" data-subtitle="{{ $subtitle }}">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>
                                                <button class="btn btn--sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to remove this subtitle?')" data-action="{{ route('admin.item.subtitle.delete', $subtitle->id) }}"><i class="la la-trash"></i>@lang('Delete')</button>
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
                @if ($subtitles->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($subtitles) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="subtitleModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Language')</label>
                            <input class="form-control" name="language" type="text" placeholder="@lang('e.g:English')">
                        </div>
                        <div class="form-group">
                            <label>@lang('Short Code')</label>
                            <input class="form-control" name="code" type="text" placeholder="@lang('e.g:en')">
                        </div>
                        <div class="form-group">
                            <label>@lang('File')<code>(@lang('uploade only .vtt file'))</code></label>
                            <input class="form-control" name="file" type="file" accept=".vtt">
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
    <x-back route="{{ route('admin.item.index') }}" />
    <button class="btn btn--sm btn-outline--primary addBtn"><i class="la la-plus"></i> @lang('Add New')</button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            let modal = $("#subtitleModal");
            $('.addBtn').on('click', function(e) {
                modal.find('.modal-title').text('Add New Subtitle');
                modal.find('form').attr('action', `{{ route('admin.item.subtitle.store', [$itemId, $episodeId, $videoId]) }}`);
                modal.modal('show');
            });

            $('.editBtn').on('click', function(e) {
                var subtitle = $(this).data('subtitle')
                modal.find('.modal-title').text('Update Subtitle');
                modal.find('form').attr('action', `{{ route('admin.item.subtitle.store', [$itemId, $episodeId, $videoId]) }}/${subtitle.id}`);
                modal.find('[name=language]').val(subtitle.language);
                modal.find('[name=code]').val(subtitle.code);
                modal.modal('show')
            });

            modal.on('hidden.bs.modal', function() {
                modal.find('form')[0].reset();
            });
        })(jQuery)
    </script>
@endpush
