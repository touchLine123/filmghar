<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyMember extends Model {
    use HasFactory;

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function watchParty() {
        return $this->belongsTo(WatchParty::class, 'watch_party_id');
    }

    public function scopeAccepted($query) {
        return $query->where('status', Status::WATCH_PARTY_REQUEST_ACCEPTED);
    }
    public function scopeRejected($query) {
        return $query->where('status', Status::WATCH_PARTY_REQUEST_REJECTED);
    }
    public function scopePending($query) {
        return $query->where('status', Status::WATCH_PARTY_REQUEST_PENDING);
    }
}
