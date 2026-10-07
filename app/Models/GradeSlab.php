<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeSlab extends Model
{
    protected $fillable = [
        'grade_scale_id', 'grade', 'min_percent', 'max_percent', 'is_pass', 'sort_order',
    ];

    protected $casts = ['is_pass' => 'boolean'];

    public function scale()
    {
        return $this->belongsTo(GradeScale::class, 'grade_scale_id');
    }
}
