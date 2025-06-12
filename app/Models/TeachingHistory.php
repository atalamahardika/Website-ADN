<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeachingHistory extends Model
{
    protected $fillable = [
        'member_id',
        'mata_kuliah',
        'institusi',
        'tahun_ajar',
    ];
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
