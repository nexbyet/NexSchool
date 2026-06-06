<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableEntry extends Model
{
    protected $fillable = [
        'academic_year_id',
        'timetable_slot_id',
        'standard_id',
        'school_class_id',
        'day_of_week',
        'subject_id',
        'teacher_id',
    ];

    public function slot()
    {
        return $this->belongsTo(TimetableSlot::class, 'timetable_slot_id');
    }

    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
