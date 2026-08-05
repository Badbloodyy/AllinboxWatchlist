<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\Models\WatchlistUser;
use App\Models\WatchlistMovie;
use Illuminate\Pagination\LengthAwarePaginator;


class MoviesController extends Controller
{
    private $tmdb_trends;

    // TODO : Ajouter notification si erreur chargement pour toute requêtes et passer l'info front
    public function show_movies_trends(Request $request): View
    {
        // Je triche un peu en le mettant ici, mais en le mettant dans le service provider c'est problématique
        WatchlistUser::firstOrCreate(['name' => 'myUser']);

        $messageUser = 'Bienvenue!';
        /*
        if ($response->failed()) {
            abort(500, "Impossible de récupérer les données de TMDB.");
        }*/

        $current_page = $request->input('page', 1);
        $tmdb_trends = Http::api_tmdb()->get('/trending/movie/week', [
            'language' => 'fr-FR',
            'page'     => $current_page
        ]);

        $pagination_data = $this->generate_pagination($request, $tmdb_trends, $current_page);

        return view('movies', [
            'results' => $pagination_data,
            'search' => false,
            'trends' => true,
            'messageInitUser' => $messageUser
        ]);
    }

    // TODO :
    // Call film par id et aussi les recommendation (limité au 3 premieres côté front je pense) à retourner
    // Check si l'id est bonne / existe
    // Check si recommandation, sinon renvoi null je pense
    public function show_movie(string $id): View
    {
        // 404 si l'id tmdb n'est pas bonne aussi
        // TODO : Mettre en cache cette donnée
        $recommendation_data = Http::api_tmdb()->get('movie/' . $id . '/recommendations', [
           'language' => 'fr-FR'
        ]);

        // Call BDD local, sinon tmdb
        $movie = WatchlistMovie::where('tmdb_id', $id)->first();

        if(!$movie) {
            $movie = json_decode(Http::api_tmdb()->get('/movie/' . $id, [
                'language' => 'fr-FR'
            ]));

            return view('movie', [
                'movie' => $movie,
                'recommendation' => $recommendation_data,
            ]);
        }

        return view('movie_watchlist', [
            'movie' => $movie,
            'recommendation' => $recommendation_data,
            //'watchlisted' => ,
            //'alreadyViewed' => ,

        ]);
    }

    public function search_movies(Request $request): View
    {
        $messageUser = 'Recherche';
        $search = request()->query('search');
        $current_page = $request->input('page', 1);

        $tmdb_search = Http::api_tmdb()->get('search/movie', [
            'query' => $search,
            'language' => 'fr-FR'
        ]);

        $pagination_data = $this->generate_pagination($request, $tmdb_search, $current_page);

        return view('movies', [
            'results' => $pagination_data,
            'search' => true,
            'trends' => false,
            'messageInitUser' => $messageUser
        ]);
    }

    public function generate_pagination(Request $request, Response $data, int $current_page) : LengthAwarePaginator {
        // Création d'une pagination compatible avec tailwindcss, en créant un objet pagination
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

/*

     $response = Http::api_tmdb()->get('/search/movie', [
            'query' => 'star wars',
            'language' => 'fr-FR'
        ]);

    JSON :
    {
        "id" : xx,
        "poster_path": xxxx , // url img poster
        "title": "Star Wars",
        "overview": "Princess Leia Venturesome Luke Skywalker...", // description
        "release_date": "1977-05-25",
    }
	https://media.themoviedb.org/t/p/w220_and_h330_face/yikio8CfJxIA7faZxgvB9FGXy6u.jpg
    https://image.tmdb.org/t/p/w500/xxx (autre pour d'autres version) ou https://image.tmdb.org/t/p/original/xxx pour l'original
    movie/{movie_id}/recommendations -> recommandation vis à vis du film en question
    search/movie?query=starwars
    $response = Http::api_tmdb()->get('/movie/11/', [
            'language' => 'fr-FR'
        ]);
    https://api.themoviedb.org/3/trending/movie/week -> page tendance pour l'accueil?


*/
