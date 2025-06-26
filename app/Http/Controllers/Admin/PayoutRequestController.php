<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use App\Models\VendorCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayoutRequestController extends Controller
{

    public function index() 
    {
        $pageTitle = "All Payout";
        $payoutRequests = PayoutRequest::with('vendor')->latest()->paginate(20);
        return view('admin.payout_requests.index', compact('payoutRequests', 'pageTitle'));
    }

    public function approve($id)
    {
        $payout = PayoutRequest::findOrFail($id);

        if ($payout->status !== 'pending') {
            return back()->withErrors(['Invalid request status']);
        }

        DB::transaction(function () use ($payout) {
            $payout->update([
                'status' => 'approved',
                'approved_at' => now()
            ]);

            VendorCommission::where('payout_request_id', $payout->id)
                ->update(['status' => 2]); // 2 = paid
        });

        return back()->with('success', 'Payout request approved successfully.');
    }

    public function reject($id)
    {
        $payout = PayoutRequest::findOrFail($id);

        if ($payout->status !== 'pending') {
            return back()->withErrors(['Invalid request status']);
        }

        DB::transaction(function () use ($payout) {
            VendorCommission::where('payout_request_id', $payout->id)
                ->update([
                    'status' => 0,            // back to unpaid
                    'payout_request_id' => null
                ]);

            $payout->update(['status' => 'rejected']);
        });

        return back()->with('success', 'Payout request rejected successfully.');
    }
}
