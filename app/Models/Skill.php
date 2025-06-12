<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = ['member_id', 'name'];
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
