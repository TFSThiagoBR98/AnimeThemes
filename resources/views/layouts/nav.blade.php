<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a href="{{ route('welcome') }}" class="navbar-brand">
        AnimeTheme.moe
    </a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#nvb" aria-controls="nvb" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="nvb">
        <ul id="menus" class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="/">Home</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown_catalog" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Catalog
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown_catalog">
                    <a class="dropdown-item" href="#">Animes</a>
                    <a class="dropdown-item" href="#">Collections</a>
                    <a class="dropdown-item" href="#">Artists</a>
                    <a class="dropdown-item" href="#">Series</a>
                    <a class="dropdown-item" href="{{ route('video.index') }}">Videos</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Playlist</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://www.reddit.com/r/AnimeThemes/">Reddit</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://twitter.com/parameterized">Twitter</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="https://github.com/paranarimasu/AnimeThemes">Github</a>
            </li>
        </ul>
    </div>
</nav>