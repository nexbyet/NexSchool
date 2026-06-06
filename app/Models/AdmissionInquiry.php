<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionInquiry extends Model
{
    protected $fillable = [
        'first_name_gu', 'father_name_gu', 'surname_gu',
        'first_name_en', 'father_name_en', 'surname_en',
        'gender', 'date_of_birth',
        'standard_applied_for', 'father_name', 'mother_name', 'phone', 'email',
        'address', 'previous_school', 'gr_number', 'status',
        'academic_year_id', 'admin_notes', 'approved_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'approved_at' => 'datetime',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function getFullNameGuAttribute()
    {
        return trim(implode(' ', array_filter([$this->first_name_gu, $this->father_name_gu, $this->surname_gu])));
    }

    public function getFullNameEnAttribute()
    {
        return trim(implode(' ', array_filter([$this->first_name_en, $this->father_name_en, $this->surname_en])));
    }
}
