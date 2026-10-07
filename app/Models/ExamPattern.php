<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamPattern extends Model
{
    protected $fillable = [
        'academic_year_id', 'name', 'description', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function heads()
    {
        return $this->hasMany(ExamPatternHead::class)->orderBy('semester')->orderBy('sort_order');
    }

    public function applies()
    {
        return $this->hasMany(ExamPatternApply::class);
    }

    public function semTotal($semester, $field = 'total_marks')
    {
        return (float) $this->heads()->where('semester', $semester)->sum($field);
    }
}
