<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorCommission extends Model
{
    protected $fillable = ['vendor_id', 'item_id', 'purchase_id', 'commission_amount', 'status', 'payout_request_id'];

    public function vendor() { return $this->belongsTo(User::class, 'vendor_id'); }
    public function item() { return $this->belongsTo(Item::class); }
    public function payoutRequest() { return $this->belongsTo(PayoutRequest::class); }
}

