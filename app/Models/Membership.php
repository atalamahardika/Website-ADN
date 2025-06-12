<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = ['member_id', 'status', 'masa_berlaku', 'nomor_kartu'];

    public function member() {
        return $this->belongsTo(Member::class);
    }
}
