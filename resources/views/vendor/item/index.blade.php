@extends('vendor.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card  ">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Subcategory')</th>
                                    <th>@lang('Item Type')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr>
                                        <td>{{ $item->title }}</td>
                                        <td>{{ $item->category->name }}</td>
                                        <td>{{ @$item->sub_category->name ?? 'N/A' }}</td>
                                        <td>
                                            @if ($item->item_type == 1 && $item->is_trailer != 1)
                                                <span class="badge badge--success">@lang('Single Item')</span>
                                            @elseif($item->item_type == 2 && $item->is_trailer != 1)
                                                <span class="badge badge--primary">@lang('Episode Item')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Trailer')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->status == 1)
                                                <span class="badge badge--success">@lang('Active')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Deactive')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a class="btn btn--sm btn-outline--primary" href="{{ route('vendor.item.edit', $item->id) }}">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </a>

                                                <button class="btn btn--sm btn-outline--info" data-bs-toggle="dropdown" type="button" aria-expanded="false"><i class="las la-ellipsis-v"></i>@lang('More')</button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item threshold" href="{{ route('watch', $item->slug) }}" target="_blank"> <i class="las la-eye"></i> @lang('Preview') </a>

                                                    @if ($item->item_type == 2)
                                                        <a class="dropdown-item threshold" href="{{ route('vendor.item.episodes', $item->id) }}">
                                                            <i class="las la-list"></i> @lang('Episodes')
                                                        </a>
                                                    @else
                                                        @if ($item->video)
                                                            <a class="dropdown-item threshold" href="{{ route('vendor.item.updateVideo', $item->id) }}">
                                                                <i class="las la-cloud-upload-alt"></i> @lang('Update Video')
                                                            </a>
                                                            <a class="dropdown-item threshold" href="{{ route('vendor.item.ads.duration', $item->id) }}">
                                                                <i class="lab la-buysellads"></i> @lang('Update Ads')
                                                            </a>
                                                            <a class="dropdown-item threshold" href="{{ route('vendor.item.subtitle.list', [$item->id, '']) }}">
                                                                <i class="las la-file-audio"></i> @lang('Subtitles')
                                                            </a>
                                                            <a class="dropdown-item threshold" href="{{ route('vendor.item.report', [$item->id, '']) }}">
                                                                <i class="las la-chart-area"></i> @lang('Report')
                                                            </a>
                                                        @else
                                                            <a class="dropdown-item threshold" href="{{ route('vendor.item.uploadVideo', $item->id) }}">
                                                                <i class="las la-cloud-upload-alt"></i> @lang('Upload Video')
                                                            </a>
                                                        @endif
                                                    @endif
                                                    <a class="dropdown-item threshold confirmationBtn" data-action="{{ route('vendor.item.send.notification', $item->id) }}" data-question="@lang('Are you sure to send notifications to all users?')" href="javascript:void(0)"> <i class="las la-bell"></i> @lang('Send Notification') </a>
                                                </div>

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
                @if ($items->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($items) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />

@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by Name" />
    <a class="btn btn-outline--primary" href="{{ route('vendor.item.create') }}"><i class="la la-plus"></i>@lang('Add New')</a>
@endpush

@push('style')
    <style>
        .table-responsive {
            overflow-x: unset !important;
        }
    </style>
@endpush
