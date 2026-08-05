@include('header')


<h1 class="p-5 text-left mb-1 text-4xl font-bold tracking-tight text-heading md:text-5xl lg:text-6xl">
    <span>
        @if($trends)
            Tendances
        @else
            Recherche
        @endif
    </span>
</h1>
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
                                            <div class="relative flex flex-nowrap">
                                                @if(in_array($movie['id'], $local_movies_ids))
                                                    <button disabled class="cursor-not-allowed bg-blue-300 hover:bg-blue-400 text-white font-bold py-2 px-4 rounded" style="right: 2rem; top: 1rem; position: absolute;">
                                                        Dans ma Watchlist
                                                    </button>
                                                @else
                                                    <form action="/movie/add/{{ $movie['id'] }}"  method="GET">
                                                        <button class="bg-blue-500 hover:bg-blue-700 text-white cursor-pointer font-bold py-2 px-4 rounded" style="right: 2rem; top: 1rem; position: absolute;">
                                                            Watchlister
                                                        </button>
                                                    </form>
                                                @endif

                                                @include('success_message')
                                                @include('error_message')
                                                <div class="relative border-r border-light-grey min-w-25 w-60">
                                                    <a class="flex w-full" href="movie/{{ $movie['id'] }}">
                                                        <div class="image aspect-poster glyphicons picture no_image_holder bg-light-grey! grey rounded-none! overflow-hidden">
                                                            <img alt="{{ $movie['title'] }}" class="poster w-full block" loading="lazy" decoding="async" fetchpriority="low" src="https://image.tmdb.org/t/p/w500/{{ $movie['poster_path'] }}">
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="flex flex-wrap items-center content-center w-full p-3">
                                                    <div class="w-full flex items-center">
                                                        <div class="flex flex-wrap w-full">
                                                            <a class="font-normal hover:underline hover:underline-offset-2 hover:decoration-current hover:text-tmdb-light-blue" href="movie/{{ $movie['id'] }}">
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
<span class="p-2">{{ $results->links() }}</span>

@endif
@if($results->isEmpty())
    <div class="w-full text-4xl text-center">
        <p>Aucune donnée trouvée malheureusement !</p>
        <p>Essayez un autre terme</p>
    </div>
@endif



@include('footer')

