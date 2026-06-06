<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectTeacherAssignment extends Model
{
    protected $fillable = ['subject_id', 'teacher_id', 'standard_id', 'class_id', 'academic_year_id'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
