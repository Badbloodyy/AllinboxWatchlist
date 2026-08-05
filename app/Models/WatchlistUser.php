<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use App\Models\WatchlistMovie;



//#[Table(dateFormat: 'Y-m-d')]
//#[Table(dateFormat: 'U')]
#[Table('watchlist_users')]
class WatchlistUser extends Model
{

    // Transforme le JSON en ARRAY lorsqu'il faudra l'exploiter
    protected $casts = [
        'id_external_movies' => 'array',
        'id_external_movies_already_viewed' => 'array'
    ];

    // J'avais mis #Fillable avant la classe mais ça ne fonctionnais pas
    protected $fillable = [
       'name',
       'id_external_movies',
       'id_external_movies_already_viewed'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
    */
    protected $attributes = [
        'name' => 'user',
        'id_external_movies' => '[]',
        'id_external_movies_already_viewed' => '[]'
    ];



     /**
     * Identifier les films de la liste de l'user
     */
    public function watchlistMovies(): HasMany
    {
        return $this->hasMany(WatchlistMovie::class);
        // Ptete plutôt utilisé ça pour "hydrater" la liste, voir dans la doc eloquent
        //   return $this->hasMany(Comment::class)->chaperone();
    }
}
