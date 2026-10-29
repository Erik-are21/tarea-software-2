<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        $teams = Team::orderBy('nombre')->get();
        $standings = $this->standings($teams);

        return view('teams.index', [
            'teams' => $teams,
            'standings' => $standings,
            'editing' => null,
        ]);
    }

    /**
     * @param  Collection<int, Team>  $teams
     * @return Collection<int, array{
     *     team: Team,
     *     games_played: int,
     *     wins: int,
     *     draws: int,
     *     losses: int,
     *     points_for: int,
     *     points_against: int,
     *     point_difference: int,
     *     win_percentage: float
     * }>
     */
    private function standings(Collection $teams): Collection
    {
        $statistics = $teams->mapWithKeys(fn (Team $team): array => [
            $team->id => [
                'games_played' => 0,
                'wins' => 0,
                'draws' => 0,
                'losses' => 0,
                'points_for' => 0,
                'points_against' => 0,
            ],
        ]);

        Game::query()
            ->where('status', 'played')
            ->whereNotNull('home_score')
            ->whereNotNull('away_score')
            ->get(['home_team_id', 'away_team_id', 'home_score', 'away_score'])
            ->each(function (Game $game) use ($statistics): void {
                $homeStats = $statistics->get($game->home_team_id);
                $awayStats = $statistics->get($game->away_team_id);

                if ($homeStats === null || $awayStats === null) {
                    return;
                }

                $homeStats['games_played']++;
                $homeStats['points_for'] += $game->home_score;
                $homeStats['points_against'] += $game->away_score;

                $awayStats['games_played']++;
                $awayStats['points_for'] += $game->away_score;
                $awayStats['points_against'] += $game->home_score;

                if ($game->home_score > $game->away_score) {
                    $homeStats['wins']++;
                    $awayStats['losses']++;
                } elseif ($game->home_score < $game->away_score) {
                    $awayStats['wins']++;
                    $homeStats['losses']++;
                } else {
                    $homeStats['draws']++;
                    $awayStats['draws']++;
                }

                $statistics->put($game->home_team_id, $homeStats);
                $statistics->put($game->away_team_id, $awayStats);
            });

        return $teams
            ->map(function (Team $team) use ($statistics): array {
                $stats = $statistics->get($team->id);
                $stats['point_difference'] = $stats['points_for'] - $stats['points_against'];
                $stats['win_percentage'] = $stats['games_played'] > 0
                    ? $stats['wins'] / $stats['games_played']
                    : 0;

                return ['team' => $team] + $stats;
            })
            ->sort(function (array $first, array $second): int {
                return ($second['win_percentage'] <=> $first['win_percentage'])
                    ?: ($second['point_difference'] <=> $first['point_difference'])
                    ?: ($second['points_for'] <=> $first['points_for'])
                    ?: strcmp($first['team']->nombre, $second['team']->nombre);
            })
            ->values();
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('teams.index');
    }

    public function store(Request $request): RedirectResponse
    {
        Team::create($request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'ciudad' => ['required', 'string', 'max:100'],
            'entrenador' => ['required', 'string', 'max:100'],
            'es_inventado' => ['required', 'boolean'],
        ]));

        return redirect()->route('teams.index')->with('success', 'Equipo creado.');
    }

    public function edit(Team $team): View
    {
        $teams = Team::orderBy('nombre')->get();

        return view('teams.index', [
            'teams' => $teams,
            'standings' => $this->standings($teams),
            'editing' => $team,
        ]);
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $team->update($request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'ciudad' => ['required', 'string', 'max:100'],
            'entrenador' => ['required', 'string', 'max:100'],
            'es_inventado' => ['required', 'boolean'],
        ]));

        return redirect()->route('teams.index')->with('success', 'Equipo actualizado.');
    }

    public function destroy(Team $team): RedirectResponse
    {
        if ($team->homeGames()->exists() || $team->awayGames()->exists()) {
            return redirect()->route('teams.index')
                ->with('error', 'No se puede eliminar un equipo con partidos registrados.');
        }

        $team->delete();

        return redirect()->route('teams.index')->with('success', 'Equipo eliminado.');
    }
}
