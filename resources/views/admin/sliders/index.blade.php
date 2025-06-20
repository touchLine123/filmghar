@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('S.N')</th>
                                    <th>@lang('Item')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sliders as $slider)
                                    <tr>
                                        <td>{{ $sliders->firstItem() + $loop->index }}</td>
                                        <td>
                                            {{ __(@$slider->item->title) }}
                                        </td>
                                        <td>
                                            @php
                                                echo $slider->statusBadge;
                                            @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn--sm btn-outline--primary editBtn" data-id="{{ $slider->id }}" data-image="{{ getImage(getFilePath('slider') . '/' . $slider->image, getFileSize('slider')) }}" data-status="{{ $slider->status }}" data-caption="{{ $slider->caption_show }}">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>

                                                @if ($slider->status == Status::ENABLE)
                                                    <button class="btn btn--sm btn-outline--warning confirmationBtn" data-question="@lang('Are you sure to disable this slider?')" data-action="{{ route('admin.sliders.status', $slider->id) }}"><i class="la la-eye-slash"></i>@lang('Disable')</button>
                                                @else
                                                    <button class="btn btn--sm btn-outline--success confirmationBtn" data-question="@lang('Are you sure to enable this slider?')" data-action="{{ route('admin.sliders.status', $slider->id) }}"><i class="la la-eye"></i>@lang('Enable')</button>
                                                @endif

                                                <button class="btn btn--sm btn-outline--danger confirmationBtn" data-id="{{ $slider->id }}" data-action="{{ route('admin.sliders.remove', $slider->id) }}" data-question="@lang('Are you sure to delete this slider?')">
                                                    <i class="las la-trash text--shadow"></i>@lang('Delete')
                                                </button>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">@lang('Slider Not Found')</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($sliders->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($sliders) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    <!-- Slider Modal -->
    <div class="modal fade" id="sliderModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add Slider')</h5>
                    <button class="close" data-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.sliders.add') }}" method="post" enctype="multipart/form-data" novalidate>
                    <div class="modal-body">
                        @csrf
                        <div class="form-group itemGroup">
                            <label>@lang('Select Item')</label>
                            <select class="form-control item-list select2" name="item" required>
                                <option value="">-- @lang('Select One') --</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        @php
                            if (gs('active_template') == 'basic') {
                                $slider = 'slider';
                            } else {
                                $slider = 'labflixSlider';
                            }
                        @endphp
                        <div class="form-group">
                            <label>@lang('Thumbnail Image')</label>
                            <x-image-uploader image="{{ getImage('/', getFileSize($slider)) }}" class="w-100" type="{{ $slider }}" :required=false />
                        </div>
                        @if (gs('active_template') == 'labflix')
                            <div class="form-group caption">
                                <label>@lang('Caption Status')</label>
                                <input name="caption_show" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" data-width="100%" type="checkbox">
                            </div>
                        @endif
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
    <button class="btn btn--sm btn-outline--primary addBtn"><i class="las la-plus"></i> @lang('Add New')</button>
@endpush
@push('script')
    <script>
        var modal = $('#sliderModal');
        var defautlImage = `{{ getImage(getFilePath('slider'), getFileSize('slider')) }}`;
        $('.addBtn').on('click', function() {
            modal.find('.modal-title').text(`@lang('Add Slider')`);
            modal.find('form').attr('action', `{{ route('admin.sliders.add') }}`);
            modal.find('.itemGroup').show();
            modal.find('.statusGroup').hide();
            modal.find('input[name=caption_show]').bootstrapToggle('off');
            modal.modal('show');
            $(".item-list").select2({
                ajax: {
                    url: "{{ route('admin.item.list') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 1000,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page,
                            rows: 5,
                        };
                    },
                    processResults: function(response, params) {
                        params.page = params.page || 1;
                        return {
                            results: response,
                            pagination: {
                                more: params.page < response.length
                            }
                        };
                    },
                    cache: false
                },

                dropdownParent: modal
            });
        });

        $('.editBtn').on('click', function() {
            modal.find('.modal-title').text(`@lang('Update Slider')`);
            modal.find('.itemGroup').hide();
            modal.find('.image-upload-preview').attr('style', `background-image: url(${$(this).data('image')})`);
            modal.find('form').attr('action', `{{ route('admin.sliders.update', '') }}/${$(this).data('id')}`);

            modal.find('.statusGroup').show();
            var caption = $(this).data('caption');
            var status = $(this).data('status');
            if (caption == 1) {
                modal.find('input[name=caption_show]').bootstrapToggle('on');
            } else {
                modal.find('input[name=caption_show]').bootstrapToggle('off');
            }
            modal.modal('show');
        });

        modal.on('hidden.bs.modal', function() {
            modal.find('.image-upload-preview').attr('style', `background-image: url(${defautlImage})`);
            $('#sliderModal form')[0].reset();
        });
    </script>
@endpush
