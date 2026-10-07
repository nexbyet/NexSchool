<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamPatternApply extends Model
{
    protected $fillable = ['exam_pattern_id', 'standard_id', 'school_class_id', 'subject_id'];

    public function pattern()
    {
        return $this->belongsTo(ExamPattern::class, 'exam_pattern_id');
    }

    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
