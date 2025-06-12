<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Division extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'region', 'description', 'admin_id'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($division) {
            $division->slug = Str::slug($division->title);
        });

        static::updating(function ($division) {
            $division->slug = Str::slug($division->title);
        });
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function subDivisions()
    {
        return $this->hasMany(SubDivision::class);
    }

}
