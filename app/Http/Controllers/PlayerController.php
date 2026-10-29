<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlayerController extends Controller
{
    public function index(): View
    {
        return view('players.index', [
            'players' => Player::with('team')->orderBy('nombre')->get(),
            'teams' => Team::orderBy('nombre')->get(),
            'editing' => null,
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('players.index');
    }

    public function store(Request $request): RedirectResponse
    {
        Player::create($request->validate([
            'team_id' => ['required', 'exists:teams,id'],
            'nombre' => ['required', 'string', 'max:100'],
            'numero' => [
                'required',
                'integer',
                'min:0',
                'max:99',
                Rule::unique('players', 'numero')
                    ->where(fn ($query) => $query->where('team_id', $request->input('team_id'))),
            ],
            'posicion' => ['required', 'string', 'max:30'],
        ]));

        return redirect()->route('players.index')->with('success', 'Jugador creado.');
    }

    public function edit(Player $player): View
    {
        return view('players.index', [
            'players' => Player::with('team')->orderBy('nombre')->get(),
            'teams' => Team::orderBy('nombre')->get(),
            'editing' => $player,
        ]);
    }

    public function update(Request $request, Player $player): RedirectResponse
    {
        $player->update($request->validate([
            'team_id' => ['required', 'exists:teams,id'],
            'nombre' => ['required', 'string', 'max:100'],
            'numero' => [
                'required',
                'integer',
                'min:0',
                'max:99',
                Rule::unique('players', 'numero')
                    ->where(fn ($query) => $query->where('team_id', $request->input('team_id')))
                    ->ignore($player->id),
            ],
            'posicion' => ['required', 'string', 'max:30'],
        ]));

        return redirect()->route('players.index')->with('success', 'Jugador actualizado.');
    }

    public function destroy(Player $player): RedirectResponse
    {
        $player->delete();

        return redirect()->route('players.index')->with('success', 'Jugador eliminado.');
    }
}
