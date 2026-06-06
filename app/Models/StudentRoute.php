<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentRoute extends Model
{
    protected $table = 'student_route';

    protected $fillable = [
        'student_id', 'route_id', 'stop_id',
        'pickup', 'drop', 'is_active',
    ];

    protected $casts = [
        'pickup' => 'boolean',
        'drop' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function stop()
    {
        return $this->belongsTo(RouteStop::class, 'stop_id');
    }
}
