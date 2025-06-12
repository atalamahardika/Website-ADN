<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    protected $fillable = [
        'member_id',
        'nama',
        'penyelenggara',
        'tahun',
    ];
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
