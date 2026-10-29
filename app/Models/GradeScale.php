<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

    // Slabs ek j var load kariye — report ma darek student × subject mate
    // fari fari query na thay.
    private function slabCollection(): Collection
    {
        return $this->relationLoaded('slabs') ? $this->slabs : $this->slabs()->get();
    }

    // Integer-style scale = badha slab bounds purna ank ma (e.g. 0–34, 35–49).
    // Avi scale ma percentage nearest integer par round thay (34.5 → 35).
    public function isIntegerStyle(): bool
    {
        return $this->isIntegerSlabs($this->slabCollection());
    }

    private function isIntegerSlabs(Collection $slabs): bool
    {
        if ($slabs->isEmpty()) {
            return false;
        }

        return $slabs->every(function (GradeSlab $slab) {
            return (float) $slab->min_percent == (int) $slab->min_percent
                && (float) $slab->max_percent == (int) $slab->max_percent;
        });
    }

    // percent (0-100) -> slab lookup. Result/report phase aahi vaparshe.
    // percent = melvel_marks / total_marks * 100
    public function gradeFor($percent)
    {
        $percent = max(0, min(100, (float) $percent));
        $slabs = $this->slabCollection();

        // Integer-style slabs (0–34, 35–49...) ma 34.5% ne 35% ganaviye,
        // nahi to E (fail) ma fall thay — client school ni rule mujab.
        if ($this->isIntegerSlabs($slabs)) {
            $percent = round($percent);
        }

        $exact = $slabs->first(function (GradeSlab $slab) use ($percent) {
            return (float) $slab->min_percent <= $percent
                && (float) $slab->max_percent >= $percent;
        });
        if ($exact) {
            return $exact;
        }

        // Gap (e.g. 34 → 35) ma najik no niche no slab
        return $slabs
            ->filter(fn (GradeSlab $slab) => (float) $slab->min_percent <= $percent)
            ->sortByDesc('min_percent')
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
