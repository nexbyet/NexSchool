<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeHead extends Model
{
    protected $fillable = ['name_gu', 'name_en', 'description', 'is_active', 'sort_order'];
    protected $casts = ['is_active' => 'boolean'];
}
