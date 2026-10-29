<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\LeagueRosterAndScheduleSeeder;
use Database\Seeders\TeamsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeagueManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_player_and_record_game(): void
    {
        $this->actingAs($this->adminUser());

        $home = Team::create([
            'nombre' => 'Cóndores',
            'ciudad' => 'Puebla',
            'entrenador' => 'Ana López',
        ]);

        $away = Team::create([
            'nombre' => 'Tigres',
            'ciudad' => 'Monterrey',
            'entrenador' => 'Luis Pérez',
        ]);

        $this->post(route('players.store'), [
            'team_id' => $home->id,
            'nombre' => 'María García',
            'numero' => 8,
            'posicion' => 'Base',
        ])->assertRedirect(route('players.index'));

        $this->assertDatabaseHas('players', [
            'team_id' => $home->id,
            'nombre' => 'María García',
            'numero' => 8,
        ]);

        $this->post(route('games.store'), [
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'game_date' => '2026-11-01 18:00:00',
            'status' => 'played',
            'home_score' => 72,
            'away_score' => 68,
        ])->assertRedirect(route('games.index'));

        $this->assertDatabaseHas('games', [
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'home_score' => 72,
            'away_score' => 68,
            'status' => 'played',
        ]);
    }

    public function test_guests_can_view_league_panels_without_management_controls(): void
    {
        $this->get(route('teams.index'))
            ->assertOk()
            ->assertSee('Equipos')
            ->assertDontSee('Agregar equipo')
            ->assertDontSee('Editar');

        $this->get(route('players.index'))
            ->assertOk()
            ->assertSee('Jugadores')
            ->assertDontSee('Editar')
            ->assertDontSee('Guardar');

        $this->get(route('games.index'))
            ->assertOk()
            ->assertSee('Partidos y marcadores')
            ->assertDontSee('Editar')
            ->assertDontSee('Guardar partido');
    }

    public function test_guests_cannot_create_or_edit_league_content(): void
    {
        $this->post(route('teams.store'))->assertRedirect(route('login'));
        $this->post(route('players.store'))->assertRedirect(route('login'));
        $this->post(route('games.store'))->assertRedirect(route('login'));

        $this->assertDatabaseCount('teams', 0);
        $this->assertDatabaseCount('players', 0);
        $this->assertDatabaseCount('games', 0);
    }

    public function test_authenticated_non_admin_can_view_but_cannot_manage_league_content(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('teams.index'))
            ->assertOk()
            ->assertDontSee('Agregar equipo')
            ->assertDontSee('Editar');

        $this->get(route('players.index'))
            ->assertOk()
            ->assertDontSee('Nuevo jugador')
            ->assertDontSee('Acciones');

        $this->get(route('games.index'))
            ->assertOk()
            ->assertDontSee('Programar partido')
            ->assertDontSee('Acciones');

        $this->post(route('teams.store'), [
            'nombre' => 'No autorizado',
            'ciudad' => 'Puebla',
            'entrenador' => 'No autorizado',
            'es_inventado' => 1,
        ])->assertForbidden();

        $this->assertDatabaseMissing('teams', ['nombre' => 'No autorizado']);
    }

    public function test_only_the_configured_admin_can_manage_league_content(): void
    {
        $this->actingAs($this->adminUser());

        $this->get(route('teams.index'))
            ->assertOk()
            ->assertSee('Agregar equipo');

        $this->post(route('teams.store'), [
            'nombre' => 'Equipo administrador',
            'ciudad' => 'Puebla',
            'entrenador' => 'Entrenadora',
            'es_inventado' => 1,
        ])->assertRedirect(route('teams.index'));

        $this->assertDatabaseHas('teams', ['nombre' => 'Equipo administrador']);
    }

    public function test_nba_roster_and_schedule_seeder_is_repeatable(): void
    {
        $this->seed(LeagueRosterAndScheduleSeeder::class);
        $this->seed(LeagueRosterAndScheduleSeeder::class);

        $this->assertDatabaseCount('teams', 16);
        $this->assertDatabaseCount('players', 20);
        $this->assertDatabaseCount('games', 10);
        $this->assertDatabaseHas('players', [
            'nombre' => 'Stephen Curry',
            'numero' => 30,
        ]);
        $this->assertDatabaseHas('games', [
            'game_date' => '2026-10-31 02:00:00',
            'status' => 'scheduled',
        ]);
    }

    public function test_viewers_see_admin_changes_to_teams_players_and_games(): void
    {
        $this->seed(TeamsSeeder::class);
        $home = Team::where('nombre', 'Celtics')->firstOrFail();
        $away = Team::where('nombre', 'Lakers')->firstOrFail();

        $this->actingAs($this->adminUser());

        $this->post(route('teams.store'), [
            'nombre' => 'Equipo visible',
            'ciudad' => 'Lima',
            'entrenador' => 'Entrenadora',
            'es_inventado' => 1,
        ])->assertRedirect(route('teams.index'));

        $this->post(route('players.store'), [
            'team_id' => $home->id,
            'nombre' => 'Jugador visible',
            'numero' => 42,
            'posicion' => 'Guard',
        ])->assertRedirect(route('players.index'));

        $this->post(route('games.store'), [
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'game_date' => '2026-12-01 19:30:00',
            'status' => 'played',
            'home_score' => 101,
            'away_score' => 98,
        ])->assertRedirect(route('games.index'));

        $this->actingAs(User::factory()->create());

        $this->get(route('teams.index'))
            ->assertOk()
            ->assertSee('Equipo visible');

        $this->get(route('players.index'))
            ->assertOk()
            ->assertSee('Jugador visible')
            ->assertSee('Celtics')
            ->assertDontSee('Acciones');

        $this->get(route('games.index'))
            ->assertOk()
            ->assertSee('Celtics vs. Lakers')
            ->assertSee('101 : 98')
            ->assertDontSee('Acciones');
    }

    public function test_standings_are_calculated_from_played_games_and_ignore_scheduled_games(): void
    {
        $this->seed(TeamsSeeder::class);
        $celtics = Team::where('nombre', 'Celtics')->firstOrFail();
        $lakers = Team::where('nombre', 'Lakers')->firstOrFail();
        $warriors = Team::where('nombre', 'Warriors')->firstOrFail();

        $this->actingAs($this->adminUser());

        $this->post(route('games.store'), [
            'home_team_id' => $celtics->id,
            'away_team_id' => $lakers->id,
            'game_date' => '2026-12-01 19:30:00',
            'status' => 'played',
            'home_score' => 101,
            'away_score' => 98,
        ])->assertRedirect(route('games.index'));

        $this->post(route('games.store'), [
            'home_team_id' => $celtics->id,
            'away_team_id' => $warriors->id,
            'game_date' => '2026-12-02 19:30:00',
            'status' => 'scheduled',
        ])->assertRedirect(route('games.index'));

        $this->actingAs(User::factory()->create());

        $this->get(route('teams.index'))
            ->assertOk()
            ->assertSee('Tabla de posiciones')
            ->assertSee('Celtics')
            ->assertSee('101')
            ->assertSee('Lakers')
            ->assertViewHas('standings', function ($standings) use ($celtics, $lakers, $warriors): bool {
                $celticsStats = $standings->firstWhere('team.id', $celtics->id);
                $lakersStats = $standings->firstWhere('team.id', $lakers->id);
                $warriorsStats = $standings->firstWhere('team.id', $warriors->id);

                return $celticsStats['games_played'] === 1
                    && $celticsStats['wins'] === 1
                    && $celticsStats['points_for'] === 101
                    && $celticsStats['points_against'] === 98
                    && $celticsStats['point_difference'] === 3
                    && $lakersStats['losses'] === 1
                    && $warriorsStats['games_played'] === 0;
            });
    }

    public function test_viewers_can_filter_players_by_team(): void
    {
        $this->seed(TeamsSeeder::class);
        $celtics = Team::where('nombre', 'Celtics')->firstOrFail();
        $lakers = Team::where('nombre', 'Lakers')->firstOrFail();

        Player::create([
            'team_id' => $celtics->id,
            'nombre' => 'Jugador de Boston',
            'numero' => 41,
            'posicion' => 'Guard',
        ]);
        Player::create([
            'team_id' => $lakers->id,
            'nombre' => 'Jugador de Los Angeles',
            'numero' => 42,
            'posicion' => 'Forward',
        ]);

        $this->actingAs(User::factory()->create());

        $this->get(route('players.index', ['team_id' => $celtics->id]))
            ->assertOk()
            ->assertSee('Jugador de Boston')
            ->assertDontSee('Jugador de Los Angeles')
            ->assertSee('Ver todos');
    }

    public function test_teams_page_distinguishes_nba_and_fictional_teams(): void
    {
        $this->seed(TeamsSeeder::class);
        $this->seed(TeamsSeeder::class);
        $this->actingAs($this->adminUser());

        $this->get(route('teams.index'))
            ->assertOk()
            ->assertSee('Celtics')
            ->assertSee('NBA')
            ->assertSee('Coyotes')
            ->assertSee('Nueva franquicia');

        $this->assertDatabaseCount('teams', 16);
    }

    public function test_user_can_create_a_fictional_team(): void
    {
        $this->actingAs($this->adminUser());

        $this->post(route('teams.store'), [
            'nombre' => 'Rayos',
            'ciudad' => 'Toluca',
            'entrenador' => 'Por anunciar',
            'es_inventado' => '1',
        ])->assertRedirect(route('teams.index'));

        $this->assertDatabaseHas('teams', [
            'nombre' => 'Rayos',
            'ciudad' => 'Toluca',
            'es_inventado' => 1,
        ]);
    }

    private function adminUser(): User
    {
        return User::factory()->create(['email' => config('league.admin_email')]);
    }
}
