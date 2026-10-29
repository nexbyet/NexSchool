<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultPublication extends Model
{
    protected $fillable = ['academic_year_id', 'standard_id', 'school_class_id', 'semester', 'published_at'];

    protected $casts = ['published_at' => 'datetime'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public static function isPublished($academicYearId, $standardId, $schoolClassId, $semester): bool
    {
        return static::where('academic_year_id', $academicYearId)
            ->where('standard_id', $standardId)
            ->where('school_class_id', $schoolClassId)
            ->where('semester', $semester)
            ->exists();
    }
}