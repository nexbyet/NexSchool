<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = [
        'route_name', 'vehicle_id', 'description', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function stops()
    {
        return $this->hasMany(RouteStop::class)->orderBy('stop_order');
    }

    public function studentRoutes()
    {
        return $this->hasMany(StudentRoute::class);
    }
}
