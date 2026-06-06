<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $fillable = [
        'title_gu', 'title_en', 'slug', 'content_gu', 'content_en',
        'meta_title', 'meta_description', 'meta_keywords', 'status',
    ];

    protected static function booted()
    {
        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title_en ?: $page->title_gu);
            }
        });
    }
}
