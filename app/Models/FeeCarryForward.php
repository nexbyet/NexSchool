<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeCarryForward extends Model
{
    protected $fillable = [
        'student_id', 'from_academic_year_id', 'to_academic_year_id', 'amount'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function fromAcademicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'from_academic_year_id');
    }

    public function toAcademicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'to_academic_year_id');
    }
}
