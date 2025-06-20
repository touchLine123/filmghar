<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferMovie extends Model
{
    use HasFactory;
    protected $table = "offer_movies";
    protected $fillable = [
        'offer_id',
        'movie_id',
        'addedBy',
        'status',
    ];

    // Relationships
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function movie()
    {
        return $this->belongsTo(Item::class, 'movie_id'); // Assuming 'Item' is your movie model
    }
}
