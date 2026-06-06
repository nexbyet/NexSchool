<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicHoliday extends Model
{
    protected $fillable = ['academic_year_id', 'name', 'type', 'date'];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
