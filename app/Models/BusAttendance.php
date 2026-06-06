<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusAttendance extends Model
{
    protected $fillable = [
        'student_id', 'route_id', 'date',
        'morning_status', 'evening_status', 'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
