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
                                    <th>@lang('User')</th>
                                    <th>@lang('Code')</th>
                                    <th>@lang('Item')</th>
                                    <th>@lang('Members')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Created At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($watchParties as $party)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ @$party->user->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('vendor.users.detail', @$party->user->id) }}"><span>@</span>{{ @$party->user->username }}</a>
                                            </span>
                                        </td>
                                        <td>{{ $party->party_code }}</td>
                                        <td>
                                            @if ($party->episode)
                                                <span>{{ __(@$party->episode->title) }}</span>
                                            @else
                                                <span>{{ __(@$party->item->title) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $party->partyMember->count() ?? 0 }}</td>
                                        <td>
                                            @php
                                                echo $party->statusBadge;
                                            @endphp
                                        </td>
                                        <td>{{ showDateTime($party->created_at) }}</td>
                                        <td>
                                            <a class="btn btn-outline--primary btn--sm" href="{{ route('vendor.watch.party.joined', $party->id) }}"><i class="las la-users"></i> @lang('Joined')</a>
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
                @if ($watchParties->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($watchParties) }}
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Username / Code" />
@endpush
