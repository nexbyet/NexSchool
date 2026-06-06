<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'vehicle_no', 'vehicle_type', 'capacity',
        'driver_name', 'driver_mobile', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function routes()
    {
        return $this->hasMany(Route::class);
    }
}
