<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\WatchlistController;

// TMDB
Route::get('/', [MoviesController::class, 'show_movies_trends']);
Route::get('/movie/{id}', [MoviesController::class, 'show_movie']);
Route::get('/search', [MoviesController::class, 'search_movies']);
// Watchlist
Route::get('/movie/add/{id}', [WatchlistController::class, 'add_movie']);
Route::get('/movie/remove/{id}', [WatchlistController::class, 'remove_movie']);
Route::get('/movie/update/{id}', [WatchlistController::class, 'update_movie']);

Route::get('/watchlist/movies', [WatchlistController::class, 'movies']);
Route::get('/watchlist/movie/{id}', [WatchlistController::class, 'movie']);
Route::get('/watchlist/movies/viewed', [WatchlistController::class, 'movies_already_viewed']);
