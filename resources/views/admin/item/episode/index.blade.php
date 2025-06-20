@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card  ">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th scope="col">@lang('Title')</th>
                                    <th scope="col">@lang('Status')</th>
                                    <th scope="col">@lang('Version')</th>
                                    <th scope="col">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($episodes as $episode)
                                    <tr>
                                        <td>{{ __($episode->title) }}</td>
                                        <td>
                                            @php
                                                echo $episode->statusbadge;
                                            @endphp
                                        </td>
                                        <td>
                                            @php
                                                echo $episode->episodeBadge;
                                            @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn--sm btn-outline--primary editBtn" data-title="{{ $episode->title }}" data-version="{{ $episode->version }}" data-image="{{ getImage(getFilePath('episode') . '/' . $episode->image) }}" data-episode_id="{{ $episode->id }}" data-status="{{ $episode->status }}" data-toggle="tooltip" data-original-title="Edit" title="">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>
                                                @if ($episode->video)
                                                    <button class="btn btn--sm btn-outline--info" data-bs-toggle="dropdown" type="button" aria-expanded="false"><i class="las la-ellipsis-v"></i>@lang('More')</button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item threshold" href="{{ route('admin.item.episode.updateVideo', $episode->id) }}">
                                                            <i class="la la-cloud-upload-alt"></i> @lang('Update Video')
                                                        </a>
                                                        <a class="dropdown-item threshold" href="{{ route('admin.item.ads.duration', [$episode->item_id, $episode->id]) }}">
                                                            <i class="lab la-buysellads"></i> @lang('Update Ads')
                                                        </a>
                                                        <a class="dropdown-item threshold" href="{{ route('admin.item.subtitle.list', [$episode->id, $episode->video->id]) }}">
                                                            <i class="las la-file-audio"></i> @lang('Subtitles')
                                                        </a>
                                                        <a class="dropdown-item threshold" href="{{ route('admin.item.report', [$episode->item_id, $episode->id]) }}">
                                                            <i class="las la-chart-area"></i> @lang('Report')
                                                        </a>
                                                    </div>
                                                @else
                                                    <a class="btn btn--sm btn-outline--warning" href="{{ route('admin.item.episode.addVideo', $episode->id) }}">
                                                        <i class="la la-cloud-upload-alt"></i>@lang('Upload Video')
                                                    </a>
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
                @if ($episodes->hasPages())
                    <div class="card-footer py-4">
                        {{ $episodes->links('admin.partials.paginate') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="episodeModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add New Episode')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.item.addEpisode', $item->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Thumbnail Image')</label>
                            <x-image-uploader image="{{ getImage('') }}" class="w-100" type="episode" :required=false size="" />
                        </div>
                        <div class="form-group">
                            <label>@lang('Video Title')</label>
                            <input class="form-control" name="title" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Version')</label>
                            <select class="form-control" name="version" required>
                                @if ($item->version == Status::RENT_VERSION)
                                    <option value="2">@lang('Rent')</option>
                                @else
                                    <option value="0">@lang('Free')</option>
                                    <option value="1">@lang('Paid')</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group statusGroup">
                            <label>@lang('Status')</label>
                            <input name="status" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Active')" data-off="@lang('Inactive')" data-width="100%" type="checkbox">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <button class="btn btn--sm btn-outline--primary addBtn"><i class="las la-plus"></i>@lang('Add New Episode')</button>
@endpush
@push('style')
    <style>
        .table-responsive {
            overflow-x: unset !important;
        }
    </style>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict"
            var modal = $('#episodeModal');
            var defautlImage = `{{ getImage(getFilePath('episode')) }}`;
            $('.addBtn').on('click', function() {
                modal.find('form').attr('action', `{{ route('admin.item.addEpisode', $item->id) }}`);
                modal.find('.statusGroup').hide();
                modal.modal('show');
            });

            $('.editBtn').on('click', function() {
                let data = $(this).data();
                modal.find('input[name=title]').val(data.title);
                modal.find('.image-upload-preview').attr('style', `background-image:url(${data.image})`);
                modal.find('select').val(data.version);
                modal.find('.statusGroup').show();
                if (data.status == 1) {
                    modal.find('input[name=status]').bootstrapToggle('on');
                } else {
                    modal.find('input[name=status]').bootstrapToggle('off');
                }
                modal.find('form').attr('action', `{{ route('admin.item.updateEpisode', '') }}/${data.episode_id}`);
                modal.modal('show');
            });

            modal.on('hidden.bs.modal', function() {
                modal.find('.image-upload-preview').attr('style', `background-image: url(${defautlImage})`);
                $('#episodeModal form')[0].reset();
            });

        })(jQuery);
    </script>
@endpush
