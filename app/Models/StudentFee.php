<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    protected $fillable = [
        'student_id', 'academic_year_id', 'semester', 'fee_structure_id',
        'total_amount', 'concession_amount', 'net_amount', 'is_active',
        'is_waived', 'excluded_fee_heads',
    ];

    protected $appends = ['paid_amount', 'due_amount'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_waived' => 'boolean',
        'excluded_fee_heads' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function payments()
    {
        return $this->hasMany(FeePayment::class, 'student_fee_id', 'id');
    }

    public function getEffectiveTotalAttribute()
    {
        $excluded = $this->excluded_fee_heads ?? [];
        if (empty($excluded) || !$this->feeStructure) {
            return $this->total_amount;
        }
        $total = $this->total_amount;
        foreach ($this->feeStructure->details as $detail) {
            if (in_array($detail->fee_head_id, $excluded)) {
                $total -= $detail->amount;
            }
        }
        return max(0, $total);
    }

    public function getPaidAmountAttribute()
    {
        return $this->payments()->sum('amount_paid');
    }

    public function getDueAmountAttribute()
    {
        return max(0, $this->net_amount - $this->paid_amount);
    }
}
