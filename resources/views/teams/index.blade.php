<x-app-layout>
    <div class="league-page" x-data="{ showForm: {{ ($editing || $errors->any()) ? 'true' : 'false' }} }">
        <section class="league-hero" style="--hero-image: url('{{ asset('images/basketball-court.jpg') }}')">
            <div class="league-hero__content">
                <p class="eyebrow"><span class="eyebrow__dot"></span> La liga está en juego</p>
                <h1>La ciudad cambia.<br>El juego, no.</h1>
                <p class="league-hero__copy">Franquicias de verdad, nuevas rivalidades y una cancha para cada historia.</p>
                <a class="button button--orange" href="#equipos">Explorar equipos <span aria-hidden="true">↓</span></a>
            </div>
            <div class="league-hero__note">
                <span class="league-hero__note-mark">B.</span>
                <span>Pasión local.<br>Juego de altura.</span>
            </div>
            <span class="league-hero__index" aria-hidden="true">01 / 02</span>
        </section>

        <section class="league-content" id="equipos">
            @if (session('success'))
                <div class="notice notice--success" role="status">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="notice notice--error" role="alert">{{ session('error') }}</div>
            @endif

            <div class="section-heading">
                <div>
                    <p class="eyebrow eyebrow--dark">Plantillas · Temporada abierta</p>
                    <h2>Los equipos</h2>
                    <p class="section-heading__copy">Clásicos de la NBA y nuevas franquicias listas para competir.</p>
                </div>
                @can('manage-league')
                    <button class="button button--dark" type="button" @click="showForm = !showForm">
                        <span x-text="showForm ? 'Cerrar formulario' : '＋ Agregar equipo'"></span>
                    </button>
                @endcan
            </div>

            <div class="league-stats" aria-label="Resumen de equipos">
                <div class="league-stat"><span class="league-stat__number">{{ $teams->count() }}</span><span class="league-stat__label">Equipos</span></div>
                <div class="league-stat"><span class="league-stat__number">{{ $teams->where('es_inventado', false)->count() }}</span><span class="league-stat__label">Franquicias NBA</span></div>
                <div class="league-stat"><span class="league-stat__number">{{ $teams->where('es_inventado', true)->count() }}</span><span class="league-stat__label">Nuevas franquicias</span></div>
            </div>

            <section class="bg-white p-6 shadow rounded" aria-labelledby="standings-heading">
                <div class="mb-4">
                    <p class="eyebrow eyebrow--dark">Resultados · Temporada</p>
                    <h3 id="standings-heading" class="text-xl font-semibold">Tabla de posiciones</h3>
                    <p class="text-sm text-gray-600">Estadísticas calculadas a partir de los partidos jugados y sus marcadores.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr>
                                <th class="p-2">#</th>
                                <th class="p-2">Equipo</th>
                                <th class="p-2">JJ</th>
                                <th class="p-2">G</th>
                                <th class="p-2">P</th>
                                <th class="p-2">E</th>
                                <th class="p-2">PF</th>
                                <th class="p-2">PC</th>
                                <th class="p-2">DIF</th>
                                <th class="p-2">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($standings as $standing)
                                <tr class="border-t">
                                    <td class="p-2">{{ $loop->iteration }}</td>
                                    <td class="p-2 font-medium">{{ $standing['team']->nombre }}</td>
                                    <td class="p-2">{{ $standing['games_played'] }}</td>
                                    <td class="p-2">{{ $standing['wins'] }}</td>
                                    <td class="p-2">{{ $standing['losses'] }}</td>
                                    <td class="p-2">{{ $standing['draws'] }}</td>
                                    <td class="p-2">{{ $standing['points_for'] }}</td>
                                    <td class="p-2">{{ $standing['points_against'] }}</td>
                                    <td class="p-2">{{ $standing['point_difference'] > 0 ? '+' : '' }}{{ $standing['point_difference'] }}</td>
                                    <td class="p-2">{{ number_format($standing['win_percentage'] * 100, 1) }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="p-3 text-gray-600">Todavía no hay equipos para mostrar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-xs text-gray-500">JJ: jugados · G: ganados · P: perdidos · E: empatados · PF: puntos a favor · PC: puntos en contra · DIF: diferencia.</p>
            </section>

            @can('manage-league')
                <form x-cloak x-show="showForm" x-transition method="POST"
                      action="{{ $editing ? route('teams.update', $editing) : route('teams.store') }}"
                      class="team-form" id="team-form">
                    @csrf
                    @if ($editing) @method('PUT') @endif
                    <div class="team-form__heading">
                        <div>
                            <p class="eyebrow eyebrow--dark">{{ $editing ? 'Actualiza la ficha' : 'Expande la liga' }}</p>
                            <h3>{{ $editing ? 'Editar equipo' : 'Nueva franquicia' }}</h3>
                        </div>
                        @if ($editing)
                            <a class="text-link" href="{{ route('teams.index') }}">Cancelar</a>
                        @endif
                    </div>

                    <div class="team-form__fields">
                        @foreach (['nombre' => 'Nombre del equipo', 'ciudad' => 'Ciudad', 'entrenador' => 'Entrenador'] as $field => $label)
                            <label class="team-field">
                                <span>{{ $label }}</span>
                                <input name="{{ $field }}" value="{{ old($field, $editing?->{$field}) }}" required maxlength="100">
                                @error($field) <span class="field-error">{{ $message }}</span> @enderror
                            </label>
                        @endforeach
                    </div>
                    <input type="hidden" name="es_inventado" value="0">
                    <label class="team-checkbox">
                        <input type="checkbox" name="es_inventado" value="1" @checked(old('es_inventado', $editing?->es_inventado))>
                        <span>Es una franquicia inventada</span>
                    </label>
                    @error('es_inventado') <span class="field-error">{{ $message }}</span> @enderror
                    <div class="team-form__actions">
                        <button class="button button--orange" type="submit">{{ $editing ? 'Guardar cambios' : 'Agregar a la liga' }}</button>
                        @if ($editing)
                            <a class="text-link" href="{{ route('teams.index') }}">Cancelar</a>
                        @endif
                    </div>
                </form>
            @endcan

            <div class="team-grid">
                @forelse ($teams as $team)
                    <article class="team-card">
                        <div class="team-card__top">
                            <span class="team-mark" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($team->ciudad, 0, 2)) }}</span>
                            <span class="team-badge {{ $team->es_inventado ? 'team-badge--new' : 'team-badge--nba' }}">
                                {{ $team->es_inventado ? 'Nueva franquicia' : 'NBA' }}
                            </span>
                        </div>
                        <div class="team-card__body">
                            <p class="team-card__city">{{ $team->ciudad }}</p>
                            <h3>{{ $team->nombre }}</h3>
                            <p class="team-card__coach">{{ $team->entrenador === 'Por anunciar' ? 'Staff técnico por anunciar' : $team->entrenador }}</p>
                        </div>
                        @can('manage-league')
                            <div class="team-card__actions">
                                <a href="{{ route('teams.edit', $team) }}">Editar <span aria-hidden="true">↗</span></a>
                                <form method="POST" action="{{ route('teams.destroy', $team) }}"
                                      onsubmit="return confirm('¿Eliminar este equipo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Eliminar</button>
                                </form>
                            </div>
                        @endcan
                    </article>
                @empty
                    <div class="empty-state">
                        <h3>La cancha está lista.</h3>
                        <p>Todavía no hay equipos en la liga.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <footer class="league-footer">
            <a class="league-footer__brand" href="{{ route('teams.index') }}">CANCHA<span>.</span></a>
            <p>Fotografía: <a href="https://unsplash.com" target="_blank" rel="noreferrer">Unsplash</a> · El juego empieza en casa.</p>
        </footer>
    </div>
</x-app-layout>
