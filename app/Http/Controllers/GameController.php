<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class GameController extends Controller
{
    public function index(): View
    {
        return view('games.index', [
            'games' => Game::with(['homeTeam', 'awayTeam'])->orderByDesc('game_date')->get(),
            'teams' => Team::orderBy('nombre')->get(),
            'editing' => null,
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('games.index');
    }

    public function store(Request $request): RedirectResponse
    {
        Game::create($this->validatedData($request));

        return redirect()->route('games.index')->with('success', 'Partido guardado.');
    }

    public function edit(Game $game): View
    {
        return view('games.index', [
            'games' => Game::with(['homeTeam', 'awayTeam'])->orderByDesc('game_date')->get(),
            'teams' => Team::orderBy('nombre')->get(),
            'editing' => $game,
        ]);
    }

    public function update(Request $request, Game $game): RedirectResponse
    {
        $game->update($this->validatedData($request, $game));

        return redirect()->route('games.index')->with('success', 'Partido actualizado.');
    }

    public function destroy(Game $game): RedirectResponse
    {
        $game->delete();

        return redirect()->route('games.index')->with('success', 'Partido eliminado.');
    }

    private function validatedData(Request $request, ?Game $game = null): array
    {
        $validator = Validator::make($request->all(), [
            'home_team_id' => ['required', 'exists:teams,id', 'different:away_team_id'],
            'away_team_id' => ['required', 'exists:teams,id'],
            'game_date' => ['required', 'date'],
            'status' => ['required', 'in:scheduled,played'],
            'home_score' => ['nullable', 'required_if:status,played', 'integer', 'min:0', 'max:999'],
            'away_score' => ['nullable', 'required_if:status,played', 'integer', 'min:0', 'max:999'],
        ]);

        $validator->after(function ($validator) use ($request, $game): void {
            $duplicate = Game::query()
                ->where('home_team_id', $request->input('home_team_id'))
                ->where('away_team_id', $request->input('away_team_id'))
                ->where('game_date', $request->input('game_date'))
                ->when($game, fn ($query) => $query->where('id', '!=', $game->id))
                ->exists();

            if ($duplicate) {
                $validator->errors()->add('game_date', 'Ya existe un partido igual en esa fecha.');
            }
        });

        $data = $validator->validate();

        if ($data['status'] === 'scheduled') {
            $data['home_score'] = null;
            $data['away_score'] = null;
        }

        return $data;
    }
}
