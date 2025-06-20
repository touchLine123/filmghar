<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoviePlans extends Model
{
    use HasFactory;
    protected $table = "plan_movie";
    protected $fillable = [
        'plan_id',
        'item_id',
        'addedBy',
        'status',
    ];

    // Relationships
    public function Plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function movie()
    {
        return $this->belongsTo(Item::class, 'item_id'); // Assuming 'Item' is your movie model
    }
}
