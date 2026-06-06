<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityPlan extends Model
{
    protected $fillable = ['academic_year_id', 'sort_order', 'activity_name', 'date', 'remarks'];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
