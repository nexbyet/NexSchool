<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusOnlyFeePayment extends Model
{
    protected $fillable = [
        'bus_only_student_id', 'semester', 'amount',
        'payment_date', 'payment_method', 'reference_number', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(BusOnlyStudent::class, 'bus_only_student_id');
    }
}
