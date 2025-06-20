<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Advertise extends Model {
    protected $casts = [
        'content' => 'object',
    ];

  
}
