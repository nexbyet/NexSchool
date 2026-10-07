<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeScale extends Model
{
    protected $fillable = ['academic_year_id', 'name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function slabs()
    {
        return $this->hasMany(GradeSlab::class)->orderBy('sort_order');
    }

    // percent (0-100) -> slab lookup. Result/report phase aahi vaparshe.
    // percent = melvel_marks / total_marks * 100
    public function gradeFor($percent)
    {
        $percent = max(0, min(100, (float) $percent));
        $exact = $this->slabs()
            ->where('min_percent', '<=', $percent)
            ->where('max_percent', '>=', $percent)
            ->orderBy('sort_order')
            ->first();
        if ($exact) return $exact;
        // integer-style 1% gap ma (e.g. 20.5) — najik no niche no slab
        return $this->slabs()
            ->where('min_percent', '<=', $percent)
            ->orderByDesc('min_percent')
            ->first();
    }

    // varsh no active scale (result mate default)
    public static function activeForYear($academicYearId)
    {
        return static::where('academic_year_id', $academicYearId)
            ->where('is_active', true)
            ->with('slabs')
            ->orderByDesc('id')
            ->first();
    }
}
