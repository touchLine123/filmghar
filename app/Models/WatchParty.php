<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\ApiQuery;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class WatchParty extends Model {
    use GlobalStatus, ApiQuery;

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function item() {
        return $this->belongsTo(Item::class);
    }
    public function episode() {
        return $this->belongsTo(Episode::class);
    }

    public function partyMember() {
        return $this->hasMany(PartyMember::class, 'watch_party_id');
    }

    public function statusBadge(): Attribute {
        return new Attribute(
            get: fn() => $this->badgeData(),
        );
    }

    public function badgeData() {
        $html = '';
        if ($this->status == Status::ENABLE) {
            $html = '<span class="badge badge--success">' . trans('Running') . '</span>';
        } else {
            $html = '<span class="badge badge--danger">' . trans('Canceled') . '</span>';
        }
        return $html;
    }

}
