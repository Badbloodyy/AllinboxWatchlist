<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\WatchlistUser;
use App\Models\WatchlistMovie;
use Illuminate\Pagination\LengthAwarePaginator;


class MoviesController extends Controller
{
    private $tmdb_trends;

    public function show_movies_trends(Request $request): View
    {
        // Je triche un peu en le mettant ici, mais en le mettant dans le service provider c'est problématique
        $user = WatchlistUser::firstOrCreate(['name' => 'myUser']);

         // Je vais tester le comportement de watchlist si le serveur tmdb est down
        /* Essai avec fake mais pas concluant
        Http::fake([
            'api.themoviedb.org/*' => Http::sequence()
                ->push('Hello World', 200)
                ->push(['foo' => 'bar'], 200)
                ->pushStatus(503),
        ]);
        */

        /*
        try {
            throw new \Exception("Simulation : API DOWN");
        } catch (\Exception $e) {
            $results = WatchlistMovie::paginate(15);
            session()->now('error', 'Mode sans API externe');
        }

        return view('movies_watchlist', [
            'results' => $results,
            'local_movies_ids' => $user->id_external_movies,
            'local_movies_already_viewed_ids' => $user->id_external_movies_already_viewed,
        ]);
        */

        $current_page = $request->input('page', 1);

        $tmdb_trends = Http::api_tmdb()->get('/trending/movie/week', [
            'language' => 'fr-FR',
            'page'     => $current_page
        ]);

        if($tmdb_trends->failed()) {
            return redirect()->route('movies_watchlist')->with('error', "Une erreur est survenue, passage dans votre liste local");
        }

        $pagination_data = $this->generate_pagination($request, $tmdb_trends, $current_page);

        return view('movies', [
            'results' => $pagination_data,
            'local_movies_ids' => $user->id_external_movies,
            'local_movies_already_viewed_ids' => $user->id_external_movies_already_viewed,
            'trends' => true
        ]);
    }

    public function show_movie(string $id)
    {
         if(!is_numeric($id)) {
            return redirect()->back()->with('error', "L'identifiant du film doit être un nombre");
        }

        $key_cache = "tmdb_popular_page_{$id}";

        // Cache recommendation
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

        $user = WatchlistUser::where('name', 'myUser')->first();

        try {
            // Call BDD local, sinon tmdb
            $movie = WatchlistMovie::where('tmdb_id', $id)->first();

            if($movie) {
                return view('movie_watchlist', [
                    'movie' => $movie,
                    'recommendation' => $random_recom,
                    'local_movies_ids' => $user->id_external_movies,
                    'local_movies_already_viewed_ids' => $user->id_external_movies_already_viewed
                ]);
            } else {
                $movie = Http::api_tmdb()->get('/movie/' . $id, [
                    'language' => 'fr-FR'
                ]);

                if($movie->failed()) {
                    return redirect()->back()->with('error', "L'id TMDB ne mène pas à un film existant. Réessayez avec un autre lien");
                }

                $movie_data = $movie->json();

                return view('movie', [
                    'movie' => $movie_data,
                    'recommendation' => $random_recom,
                    'local_movies_ids' => $user->id_external_movies,
                    'local_movies_already_viewed_ids' => $user->id_external_movies_already_viewed
                ]);
            }
        } catch(Exception $e) {
            Log::error("Erreur lors de l'ajout : " . $e->getMessage());

            return redirect()->back()->with('error', "Il y a eu une erreur technique");
        }
    }

    public function search_movies(Request $request): View
    {
        $search = request()->query('search');
        $current_page = $request->input('page', 1);

        $user = WatchlistUser::where('name', 'myUser')->first();

        $tmdb_search = Http::api_tmdb()->get('search/movie', [
            'query' => $search,
            'language' => 'fr-FR'
        ]);

        $pagination_data = $this->generate_pagination($request, $tmdb_search, $current_page);

        return view('movies', [
            'results' => $pagination_data,
            'local_movies_ids' => $user->id_external_movies,
            'local_movies_already_viewed_ids' => $user->id_external_movies_already_viewed,
            'trends' => false
        ]);
    }

    public function generate_pagination(Request $request, Response $data, int $current_page) : LengthAwarePaginator {
        // Création d'une pagination compatible avec tailwindcss, en créant un objet paginator
        $data_json = $data->json();
        $movies = $data_json['results'] ?? [];
        $resultByPage = 20;

        // 10 pages suffisent pour le trend de cette app test
        $totalResults = min($data_json['total_results'] ?? 0, 200);

        // 20 -> Nombre de résultat de tmdb, 1 -> La page par défaut
        return new LengthAwarePaginator(
            $movies,
            $totalResults,
            $resultByPage,
            $current_page,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );
    }
}
