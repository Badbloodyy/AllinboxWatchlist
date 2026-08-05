<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\WatchlistUser;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('watchlist_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('id_external_movies');
            $table->json('id_external_movies_already_viewed');
            $table->timestamps();
        });

        Schema::create('watchlist_movies', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignIdFor(WatchlistUser::class);
            $table->integer('tmdb_id');
            $table->boolean('already_viewed');
            $table->string('title');
            $table->string('original_title');
            $table->string('overview');
            $table->string('poster_path');
            //$table->binary('poster');
            $table->date('release_date');
             $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watchlist_users');
        Schema::dropIfExists('watchlist_movies');
    }
};
