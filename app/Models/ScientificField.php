<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScientificField extends Model
{
    protected $fillable = [
        'name',
        'member_id',
    ];
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
