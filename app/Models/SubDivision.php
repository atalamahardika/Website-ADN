<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubDivision extends Model
{
    use HasFactory;

    protected $fillable = [
        'division_id',
        'title',
        'slug',
        'description',
        'is_approved',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($subdivision) {
            $subdivision->slug = Str::slug($subdivision->title);
        });

        static::updating(function ($subdivision) {
            $subdivision->slug = Str::slug($subdivision->title);
        });
    }


    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
