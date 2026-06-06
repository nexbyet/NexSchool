<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructureDetail extends Model
{
    protected $fillable = ['fee_structure_id', 'fee_head_id', 'amount'];

    protected $casts = ['amount' => 'float'];

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class);
    }
}
