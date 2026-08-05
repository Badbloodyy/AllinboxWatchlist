 @include('header')


 <h1 class="p-5 text-left mb-1 text-4xl font-bold tracking-tight text-heading md:text-5xl lg:text-6xl">
    Ma Watchlist
</h1>
<div class="w-full">
    <div class="flex justify-center mb-6" name="actions">
        <div class="w-40 flex-initial">
            <form action="/watchlist/movies/"  method="GET">
                <button class="cursor-pointer bg-green-400 hover:bg-green-300 text-white font-bold py-2 px-4 rounded">
                    Tous mes films
                </button>
            </form>
        </div>
        <div class="ml-4 w-40 flex-initial">
            <form action="/watchlist/movies/viewed/1"  method="GET">
                <button class="cursor-pointer bg-amber-700 hover:bg-amber-500 text-white font-bold py-2 px-4 rounded">
                    Films déjà vu
                </button>
            </form>
        </div>
        <div class="w-32 flex-initial">
            <form form action="/watchlist/movies/viewed/0"  method="GET">
                <button class="cursor-pointer bg-blue-400 hover:bg-blue-300 text-white font-bold py-2 px-4 rounded">
                    Film à voir
                </button>
            </form>
        </div>
    </div>
</div>
@if($results)
    <div class="p-5">
        <section class="main-content" name="movies-content">
            <div class="column-wrapper inverse">
                <div class="content-wrapper">
                    <div class="white-column">
                        <section class="panel">
                            <div class="flex">
                                <div class="media-card-list w-full flex flex-wrap gap-4">
                                    @foreach ($results as $movie)
                                        <div class="comp:media-card w-full bg-white border border-light-grey hover:border-gray-300 rounded-lg shadow-sm overflow-hidden tight">
                                            <div class="flex flex-nowrap relative">
                                                <form action="/movie/remove/{{ $movie['tmdb_id'] }}"  method="GET">
                                                    <button class="cursor-pointer bg-red-500 hover:bg-red-400 text-white font-bold py-2 px-4 rounded" style="right: 2rem; top: 1rem; position: absolute;">
                                                        Retirer de ma Watchlist
                                                    </button>
                                                </form>

                                                <form action="/movie/update/{{ $movie['tmdb_id'] }}"  method="GET">
                                                    @if(in_array($movie['tmdb_id'], $local_movies_already_viewed_ids))
                                                        <button class="cursor-pointer bg-green-500 hover:bg-green-400 text-white font-bold py-2 px-4 rounded" style="right: 16rem; top: 1rem; position: absolute;">
                                                            Déjà vu
                                                        </button>
                                                    @else
                                                        <button class="cursor-pointer bg-blue-400 hover:bg-green-400 text-white font-bold py-2 px-4 rounded" style="right: 16rem; top: 1rem; position: absolute;">
                                                            A visionner
                                                        </button>
                                                    @endif
                                                </form>
                                                @include('success_message')
                                                @include('error_message')
                                                <div class="relative border-r border-light-grey min-w-25 w-60">
                                                    <a class="flex w-full" href="movie/{{ $movie['tmdb_id'] }}">
                                                        <div class="image aspect-poster glyphicons picture no_image_holder bg-light-grey! grey rounded-none! overflow-hidden">
                                                            <img alt="{{ $movie['title'] }}" class="poster w-full block" loading="lazy" decoding="async" fetchpriority="low" src="https://image.tmdb.org/t/p/w500/{{ $movie['poster_path'] }}">
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="flex flex-wrap items-center content-center w-full p-3">
                                                    <div class="w-full flex items-center">
                                                        <div class="flex flex-wrap w-full">
                                                            <a class="font-normal hover:underline hover:underline-offset-2 hover:decoration-current hover:text-tmdb-light-blue" href="movie/{{ $movie['tmdb_id'] }}">
                                                                <h2 class="font-semibold text-base/6 m-0 whitespace-normal">
                                                                    <span>
                                                                        {{ $movie['title'] }}
                                                                    </span>
                                                                    <span class="font-light">
                                                                        ({{ $movie['original_title'] }})
                                                                    </span>
                                                                </h2>
                                                            </a>
                                                            <span class="release_date w-full font-light">
                                                                {{ isset($movie['release_date']) && !empty($movie['release_date']) ? date('d-m-Y', strtotime($movie['release_date'])) : '-' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4 **:m-0">
                                                        <p>
                                                            {{ $movie['overview'] }}
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
    </div>

    {{ $results->links() }}
@endif
@if($results->isEmpty())
    <div class="w-full text-4xl text-center">
        <p>Aucune donnée trouvée malheureusement !</p>
        <p>Ajoutez-en de suite via la recherche ou l'onglet tendance !</p>
    </div>
@endif



@include('footer')
