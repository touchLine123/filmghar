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
                                    <th>ID</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Requested At</th>
                                    <th>Approved At</th>
                                </tr>
                            </thead>
                            <tbody>
                                 @forelse($payoutRequests as $request)
                                    <tr>
                                        <td>{{ $request->id }}</td>
                                        <td>${{ number_format($request->total_amount, 2) }}</td>
                                        <td>{{ ucfirst($request->status) }}</td>
                                        <td>{{ $request->created_at->format('d M Y H:i') }}</td>
                                        <td>{{ $request->approved_at ? $request->approved_at->format('d M Y H:i') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">No payout requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($payoutRequests->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($payoutRequests) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />

@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by Name" />
@endpush

@push('style')
    <style>
        .table-responsive {
            overflow-x: unset !important;
        }
    </style>
@endpush
