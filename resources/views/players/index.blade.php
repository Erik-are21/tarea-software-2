<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Jugadores</h2></x-slot>

    <main class="max-w-7xl mx-auto p-6 space-y-6">
        @if (session('success')) <p class="text-green-700">{{ session('success') }}</p> @endif

        <form method="GET" action="{{ route('players.index') }}" class="bg-white p-6 shadow rounded flex flex-wrap items-end gap-4">
            <label class="block">Filtrar por equipo
                <select class="block mt-1 w-full rounded border-gray-300" name="team_id">
                    <option value="">Todos los equipos</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" @selected($selectedTeamId === $team->id)>
                            {{ $team->nombre }}
                        </option>
                    @endforeach
                </select>
            </label>
            <button class="px-4 py-2 bg-gray-800 text-white rounded" type="submit">Ver jugadores</button>
            @if ($selectedTeamId)
                <a class="text-blue-700 underline" href="{{ route('players.index') }}">Ver todos</a>
            @endif
        </form>

        @can('manage-league')
            <form method="POST" action="{{ $editing ? route('players.update', $editing) : route('players.store') }}"
                  class="bg-white p-6 shadow rounded space-y-4">
                @csrf
                @if ($editing) @method('PUT') @endif
                <h3 class="font-semibold">{{ $editing ? 'Editar jugador' : 'Nuevo jugador' }}</h3>

                <label class="block">Equipo
                    <select class="block mt-1 w-full rounded border-gray-300" name="team_id" required>
                        <option value="">Selecciona un equipo</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" @selected(old('team_id', $editing?->team_id) == $team->id)>
                                {{ $team->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('team_id') <span class="text-red-600">{{ $message }}</span> @enderror
                </label>

                <label class="block">Nombre
                    <input class="block mt-1 w-full rounded border-gray-300" name="nombre"
                           value="{{ old('nombre', $editing?->nombre) }}" required>
                    @error('nombre') <span class="text-red-600">{{ $message }}</span> @enderror
                </label>

                <label class="block">Número
                    <input class="block mt-1 w-full rounded border-gray-300" type="number" min="0" max="99"
                           name="numero" value="{{ old('numero', $editing?->numero) }}" required>
                    @error('numero') <span class="text-red-600">{{ $message }}</span> @enderror
                </label>

                <label class="block">Posición
                    <input class="block mt-1 w-full rounded border-gray-300" name="posicion"
                           value="{{ old('posicion', $editing?->posicion) }}" required>
                    @error('posicion') <span class="text-red-600">{{ $message }}</span> @enderror
                </label>

                <button class="px-4 py-2 bg-blue-700 text-white rounded">Guardar</button>
                @if ($editing) <a href="{{ route('players.index') }}">Cancelar</a> @endif
            </form>
        @endcan

        <div class="bg-white p-6 shadow rounded">
            <table class="w-full text-left">
                <thead><tr><th>Jugador</th><th>Equipo</th><th>Número</th><th>Posición</th>@can('manage-league')<th>Acciones</th>@endcan</tr></thead>
                <tbody>
                @forelse ($players as $player)
                    <tr class="border-t">
                        <td class="py-2">{{ $player->nombre }}</td>
                        <td>{{ $player->team->nombre }}</td>
                        <td>{{ $player->numero }}</td>
                        <td>{{ $player->posicion }}</td>
                        @can('manage-league')
                            <td class="space-x-3">
                                <a class="text-blue-700" href="{{ route('players.edit', $player) }}">Editar</a>
                                <form class="inline" method="POST" action="{{ route('players.destroy', $player) }}"
                                      onsubmit="return confirm('¿Eliminar este jugador?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-700">Eliminar</button>
                                </form>
                            </td>
                        @endcan
                    </tr>
                @empty
                    <tr><td colspan="{{ auth()->user()?->can('manage-league') ? 5 : 4 }}" class="py-3">Todavía no hay jugadores.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </main>
</x-app-layout>
