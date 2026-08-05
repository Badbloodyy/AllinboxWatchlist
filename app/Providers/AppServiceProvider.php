<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Http;
use App\Models\WatchlistUser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        // Ajout de la requête vers l'api pour faciliter son appel dans l'application
        Http::macro('api_tmdb', function () {
            return Http::withToken(config('services.tmdb.api_key'))->baseUrl(config('services.tmdb.api_url'));
        });

        // url permettant de reconstituer l'image obtenue via l'api
        Http::macro('media_tmdb', function () {
            return Http::withToken(config('services.tmdb.api_key'))->baseUrl(config('services.tmdb.media_url'));
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
