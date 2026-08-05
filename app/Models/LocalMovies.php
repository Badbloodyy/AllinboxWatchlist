<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * @property int $id
 * @property int $watchlist_user_id
 * @property WatchlistMovie[] $local_movies
 */
#[Fillable(['local_movies'])]
class LocalMovies extends Model
{
    /**
     * Identifier qui à la liste de films locaux
     */
    public function watchlistUser(): BelongsTo
    {
        return $this->belongsTo(WatchlistUser::class);
    }

      /**
     * Identifier les films de la liste local
     */
    public function WatchlistMovies(): HasMany
    {
        return $this->hasMany(WatchlistMovie::class);
    }
}
