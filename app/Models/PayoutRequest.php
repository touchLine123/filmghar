<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutRequest extends Model
{
    protected $fillable = ['vendor_id', 'total_amount', 'status', 'approved_at'];

    public function vendor() { return $this->belongsTo(User::class, 'vendor_id'); }
    public function commissions() { return $this->hasMany(VendorCommission::class); }
}

