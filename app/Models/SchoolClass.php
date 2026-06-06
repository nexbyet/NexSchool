<?php

// NexSchool - SchoolClass Model
// વર્ગો: ધોરણ, વિભાગ, વર્ગશિક્ષક, ક્ષમતા
// Inverse: belongsTo Teacher, HasMany Students and Subjects

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchoolClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'section', 'room_number', 'teacher_id', 'academic_year',
        'capacity', 'description', 'status', 'standard_id', 'sort_order',
    ];

    // Relationship: Class belongs to a Standard (ધોરણ)
    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }

    // Relationship: Class has one class teacher
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    // Relationship: Class teacher assignments per academic year
    public function classTeacher()
    {
        return $this->hasOne(ClassTeacher::class, 'school_class_id');
    }

    public function classTeachers()
    {
        return $this->hasMany(ClassTeacher::class, 'school_class_id');
    }

    // Relationship: Class has many students
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    // Relationship: Class has many subjects
    public function subjects()
    {
        return $this->hasMany(Subject::class, 'class_id');
    }
}
