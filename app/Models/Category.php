<?php

namespace App\Models;

use App\Traits\ApiQuery;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    use GlobalStatus, ApiQuery;
    public function subcategories() {
        return $this->hasMany(SubCategory::class);
    }
}
