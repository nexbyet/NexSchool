<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'credit_hours',
        'pass_mark', 'total_mark', 'status',
    ];

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'subject_teacher');
    }

    public function standards()
    {
        return $this->belongsToMany(Standard::class, 'standard_subject')
                    ->withPivot('sort_order');
    }
}
