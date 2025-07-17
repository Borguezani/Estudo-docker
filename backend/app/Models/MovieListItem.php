<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovieListItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_list_id',
        'tmdb_movie_id',
        'movie_title',
        'movie_poster_path',
        'movie_overview',
        'movie_release_date',
        'movie_vote_average',
        'user_notes',
    ];

    protected function casts(): array
    {
        return [
            'movie_release_date' => 'datetime',
            'movie_vote_average' => 'decimal:1',
        ];
    }

    /**
     * Get the movie list that owns the item.
     */
    public function movieList(): BelongsTo
    {
        return $this->belongsTo(MovieList::class);
    }

    /**
     * Get the full poster URL.
     */
    public function getPosterUrlAttribute(): ?string
    {
        return $this->movie_poster_path 
            ? 'https://image.tmdb.org/t/p/w500' . $this->movie_poster_path
            : null;
    }

    /**
     * Get a formatted release year.
     */
    public function getReleaseYearAttribute(): ?string
    {
        return $this->movie_release_date 
            ? $this->movie_release_date->format('Y')
            : null;
    }
}
