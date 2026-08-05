<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\WatchlistMovie;
use App\Models\WatchlistUser;

class WatchlistController extends Controller
{
    public function movies() : View {
        return view('movies_watchlist', [
            'results' => WatchlistMovie::paginate(15)
        ]);
    }

      public function movie(string $id) : View {
        $local_movie = WatchlistMovie::where('tmdb_id', $id)->first();
        $recommendation_data = Http::api_tmdb()->get('movie/' . $id . '/recommendations', [
           'language' => 'fr-FR'
        ]);

         return view('movie_watchlist', [
            'movie' => $local_movie,
            'recommendation' => $recommendation_data,
        ]);
    }

    public function movies_already_viewed() : View {
        return view('movies_watchlist', [
            'results' => DB::table('watchlist_movies')->where('already_viewed', 1)->paginate(15)
        ]);
    }

    public function add_movie(string $id) : RedirectResponse {

        $data = Http::api_tmdb()->get('/movie/' . $id, [
           'language' => 'fr-FR'
        ]);

        $data_json = json_decode($data);
       //'poster',
        $watchlistUser = json_decode(WatchlistUser::where('name', 'myUser')->first());

       if($data) {
            $data_json = json_decode($data);
            $new_movie = [
                'watchlist_user_id' => $watchlistUser->id,
                'tmdb_id' => $data_json->id,
                'title' => $data_json->title,
                'original_title' => $data_json->original_title,
                'overview' => $data_json->overview,
                'poster_path' => $data_json->poster_path,
                'release_date' => $data_json->release_date,
                'already_viewed' => false,
            ];

            $newMovie = WatchlistMovie::firstOrCreate($new_movie);

            return redirect('movie/' . $data_json->id)->with('status', 'Film ajouté!!');
       }
    }

    public function update_movie(string $id) {
        $movie = WatchlistMovie::where('tmdb_id', $id)->first();
        $bool = $movie->already_viewed;
        $message = '';


        if($bool) {
            $movie->already_viewed = 0;
            $message = 'Movie not seen';
        } else {
            $movie->already_viewed = 1;
            $message = "Movie seen!";
        }

        $movie->save();

        return redirect('movie/' . $id)->with('status', $message);
    }

    public function remove_movie(string $id) {
        // tmdb_id
        $movie = WatchlistMovie::where('tmdb_id', $id)->first();

        if($movie->deleteOrFail() === false) {
             return response(
                "Impossible de supprimer le film avec l'id {$id}",
                Response::HTTP_BAD_REQUEST
            );
        }

        return redirect('movie/' . $id)->with(["id" => $id, "deleted" => true]);
    }

}
