<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Observers\WatchlistMovieObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

/**
 * @property int $id
 * @property int $tmdb_id
 * @property bool already_viewed
 * @property string $title
 * @property string $original_title
 * @property string $poster_path
 * @property string $overview
 * @property Carbon|null $release_date
 */
#[ObservedBy([WatchlistMovieObserver::class])]
class WatchlistMovie extends Model
{
     protected $fillable = [
        'watchlist_user_id',
        'title',
        'original_title',
        'overview',
        'release_date',
        'poster_path',
        //'poster',
        'already_viewed',
        'tmdb_id'
    ];

     /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'd-m-Y';

    /**
     * Récupérer à quel user appartient ce film
     */
    public function watchlistMovies(): BelongsTo
    {
        return $this->belongsTo(WatchlistUser::class, 'foreign_key');
    }
}
