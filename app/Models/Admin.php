<?php

namespace App\Models;
use App\Traits\UserNotify;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use UserNotify;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function scopeActive($query) {
        return $query->where('status', 1)->where('user_role', 2);
    }

    public function scopeAllVendors($query) {
        return $query->where('user_role', 2);
    }
}
