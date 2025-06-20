<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\ApiQuery;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Item extends Model {
    use GlobalStatus, ApiQuery;

    protected $casts = [
        'team'      => 'object',
        'image'     => 'object',
        'thumbnail' => 'object',
    ];

    protected $appends = ['category_name', 'sub_category_name'];
    public function video() {
        return $this->hasOne(Video::class);
    }
    public function subtitles() {
        return $this->hasMany(Subtitle::class);
    }
    public function videoReport() {
        return $this->hasMany(VideoReport::class);
    }

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function sub_category() {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

     // Custom Accessors
    public function getCategoryNameAttribute() {
        return $this->category ? $this->category->name : null;
    }

    public function getSubCategoryNameAttribute() {
        return $this->sub_category ? $this->sub_category->name : null;
    }
    public function episodes() {
        return $this->hasMany(Episode::class);
    }

    public function wishlists() {
        return $this->hasMany(Wishlist::class);
    }

    public function getVersionNameAttribute() {
        $versionName = '';
        if ($this->item_type == Status::EPISODE_ITEM) {
            $versionName = 'Episode';
        } else if ($this->item_type == Status::SINGLE_ITEM && $this->version == Status::FREE_VERSION && $this->is_trailer == Status::NO) {
            $versionName = 'Free';
        } else if ($this->item_type == Status::SINGLE_ITEM && $this->version == Status::PAID_VERSION && $this->is_trailer == Status::NO) {
            $versionName = 'Paid';
        } else if ($this->item_type == Status::SINGLE_ITEM && $this->version == Status::RENT_VERSION && $this->is_trailer == Status::NO) {
            $versionName = 'Rent';
        } else {
            $versionName = 'Trailer';
        }
        return $versionName;
    }

    public function scopeHasVideo($query) {
        return $query->where('status', Status::ENABLE)->where(function ($q) {
            $q->orWhereHas('video')->orWhereHas('episodes', function ($video) {
                $video->where('status', Status::ENABLE)->whereHas('video');
            });
        });
    }

    public function scopeFree($query) {
        return $query->where('status', Status::ENABLE)->where('item_type', '!=', 3)->where(function ($free) {
            $free->orWhere('version', Status::FREE_VERSION)->orWhereHas('episodes', function ($q) {
                $q->where('version', Status::FREE_VERSION);
            });
        });
    }
    public function scopeActive($query) {
        return $query->where(function ($q) {
            $q->orWhere('status', Status::ENABLE)->orWhereHas('episodes', function ($episodes) {
                $episodes->where('status', Status::ENABLE);
            });
        });
    }

    public function scopeSearch($query, $search) {
        return $query->where(function ($q) use ($search) {
            $q->orWhere('title', 'LIKE', "%$search%")
                ->orWhereHas('category', function ($category) use ($search) {
                    $category->where('status', Status::ENABLE)->where('name', 'LIKE', "%$search%");
                })
                ->orWhereHas('sub_category', function ($sub_category) use ($search) {
                    $sub_category->where('status', Status::ENABLE)->where('name', 'LIKE', "%$search%");
                })
                ->orWhereHas('episodes', function ($episodes) use ($search) {
                    $episodes->where('status', Status::ENABLE)->where('title', 'LIKE', "%$search%");
                })
                ->orWhere('preview_text', 'LIKE', "%$search%")
                ->orWhere('description', 'LIKE', "%$search%")
                ->orWhere('team', 'LIKE', "%$search%")
                ->orWhere('tags', 'LIKE', "%$search%");
        });
    }

    public function scopeSingleItems($query) {
        $query->where('item_type', Status::SINGLE_ITEM);

        if (auth()->guard('vendor')->check()) {
            $query->where('addedBy', auth()->guard('vendor')->id());
        }
        return $query;
    }

    public function scopeEpisodeItems($query)
    {
        $query->where('item_type', Status::EPISODE_ITEM);
        if (auth()->guard('vendor')->check()) {
            $query->where('addedBy', auth()->guard('vendor')->id());
        }
        return $query;
    }
    

    public function scopeTrailerItems($query)
    {
        $query->where('is_trailer', Status::TRAILER)
            ->where('item_type', Status::SINGLE_ITEM);

        if (auth()->guard('vendor')->check()) {
            $query->where('addedBy', auth()->guard('vendor')->id());
        }

        return $query;
    }

    public function scopeRentItems($query)
    {
        $query->where('version', Status::RENT_VERSION);

        if (auth()->guard('vendor')->check()) {
            $query->where('addedBy', auth()->guard('vendor')->id());
        }

        return $query;
    }

    public function scopeAllItems($query)
    {
        if (auth()->guard('vendor')->check()) {
            $query->where('addedBy', auth()->guard('vendor')->id());
        }

        return $query;
    }

    

}
