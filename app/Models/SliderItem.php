<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SliderItem extends Model
{
    protected $fillable = [
        'title_gu', 'title_en', 'subtitle_gu', 'subtitle_en',
        'image', 'link_url', 'sort_order', 'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
