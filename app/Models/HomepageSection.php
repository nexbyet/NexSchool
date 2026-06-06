<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    protected $fillable = ['type', 'content', 'sort_order', 'status'];

    protected $casts = [
        'content' => 'array',
        'status' => 'boolean',
    ];
}
