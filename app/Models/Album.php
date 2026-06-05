<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'title',
    'source_url',
    'cover_path',
    'cover_source_url',
    'general_notes',
    'status',
])]
class Album extends Model
{
    use HasFactory;

    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class)->orderBy('position');
    }
}
