<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsLandingSection extends Model
{
    protected $fillable = ['section', 'key', 'value', 'icon'];
}
