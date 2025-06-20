<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model {

    use GlobalStatus;

    protected $casts = [
        'thumbnail' => 'object',
    ];

    public function video() {
        return $this->hasOne(Video::class);
    }

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function wishlists() {
        return $this->hasMany(Wishlist::class);
    }

    public function videoReport() {
        return $this->hasMany(VideoReport::class);
    }

    public function getVersionNameAttribute() {
        $versionName = '';
        if ($this->version == Status::FREE_VERSION) {
            $versionName = 'Free';
        } else if ($this->version == Status::PAID_VERSION) {
            $versionName = 'Paid';
        } else {
            $versionName = 'Rent';
        }
        return $versionName;
    }

    public function episodeBadge(): Attribute {
        return new Attribute(
            get: fn() => $this->episodeData(),
        );
    }

    public function episodeData() {
        $html = '';
        if ($this->version == Status::FREE_VERSION) {
            $html = '<span class="badge badge--success">' . trans('Free') . '</span>';
        } else if ($this->version == Status::PAID_VERSION) {
            $html = '<span class="badge badge--primary">' . trans('Paid') . '</span>';
        } else {
            $html = '<span class="badge badge--warning">' . trans('Rent') . '</span>';
        }
        return $html;
    }

    public function scopeHasVideo() {
        return $this->where('status', 1)->whereHas('video');
    }
}
