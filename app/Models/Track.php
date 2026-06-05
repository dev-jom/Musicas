<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'album_id',
    'position',
    'title',
    'youtube_url',
    'rating',
    'is_best',
    'is_favorite',
    'is_least_favorite',
    'is_downloaded',
    'notes',
])]
class Track extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:1',
            'is_best' => 'boolean',
            'is_favorite' => 'boolean',
            'is_least_favorite' => 'boolean',
            'is_downloaded' => 'boolean',
        ];
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }
}
