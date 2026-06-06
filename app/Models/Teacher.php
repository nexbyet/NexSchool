<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'name', 'email', 'phone', 'whatsapp_number', 'address',
        'date_of_birth', 'gender', 'joining_date', 'joining_number',
        'experience_in_years', 'blood_group', 'basic_pay',
        'max_lwp', 'max_cl', 'ratings', 'basic_salary', 'other_salary',
        'status', 'reason_inactive', 'date_inactive',
    ];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher');
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class, 'teacher_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'teacher_id');
    }
}
