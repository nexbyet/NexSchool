<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusAttendance extends Model
{
    protected $fillable = [
        'student_id', 'bus_only_student_id', 'student_type',
        'route_id', 'date',
        'morning_status', 'evening_status', 'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function busOnlyStudent()
    {
        return $this->belongsTo(BusOnlyStudent::class, 'bus_only_student_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
