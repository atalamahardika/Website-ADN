<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PublicationOrganization extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'content', 'slug'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($publication) {
            $publication->slug = Str::slug($publication->title);
        });

        static::updating(function ($publication) {
            $publication->slug = Str::slug($publication->title);
        });
    }
}
