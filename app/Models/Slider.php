<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model {
    use GlobalStatus;
    public function item() {
        return $this->belongsTo(Item::class);
    }
}
