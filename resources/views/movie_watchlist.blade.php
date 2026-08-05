@include('header')

<h1 class="pt-5 text-center mb-1 text-4xl font-bold tracking-tight text-heading md:text-5xl lg:text-6xl">
    {{ $movie['title'] }}
</h1>

<section class="flex justify-center image-content p-8" name="movie-content">
    <div class="column-wrapper inverse">
        <div class="content-wrapper">
            <div class="white-column">
                <section class="panel">
                    <div class="flex">
                        <div class="media-card-list w-full flex flex-wrap gap-4">
                            <div class="comp:media-card w-full bg-white border border-light-grey hover:border-gray-300 rounded-lg shadow-sm overflow-hidden tight">
                                <div class="flex flex-nowrap relative">
                                    <div class="relative border-r border-light-grey min-w-25 w-90">
                                        <a class="flex w-full">
                                            <div class="image aspect-poster glyphicons picture no_image_holder bg-light-grey! grey rounded-none! overflow-hidden">
                                                <img alt="{{ $movie['title'] }}" class="poster w-full block" loading="lazy" decoding="async" fetchpriority="low" src="https://image.tmdb.org/t/p/w500/{{ $movie['poster_path']}}">
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</section>
<section class="flex flex-wrap justify-self-center min-w-100 w-200" name="details">
    <div class="w-full">
        <div class="flex justify-center mb-6" name="actions">
            <div class="justify-items-center w-64 flex-initial">
                @if(in_array($movie['tmdb_id'], $local_movies_ids))
                    <form action="/movie/remove/{{ $movie['tmdb_id'] }}"  method="GET">
                        <button class="cursor-pointer bg-red-500 hover:bg-red-400 text-white font-bold py-2 px-4 rounded">
                            Retirer de ma Watchlist
                        </button>
                    </form>
                @else
                    <form action="/movie/add/{{ $movie['tmdb_id'] }}"  method="GET">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white cursor-pointer font-bold py-2 px-4 rounded">
                            Watchlister
                        </button>
                    </form>
                @endif
            </div>

            @if(in_array($movie['tmdb_id'], $local_movies_ids))
                <div class="w-32 flex-initial">
                    <form action="/movie/update/{{ $movie['tmdb_id'] }}"  method="GET">
                        @if(in_array($movie['tmdb_id'], $local_movies_already_viewed_ids))
                            <button class="cursor-pointer bg-green-500 hover:bg-green-400 text-white font-bold py-2 px-4 rounded">
                                Déjà vu
                            </button>
                        @else
                            <button class="cursor-pointer bg-blue-400 hover:bg-green-400 text-white font-bold py-2 px-4 rounded">
                                A visionner
                            </button>
                        @endif
                    </form>
                </div>
            @endif

            @include('success_message')
            @include('error_message')
        </div>
        @if (isset($movie['release_date']) && !empty($movie['release_date']))
        <p class="text-center mb-1 text-lg font-normal text-body lg:text-xl sm:px-16 xl:px-48">
            <span class="font-bold">Date de sortie : </span>{{ date('d-m-Y', strtotime($movie['release_date'])) }}
        </p>
        @endif
        <p class="text-center mb-3 text-lg font-normal text-body lg:text-xl sm:px-16 xl:px-48">
             <span class="font-bold">Nom original : </span>({{ $movie['original_title'] }})
        </p>
        @if (isset($movie['overview']) && !empty($movie['overview']))
        <p class="mb-4 text-lg font-normal text-body">
            <span class="font-bold">Résumé :</span> {{ $movie['overview']}}
        </p>
        @endif
    </div>
</section>

<h1 class="pt-5 text-center mb-4 text-4xl font-bold tracking-tight text-heading md:text-5xl lg:text-6xl">
    Recommendations
</h1>
<section class="recommendation-content p-10" name="movies-content">
    <div class="column-wrapper inverse">
        <div class="content-wrapper">
            <div class="white-column">
                <section class="panel">
                    <div class="flex">
                        <div class="media-card-list w-full flex flex-wrap gap-4">
                            @foreach ($recommendation as $movie_recom)
                                <div class="comp:media-card w-full bg-white border border-light-grey hover:border-gray-300 rounded-lg shadow-sm overflow-hidden tight">
                                    <div class="flex flex-nowrap relative">
                                        @if(in_array($movie_recom['id'], $local_movies_ids))
                                            <button disabled class="cursor-not-allowed bg-blue-300 hover:bg-blue-400 text-white font-bold py-2 px-4 rounded" style="right: 2rem; top: 1rem; position: absolute;">
                                                Dans ma Watchlist
                                            </button>
                                        @endif
                                        @if(in_array($movie_recom['id'], $local_movies_already_viewed_ids))
                                            <button class="cursor-not-allowed bg-green-500 hover:bg-green-400 text-white font-bold py-2 px-4 rounded" style="right: 14rem; top: 1rem; position: absolute;">
                                                Déjà vu
                                            </button>
                                        @endif
                                        @if(in_array($movie_recom['id'], $local_movies_ids))
                                            <button class="cursor-not-allowed bg-blue-400 hover:bg-green-400 text-white font-bold py-2 px-4 rounded" style="right: 14rem; top: 1rem; position: absolute;">
                                                A visionner
                                            </button>
                                        @endif
                                        <div class="relative border-r border-light-grey min-w-25 w-120">
                                            <a class="flex w-full" href="/movie/{{ $movie_recom['id'] }}">
                                                <div class="image aspect-poster glyphicons picture no_image_holder bg-light-grey! grey rounded-none! overflow-hidden">
                                                    <img alt="{{ $movie_recom['title'] }}" class="poster w-full block" loading="lazy" decoding="async" fetchpriority="low" src="https://image.tmdb.org/t/p/w500/{{ $movie_recom['poster_path']}}">
                                                </div>
                                            </a>
                                        </div>
                                        <div class="flex flex-wrap items-center content-center w-full p-3">
                                            <div class="w-full flex items-center">
                                                <div class="flex flex-wrap w-full">
                                                    <a class="font-normal hover:underline hover:underline-offset-2 hover:decoration-current hover:text-tmdb-light-blue" href="/movie/{{ $movie_recom['id'] }}">
                                                        <h2 class="font-semibold text-base/6 m-0 whitespace-normal">
                                                            <span>
                                                                {{ $movie_recom['title'] }}
                                                            </span>
                                                            <span class="font-light">
                                                                ({{ $movie_recom['original_title'] }})
                                                            </span>
                                                        </h2>
                                                    </a>
                                                    <span class="release_date w-full font-light">
                                                            {{ isset($movie_recom['release_date']) && !empty($movie_recom['release_date']) ? date('d-m-Y', strtotime($movie_recom['release_date'])) : '-' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mt-4 **:m-0">
                                                <p>
                                                    {{ $movie_recom['overview']}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</section>

@include('footer')
