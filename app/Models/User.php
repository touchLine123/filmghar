<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\UserNotify;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, UserNotify;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'ver_code', 'balance', 'kyc_data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address'           => 'object',
        'kyc_data'          => 'object',
        'ver_code_send_at'  => 'datetime',
    ];

    protected $fillable = [
        'name','email','password', 'wallet_amount',"firstname","lastname","username","email","dial_code","country_code","mobile","password","country_name","city","state","zip","address","image","status","exp","ev","sv","profile_complete","ver_code","ver_code_send_at","tsc","ban_reason","remember_token","provider","provider_id","login_by"
    ];
    
    public function loginLogs() {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class)->orderBy('id', 'desc');
    }

    public function deposits() {
        return $this->hasMany(Deposit::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function tickets() {
        return $this->hasMany(SupportTicket::class);
    }

    public function plan() {
        return $this->belongsTo(Plan::class);
    }

    public function fullname(): Attribute {
        return new Attribute(
            get: fn() => $this->firstname . ' ' . $this->lastname,
        );
    }

    public function mobileNumber(): Attribute {
        return new Attribute(
            get: fn() => $this->dial_code . $this->mobile,
        );
    }

    // SCOPES
    public function scopeActive($query) {
        return $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED);
    }

    public function scopeBanned($query) {
        return $query->where('status', Status::USER_BAN);
    }

    public function scopeEmailUnverified($query) {
        return $query->where('ev', Status::UNVERIFIED);
    }

    public function scopeMobileUnverified($query) {
        return $query->where('sv', Status::UNVERIFIED);
    }

    public function scopeKycUnverified($query) {
        return $query->where('kv', Status::KYC_UNVERIFIED);
    }

    public function scopeKycPending($query) {
        return $query->where('kv', Status::KYC_PENDING);
    }

    public function scopeEmailVerified($query) {
        return $query->where('ev', Status::VERIFIED);
    }

    public function scopeMobileVerified($query) {
        return $query->where('sv', Status::VERIFIED);
    }

    public function scopeWithBalance($query) {
        return $query->where('balance', '>', 0);
    }

    public function deviceTokens() {
        return $this->hasMany(DeviceToken::class);
    }

    public function scopeAdsFree($query) {
        return $query->where('ads_free', Status::ENABLE)->where('ads_free_expired', '>', now());
    }

}
