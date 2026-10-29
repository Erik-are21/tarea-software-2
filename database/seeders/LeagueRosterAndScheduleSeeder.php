<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class LeagueRosterAndScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(TeamsSeeder::class);

        $teams = Team::query()->pluck('id', 'nombre');

        $players = [
            'Celtics' => [
                ['Jayson Tatum', 0, 'Forward'],
                ['Derrick White', 9, 'Guard'],
            ],
            'Nuggets' => [
                ['Nikola Jokic', 15, 'Center'],
                ['Jamal Murray', 27, 'Guard'],
            ],
            'Warriors' => [
                ['Stephen Curry', 30, 'Guard'],
                ['Jimmy Butler III', 10, 'Forward'],
            ],
            'Lakers' => [
                ['Luka Doncic', 77, 'Guard'],
                ['Austin Reaves', 15, 'Guard'],
            ],
            'Heat' => [
                ['Giannis Antetokounmpo', 7, 'Forward'],
                ['Bam Adebayo', 13, 'Center'],
            ],
            'Knicks' => [
                ['Jalen Brunson', 11, 'Guard'],
                ['Karl-Anthony Towns', 32, 'Center'],
            ],
            'Bulls' => [
                ['Josh Giddey', 3, 'Guard'],
                ['Norman Powell', 24, 'Guard'],
            ],
            'Mavericks' => [
                ['Kyrie Irving', 11, 'Guard'],
                ['Cooper Flagg', 32, 'Forward'],
            ],
            'Suns' => [
                ['Devin Booker', 15, 'Guard'],
                ['Jalen Green', 4, 'Guard'],
            ],
            'Spurs' => [
                ['Victor Wembanyama', 1, 'Forward'],
                ["De'Aaron Fox", 4, 'Guard'],
            ],
        ];

        foreach ($players as $teamName => $roster) {
            $teamId = $this->teamId($teams, $teamName);

            foreach ($roster as [$name, $number, $position]) {
                Player::updateOrCreate(
                    ['team_id' => $teamId, 'numero' => $number],
                    ['nombre' => $name, 'posicion' => $position],
                );
            }
        }

        // ESPN schedule timestamps are stored as UTC.
        $games = [
            ['Bulls', 'Celtics', '2026-10-30 23:30:00'],
            ['Lakers', 'Warriors', '2026-10-31 02:00:00'],
            ['Nuggets', 'Suns', '2026-10-31 02:00:00'],
            ['Suns', 'Warriors', '2026-11-03 03:00:00'],
            ['Heat', 'Lakers', '2026-11-03 03:00:00'],
            ['Bulls', 'Knicks', '2026-11-05 00:30:00'],
            ['Mavericks', 'Spurs', '2026-11-05 02:30:00'],
            ['Heat', 'Warriors', '2026-11-06 03:00:00'],
            ['Spurs', 'Suns', '2026-11-10 02:00:00'],
            ['Spurs', 'Bulls', '2026-11-12 01:00:00'],
        ];

        foreach ($games as [$awayTeam, $homeTeam, $gameDate]) {
            $homeTeamId = $this->teamId($teams, $homeTeam);
            $awayTeamId = $this->teamId($teams, $awayTeam);

            Game::updateOrCreate(
                [
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'game_date' => $gameDate,
                ],
                ['status' => 'scheduled', 'home_score' => null, 'away_score' => null],
            );
        }
    }

    /**
     * @param Collection<string, int> $teams
     */
    private function teamId(Collection $teams, string $teamName): int
    {
        $teamId = $teams->get($teamName);

        if ($teamId === null) {
            throw new RuntimeException("The NBA team [{$teamName}] is missing from the teams table.");
        }

        return $teamId;
    }
}
