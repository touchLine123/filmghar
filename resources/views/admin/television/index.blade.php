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
                                    <th>@lang('Title')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($televisions as $television)
                                    <tr>
                                        <td>{{ $televisions->firstItem() + $loop->index }}</td>
                                        <td>{{ __($television->title) }}</td>
                                        <td>
                                            @php
                                                echo $television->statusBadge;
                                            @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn--sm btn-outline--primary editBtn" data-television="{{ $television }}" data-image="{{ getImage(getFilePath('television') . '/' . $television->image, getFileSize('television')) }}"><i class="la la-pencil"></i>@lang('Edit')</button>
                                                @if ($television->status == Status::ENABLE)
                                                    <button class="btn btn--sm btn-outline--warning confirmationBtn" data-question="@lang('Are you sure disbale this television')?" data-action="{{ route('admin.television.status', $television->id) }}"><i class="la la-eye-slash"></i>@lang('Disable')</button>
                                                @else
                                                    <button class="btn btn--sm btn-outline--success confirmationBtn" data-question="@lang('Are you sure enable this television')?" data-action="{{ route('admin.television.status', $television->id) }}"><i class="la la-eye"></i>@lang('Enable')</button>
                                                @endif
                                                <button class="btn btn--sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to delete this television?')" data-action="{{ route('admin.television.delete', $television->id) }}"><i class="la la-trash"></i>@lang('Delete')</button>
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
                @if ($televisions->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($televisions) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    <!-- Plan Modal -->
    <div class="modal fade" id="televisionModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Thumbnail Image')</label>
                                    <x-image-uploader image="{{ getImage('/', getFileSize('television')) }}" class="w-100" type="ads" size="{{ getFileSize('television') }}" :required=false />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Television Title')</label>
                                    <input class="form-control" name="title" type="text" required>
                                </div>
                                <div class="form-group">
                                    <label>@lang('URL')</label>
                                    <input class="form-control" name="url" type="text" required>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Description')</label>
                                    <textarea class="form-control" id="" name="description" rows="5" required></textarea>
                                </div>
                            </div>
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
    <button class="btn btn--sm btn-outline--primary addBtn"><i class="la la-plus"></i>@lang('Add New')</button>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"
            var modal = $('#televisionModal');
            var defautlImage = `{{ getImage(getFilePath('television'), getFileSize('television')) }}`;

            $('.addBtn').on('click', function() {
                $('.modal-title').text(`@lang('Add New Television')`);
                modal.find('form').attr('action', `{{ route('admin.television.store') }}`);
                modal.find('.image-upload-preview').attr('style', `background-image: url(${defautlImage})`);
                modal.find('.statusGroup').hide();
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                var television = $(this).data('television');
                $('.modal-title').text(`@lang('Update Television')`);
                modal.find('input[name=title]').val(television.title);
                modal.find('input[name=url]').val(television.url);
                modal.find('[name=description]').val(television.description);
                modal.find('.image-upload-preview').attr('style', `background-image: url(${$(this).data('image')})`);
                modal.find('form').attr('action', `{{ route('admin.television.store', '') }}/${television.id}`);
                modal.find('.statusGroup').show();
                modal.modal('show');
            });

            modal.on('hidden.bs.modal', function() {
                modal.find('form')[0].reset();
            });



        })(jQuery);
    </script>
@endpush
