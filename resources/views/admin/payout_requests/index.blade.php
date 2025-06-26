@extends('admin.layouts.app')

@section('panel')
    <div class="card">
        <div class="card-header"><h5>Payout Requests</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>Vendor</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Requested At</th>
                            <th>Approved At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payoutRequests as $request)
                            <tr>
                                <td>{{ $request->vendor->name }}</td>
                                <td>${{ $request->total_amount }}</td>
                                <td>{{ ucfirst($request->status) }}</td>
                                <td>{{ $request->created_at }}</td>
                                <td>{{ $request->approved_at ?? '-' }}</td>
                                <td>
                                  @if($request->status == 'pending')
                                        <form action="{{ url('admin/payout/payout-requests/'.$request->id.'/approve') }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                        </form>
                                        <form action="{{ url('admin/payout/payout-requests/'.$request->id.'/reject') }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $payoutRequests->links() }}
        </div>
    </div>
@endsection
