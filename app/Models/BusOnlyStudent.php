<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusOnlyStudent extends Model
{
    protected $fillable = [
        'full_name_gu', 'standard_label', 'gaam', 'mobile',
        'route_id', 'fee_sem1', 'fee_sem2', 'status',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function feePayments()
    {
        return $this->hasMany(BusOnlyFeePayment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getTotalPaidAttribute()
    {
        return (float) $this->feePayments()->sum('amount');
    }

    public function getTotalFeeAttribute()
    {
        return (float) $this->fee_sem1 + (float) $this->fee_sem2;
    }

    public function getDueFeeAttribute()
    {
        $paid = (float) $this->feePayments()->sum('amount');
        return max(0, $this->total_fee - $paid);
    }
}
