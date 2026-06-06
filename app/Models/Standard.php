<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Standard extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'sort_order',
    ];

    public function classes()
    {
        return $this->hasMany(SchoolClass::class, 'standard_id')->orderBy('sort_order');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'standard_subject')
                    ->withPivot('sort_order')
                    ->orderByPivot('sort_order');
    }
}
