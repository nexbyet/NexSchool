<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $fillable = ['gallery_id', 'image', 'caption_gu', 'caption_en', 'sort_order'];

    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }
}
