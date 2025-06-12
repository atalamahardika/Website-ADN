<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationalHistory extends Model
{
    protected $fillable = [
        'member_id',
        'jenjang',
        'institusi',
        'program_studi',
        'tahun_masuk',
        'tahun_lulus'
    ];
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
