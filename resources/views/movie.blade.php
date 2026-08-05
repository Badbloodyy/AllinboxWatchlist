@include('header')

<div>

    <form action="<?php echo "/movie/add/" . $movie->id ?>" method="GET">
        <button>Ajouter le film !</button>
    </form>
    <p x-text="message"></p>
    <form action="<?php echo "update/" . $movie->id ?>" method="GET">
        <button>Ajouter aux films vus !</button>
    </form>
    <form action="<?php echo "remove/" . $movie->id ?>" method="GET">
        <button>Supprimer de la watchlist</button>
    </form>



    @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
    @endif

    <?php
        $movie_json = json_encode($movie, JSON_PRETTY_PRINT);
        $recommendation_json = json_encode($recommendation['results'], JSON_PRETTY_PRINT);


        //echo $message;
        echo "\n <h1> Movie unique </h1> \n";
        echo "<pre>" . $movie_json . "</pre>";
        echo "\n <h1> Recommendations </h1> \n";
        echo "<pre>" . $recommendation_json . "<\pre>";
    ?>
    <!-- Fait apparaitre tout les films accédé avec une id unique. A aussi les recommendations
    return redirect()->to('url')->send(); -->
</div>


@include('footer')
