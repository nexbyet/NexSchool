<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    protected $fillable = [
        'student_id', 'academic_year_id', 'semester', 'student_fee_id', 'receipt_number',
        'amount_paid', 'payment_date', 'payment_method',
        'reference_number', 'notes', 'received_by'
    ];

    protected $casts = ['payment_date' => 'date'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
