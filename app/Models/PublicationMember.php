<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'authors',
        'formatted_authors',
        'title',
        'year',
        'journal_name',
        'volume',
        'pages',
        'link'
    ];

    protected $casts = [
        'authors' => 'array',
    ];

    public function member() {
        return $this->belongsTo(Member::class);
    }
}
