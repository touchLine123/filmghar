<?php

namespace App\Models;

use App\Traits\ApiQuery;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model {
    use GlobalStatus, ApiQuery;
    protected $guarded = ['id'];

    protected $casts = ['expired_date' => 'datetime'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function plan() {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
    public function item() {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function getRentedDurationAttribute() {
        $duration = '';
        if ($this->expired_date > now()) {
            $duration = diffForHumans($this->expired_date);
        } else {
            $duration = 'Expired';
        }
        return $duration;
    }

}
