<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'gr_number',
        'admission_standard_id', 'admission_class_id',
        'current_standard_id', 'current_class_id',
        'date_of_admission',
        'student_name_gu', 'student_name_en',
        'father_name_gu', 'father_name_en',
        'surname_gu', 'surname_en',
        'full_name_gu', 'full_name_en',
        'mother_name_gu', 'mother_name_en',
        'date_of_birth',
        'dob_in_text_gu', 'dob_in_text_en',
        'birth_place_gu', 'birth_place_en',
        'native_place_gu', 'native_place_en',
        'religion_gu', 'religion_en',
        'cast_gu', 'cast_en',
        'category_gu', 'category_en',
        'is_minority',
        'sharirik_jaati',
        'last_school_gu', 'last_school_en',
        'admission_under_rte',
        'photo',
        'mobile', 'whatsapp',
        'apaar_id', 'uid_no', 'pen_no', 'aadhar_no',
        'name_as_per_aadhar',
        'ration_card_no',
        'bank_name', 'bank_branch', 'bank_ifsc', 'bank_account_no',
        'name_as_per_bank',
        'leaving_reason_gu', 'leaving_reason_en',
        'leaving_date',
        'leaving_standard_id',
        'lc_number',
        'leaving_remarks',
        'status',
    ];

    public function admissionStandard()
    {
        return $this->belongsTo(Standard::class, 'admission_standard_id');
    }

    public function admissionClass()
    {
        return $this->belongsTo(SchoolClass::class, 'admission_class_id');
    }

    public function currentStandard()
    {
        return $this->belongsTo(Standard::class, 'current_standard_id');
    }

    public function currentClass()
    {
        return $this->belongsTo(SchoolClass::class, 'current_class_id');
    }

    public function leavingStandard()
    {
        return $this->belongsTo(Standard::class, 'leaving_standard_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'student_id');
    }

    public function scopeDefaultSort($query)
    {
        $columns = SchoolSetting::getSortColumns();
        foreach ($columns as $col) {
            if ($col === 'gr_number') {
                $query->orderByRaw('CAST(gr_number AS UNSIGNED)');
            } else {
                $query->orderBy($col);
            }
        }
        return $query;
    }
}
