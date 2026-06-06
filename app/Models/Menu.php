<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name', 'location', 'status'];

    public function items()
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }

    public function activeItems()
    {
        return $this->hasMany(MenuItem::class)->where('status', true)->orderBy('sort_order');
    }
}
