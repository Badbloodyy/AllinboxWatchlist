<?php

namespace App\Observers;

use App\Models\WatchlistMovie;
use App\Models\WatchlistUser;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class WatchlistMovieObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the WatchlistMovie "created" event.
     */
    public function created(WatchlistMovie $watchlistMovie): void
    {

        $user_id = json_decode($watchlistMovie->watchlist_user_id);

        $movies = json_decode(WatchlistMovie::where('watchlist_user_id', $user_id)->get());

        $tmdb_ids = array_map(
            function($movie) {
                return $movie->tmdb_id;
            },
            $movies
        );

        // first bizarre mais obligatoire pour faire le -save()
        $watchlistUser = WatchlistUser::where('id', $user_id)->first();
        $watchlistUser->id_external_movies = $tmdb_ids;
        $watchlistUser->save();
    }

    /**
     * Handle the WatchlistMovie "updated" event.
     */
    public function updated(WatchlistMovie $watchlistMovie): void
    {
        $user_id = json_decode($watchlistMovie->watchlist_user_id);
        $movies = json_decode(WatchlistMovie::where('watchlist_user_id', $user_id)->get());

        /*
        Flight::where('active', 1)
        ->where('destination', 'San Diego')
        ->update(['delayed' => 1]);
        */
        $filtered_movies = array_filter($movies, function($movie) {
                return $movie->already_viewed == true;
        });

        $already_viewed_ids = array_map(
            function($movie) {
                return $movie->tmdb_id;
            },
            $filtered_movies
        );

        $watchlistUser = WatchlistUser::where('id', $user_id)->first();
        $watchlistUser->id_external_movies_already_viewed = $already_viewed_ids;
        $watchlistUser->save();
    }

    /**
     * Handle the WatchlistMovie "deleted" event.
     */
    public function deleted(WatchlistMovie $watchlistMovie): void
    {

        // On met à jour la watchlist soit en se basant sur la liste existante (et unset la valeur) ou
        // en faisant des requêtes pour rafraichir la liste, basé sur les films présents en BDD.
        // La seconde est plus fiable, même si la premiere est moins energivore en requête

        $user_id = json_decode($watchlistMovie->watchlist_user_id);
        $movies = json_decode(WatchlistMovie::where('watchlist_user_id', $user_id)->get());

        $filtered_movies = array_filter($movies, function($movie) {
                return $movie->already_viewed == true;
        });

        $already_viewed_ids = array_map(
            function($movie) {
                return $movie->tmdb_id;
            },
            $filtered_movies
        );

        $tmdb_ids = array_map(
            function($movie) {
                return $movie->tmdb_id;
            },
            $movies
        );

        $watchlistUser = WatchlistUser::where('id', $user_id)->first();
        $watchlistUser->id_external_movies_already_viewed = $already_viewed_ids;
        $watchlistUser->id_external_movies = $tmdb_ids;
        $watchlistUser->save();
    }

    /**
     * Handle the WatchlistMovie "restored" event.
     */
    public function restored(WatchlistMovie $watchlistMovie): void
    {
        //
    }

    /**
     * Handle the WatchlistMovie "force deleted" event.
     */
    public function forceDeleted(WatchlistMovie $watchlistMovie): void
    {
        //
    }
}
