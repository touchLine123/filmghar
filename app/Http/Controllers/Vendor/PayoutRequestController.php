<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PayoutRequest;

class PayoutRequestController extends Controller
{
    public function index() 
    {   
        $pageTitle = "Payout Items";
        $payoutRequests = PayoutRequest::where('vendor_id', Auth::id())
                        ->latest()
                        ->paginate(15);

        return view('vendor.payout.index', compact('payoutRequests', 'pageTitle'));
    }

    public function create()
    {
        return view('vendor.payout.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'total_amount' => 'required|numeric|min:1',
        ]);

        PayoutRequest::create([
            'vendor_id' => Auth::id(),
            'total_amount' => $request->total_amount,
            'status' => 'pending',
        ]);

        return redirect()->route('vendor.payout.index')->with('success', 'Payout request sent successfully.');
    }
}
