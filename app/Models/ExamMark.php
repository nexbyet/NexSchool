<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamMark extends Model
{
    protected $fillable = [
        'exam_pattern_id', 'academic_year_id', 'standard_id', 'school_class_id',
        'subject_id', 'semester', 'head_id', 'student_id', 'marks_obtained',
    ];

    public function pattern()
    {
        return $this->belongsTo(ExamPattern::class, 'exam_pattern_id');
    }

    public function head()
    {
        return $this->belongsTo(ExamPatternHead::class, 'head_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
