<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\WatchlistMovie;
use App\Models\WatchlistUser;

class WatchlistController extends Controller
{
    public function movies() : View {
        $user = WatchlistUser::where(['name' => 'myUser'])->first();

        return view('movies_watchlist', [
            'results' => WatchlistMovie::paginate(15),
            'local_movies_ids' => $user->id_external_movies,
            'local_movies_already_viewed_ids' => $user->id_external_movies_already_viewed
        ]);
    }

    public function movie(string $id) {
        if(!is_numeric($id)) {
            return redirect()->back()->with('error', "L'identifiant du film doit être un nombre");
        }

        $movie_data = WatchlistMovie::where('tmdb_id', $id)->first();
        $user = WatchlistUser::where(['name' => 'myUser'])->first();

        // Test de l'id trouve bien un film auprés de l'api tmdb, comme un objet response
        if(!$movie_data) {
            return redirect()->back()->with('error', "L'id tmDb ne mène pas à un film existant dans votre watchlist");
        }

        $key_cache = "tmdb_popular_page_{$id}";

        $data_cache = Cache::remember($key_cache, now()->addMinutes(15), function () use ($id) {
            $recommendation_data = Http::api_tmdb()->get('movie/' . $id . '/recommendations', [
           'language' => 'fr-FR'
            ]);

            if($recommendation_data->failed()) {
                return redirect()->back()->with('error', "L'id TMDB ne mène pas à un film existant ou l'API à un problème. Impossible d'afficher les recommandations");
            }

            return $recommendation_data->json();
        });

        // On aura une liste de recommandation parmi les 3 comme ça !
        $random_recom = collect($data_cache['results'])->random(3);

        return view('movie_watchlist', [
            'movie' => $movie_data,
            'recommendation' => $random_recom,
            'local_movies_ids' => $user->id_external_movies,
            'local_movies_already_viewed_ids' => $user->id_external_movies_already_viewed
        ]);
    }

    public function movies_already_viewed(string $id) : View {
        if(!is_numeric($id)) {
            return redirect()->back()->with('error', "Filtre annulé, mauvaise valeur insérée");
        }

        $user = WatchlistUser::where(['name' => 'myUser'])->first();

        return view('movies_watchlist', [
            'results' => WatchlistMovie::where('already_viewed', $id)->paginate(15),
            'local_movies_ids' => $user->id_external_movies,
            'local_movies_already_viewed_ids' => $user->id_external_movies_already_viewed
        ]);
    }

    public function add_movie(string $id, Request $request) {
        // Check de l'info (on sait jamais vu qu'on peut le mettre manuellement dans l'url)
        if(!is_numeric($id)) {
            return redirect()->back()->with('error', "L'identifiant du film doit être un nombre");
        }

        $data = Http::api_tmdb()->get('/movie/' . $id, [
           'language' => 'fr-FR'
        ]);

        // Test de l'id trouve bien un film auprés de l'api tmdb, comme un objet response
        if($data->failed()) {
            return redirect()->back()->with('error', "L'id TMDB ne mène pas à un film existant. Réessayez avec un autre lien");
        }

        // On va quand même check si le film n'est pas déjà en local
        try {
            $local_movie = WatchlistMovie::where('tmdb_id', $id)->first();

            if($local_movie) {
                return redirect()->back()->with('error', 'Ce film est déjà présent !');
            }

            $watchlist_user = json_decode(WatchlistUser::where('name', 'myUser')->first());

            if($data) {
                $data_json = json_decode($data);
                $new_movie = [
                    'watchlist_user_id' => $watchlist_user->id,
                    'tmdb_id' => $data_json->id,
                    'title' => $data_json->title,
                    'original_title' => $data_json->original_title,
                    'overview' => $data_json->overview,
                    'poster_path' => $data_json->poster_path,
                    'release_date' => $data_json->release_date,
                    'already_viewed' => false,
                ];

                $newMovie = WatchlistMovie::firstOrCreate($new_movie);

                return redirect()->back()->with('success', 'Le film à été ajouté à votre Watchlist !');
            }
        } catch (\Exception $e) {
            // Se renseigner sur comment exploiter cette variable $e pour affiner les erreurs
            return redirect()->back()->with('error', "Il y a eu une erreur technique");
        }
    }

    public function update_movie(string $id) {
        if(!is_numeric($id)) {
            return redirect()->back()->with('error', "L'identifiant du film doit être un nombre");
        }

        try {
            //throw new \Exception("Simulation : API DOWN");
            $local_movie = WatchlistMovie::where('tmdb_id', $id)->first();

            if(!$local_movie) {
                return redirect()->route('movies_watchlist')->with('error', "Ce film n'est pas présent dans votre Watchlist !");
            }

            $bool = $local_movie->already_viewed;

            if($bool) {
                $local_movie->already_viewed = 0;
                $message = "Vous n'avez pas vu ce film";
            } else {
                $local_movie->already_viewed = 1;
                $message = "Vous avez vu ce film";
            }

            $local_movie->save();

            return redirect()->back()->with('success', $message);


        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Il y a eu une erreur technique");
        }
    }

    public function remove_movie(string $id) {
        if(!is_numeric($id)) {
            return redirect()->back()->with('error', "L'identifiant du film doit être un nombre");
        }
        try {
            $movie = WatchlistMovie::where('tmdb_id', $id)->first();

            if(!$movie) {
                return redirect()->route('movies_watchlist')->with('success', "Le film à bien été supprimé de votre Watchlist");
            }

            if($movie->deleteOrFail() === false) {
                return redirect()->back()->with('error', "Ce film n'est pas présent dans votre Watchlist !");
            }

            return redirect()->route('movies_watchlist')->with('success', "Le film à bien été supprimé de votre Watchlist");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Il y a eu une erreur technique");
        }
    }

}
