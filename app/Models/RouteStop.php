<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteStop extends Model
{
    protected $fillable = [
        'route_id', 'stop_name', 'stop_order',
        'pickup_time', 'drop_time',
    ];

    protected $casts = [
        'pickup_time' => 'string',
        'drop_time' => 'string',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
