<?php

// NexSchool - AcademicYear Model
// શૈક્ષણિક વર્ષ: year (2025-26), start_date, end_date, is_active

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = [
        'year', 'start_date', 'end_date', 'is_active', 'session_start_date',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
            'session_start_date' => 'date',
        ];
    }

    // Get currently active academic year
    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }
}
