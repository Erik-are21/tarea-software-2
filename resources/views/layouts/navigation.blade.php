<nav x-data="{ open: false }" class="site-nav">
    <div class="site-nav__inner">
        <a class="site-brand" href="{{ route('teams.index') }}">CANCHA<span>.</span></a>

        <div class="site-nav__links">
            <a class="{{ request()->routeIs('teams.*') ? 'is-active' : '' }}" href="{{ route('teams.index') }}">Equipos</a>
            <a class="{{ request()->routeIs('players.*') ? 'is-active' : '' }}" href="{{ route('players.index') }}">Jugadores</a>
            <a class="{{ request()->routeIs('games.*') ? 'is-active' : '' }}" href="{{ route('games.index') }}">Partidos</a>
        </div>

        @if (Route::has('login'))
            <div class="site-nav__account">
                @auth
                    <span>{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Salir</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Iniciar sesión</a>
                @endauth
            </div>
        @endif

        <button class="site-nav__toggle" type="button" @click="open = !open" :aria-expanded="open" aria-label="Abrir menú">
            <span></span><span></span>
        </button>
    </div>
    <div class="site-nav__mobile" x-cloak x-show="open">
        <a href="{{ route('teams.index') }}">Equipos</a>
        <a href="{{ route('players.index') }}">Jugadores</a>
        <a href="{{ route('games.index') }}">Partidos</a>
        @if (Route::has('login'))
            @auth
                <span>{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Cerrar sesión</button>
                </form>
            @else
                <a href="{{ route('login') }}">Iniciar sesión</a>
            @endauth
        @endif
    </div>
</nav>
