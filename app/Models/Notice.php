<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $table = 'notice_board';

    protected $fillable = [
        'title_gu', 'title_en', 'content_gu', 'content_en',
        'file_path', 'is_circular', 'date', 'status',
    ];

    protected $casts = [
        'date' => 'date',
        'is_circular' => 'boolean',
        'status' => 'boolean',
    ];
}
