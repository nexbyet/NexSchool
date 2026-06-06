<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableSlot extends Model
{
    protected $fillable = [
        'academic_year_id',
        'name_en',
        'name_gu',
        'start_time',
        'end_time',
        'is_break',
        'sort_order',
        'saturday_start_time',
        'saturday_end_time',
    ];

    protected $casts = [
        'is_break' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
