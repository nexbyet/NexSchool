<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassTeacher extends Model
{
    protected $fillable = ['school_class_id', 'teacher_id', 'academic_year_id'];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
