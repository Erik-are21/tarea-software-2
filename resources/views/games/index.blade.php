<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Partidos y marcadores</h2></x-slot>

    <main class="max-w-7xl mx-auto p-6 space-y-6">
        @if (session('success')) <p class="text-green-700">{{ session('success') }}</p> @endif

        @can('manage-league')
            <form method="POST" action="{{ $editing ? route('games.update', $editing) : route('games.store') }}"
                  class="bg-white p-6 shadow rounded space-y-4">
                @csrf
                @if ($editing) @method('PUT') @endif
                <h3 class="font-semibold">{{ $editing ? 'Editar partido o marcador' : 'Programar partido' }}</h3>

                @foreach (['home_team_id' => 'Equipo local', 'away_team_id' => 'Equipo visitante'] as $field => $label)
                    <label class="block">{{ $label }}
                        <select class="block mt-1 w-full rounded border-gray-300" name="{{ $field }}" required>
                            <option value="">Selecciona un equipo</option>
                            @foreach ($teams as $team)
                                <option value="{{ $team->id }}"
                                    @selected(old($field, $editing?->{$field}) == $team->id)>
                                    {{ $team->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error($field) <span class="text-red-600">{{ $message }}</span> @enderror
                    </label>
                @endforeach

                <label class="block">Fecha y hora
                    <input class="block mt-1 w-full rounded border-gray-300" type="datetime-local"
                           name="game_date"
                           value="{{ old('game_date', $editing?->game_date?->format('Y-m-d\TH:i')) }}" required>
                    @error('game_date') <span class="text-red-600">{{ $message }}</span> @enderror
                </label>

                <label class="block">Estado
                    <select class="block mt-1 w-full rounded border-gray-300" name="status" required>
                        <option value="scheduled" @selected(old('status', $editing?->status ?? 'scheduled') === 'scheduled')>Programado</option>
                        <option value="played" @selected(old('status', $editing?->status) === 'played')>Jugado</option>
                    </select>
                    @error('status') <span class="text-red-600">{{ $message }}</span> @enderror
                </label>

                <div class="grid grid-cols-2 gap-4">
                    <label class="block">Puntos local
                        <input class="block mt-1 w-full rounded border-gray-300" type="number" min="0" max="999"
                               name="home_score" value="{{ old('home_score', $editing?->home_score) }}">
                        @error('home_score') <span class="text-red-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">Puntos visitante
                        <input class="block mt-1 w-full rounded border-gray-300" type="number" min="0" max="999"
                               name="away_score" value="{{ old('away_score', $editing?->away_score) }}">
                        @error('away_score') <span class="text-red-600">{{ $message }}</span> @enderror
                    </label>
                </div>

                <button class="px-4 py-2 bg-blue-700 text-white rounded">Guardar partido</button>
                @if ($editing) <a href="{{ route('games.index') }}">Cancelar</a> @endif
            </form>
        @endcan

        <div class="bg-white p-6 shadow rounded">
            <table class="w-full text-left">
                <thead><tr><th>Fecha y hora (UTC)</th><th>Partido</th><th>Marcador</th><th>Estado</th>@can('manage-league')<th>Acciones</th>@endcan</tr></thead>
                <tbody>
                @forelse ($games as $game)
                    <tr class="border-t">
                        <td class="py-2">{{ $game->game_date->format('d/m/Y H:i') }}</td>
                        <td>{{ $game->homeTeam->nombre }} vs. {{ $game->awayTeam->nombre }}</td>
                        <td>{{ $game->home_score ?? '—' }} : {{ $game->away_score ?? '—' }}</td>
                        <td>{{ $game->status === 'played' ? 'Jugado' : 'Programado' }}</td>
                        @can('manage-league')
                            <td class="space-x-3">
                                <a class="text-blue-700" href="{{ route('games.edit', $game) }}">Editar</a>
                                <form class="inline" method="POST" action="{{ route('games.destroy', $game) }}"
                                      onsubmit="return confirm('¿Eliminar este partido?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-700">Eliminar</button>
                                </form>
                            </td>
                        @endcan
                    </tr>
                @empty
                    <tr><td colspan="{{ auth()->user()?->can('manage-league') ? 5 : 4 }}" class="py-3">Todavía no hay partidos.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </main>
</x-app-layout>
