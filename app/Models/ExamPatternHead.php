<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamPatternHead extends Model
{
    protected $fillable = [
        'exam_pattern_id', 'semester', 'name', 'total_marks', 'converted_marks', 'sort_order',
    ];

    public function pattern()
    {
        return $this->belongsTo(ExamPattern::class, 'exam_pattern_id');
    }
}
