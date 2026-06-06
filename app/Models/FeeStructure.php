<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $fillable = [
        'academic_year_id', 'semester', 'type', 'frequency',
        'late_fee_type', 'late_fee_amount', 'late_fee_after_days', 'is_active'
    ];

    const TYPES = ['tuition' => 'શાળા ફી', 'transport' => 'બસ ફી', 'other' => 'અન્ય'];

    protected $casts = ['is_active' => 'boolean'];
    protected $appends = ['total_amount'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function standards()
    {
        return $this->belongsToMany(Standard::class, 'fee_structure_standard');
    }

    public function details()
    {
        return $this->hasMany(FeeStructureDetail::class);
    }

    public function getTotalAmountAttribute()
    {
        return $this->details->sum('amount');
    }
}
