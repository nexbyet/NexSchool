<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['name_gu', 'name_en', 'description_gu', 'description_en', 'sort_order', 'status'];

    protected $casts = ['status' => 'boolean'];

    public function images()
    {
        return $this->hasMany(GalleryImage::class)->orderBy('sort_order');
    }
}
