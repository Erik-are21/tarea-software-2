<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Database\Seeders\LeagueRosterAndScheduleSeeder;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Las ediciones están reservadas al administrador.');
    }

    public function test_new_users_can_register_and_only_view_league_content(): void
    {
        $this->seed(LeagueRosterAndScheduleSeeder::class);

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('teams.index', absolute: false));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);

        $this->get(route('teams.index'))
            ->assertOk()
            ->assertSee('Celtics')
            ->assertDontSee('Agregar equipo')
            ->assertDontSee('Editar');

        $this->get(route('players.index'))
            ->assertOk()
            ->assertSee('Stephen Curry')
            ->assertSee('Jugadores')
            ->assertDontSee('Acciones');

        $this->get(route('games.index'))
            ->assertOk()
            ->assertSee('Celtics')
            ->assertSee('Warriors')
            ->assertSee('Partidos y marcadores')
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

    public function test_public_registration_cannot_claim_the_admin_email(): void
    {
        $this->from('/register')->post('/register', [
            'name' => 'Unauthorized Admin',
            'email' => config('league.admin_email'),
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['name' => 'Unauthorized Admin']);
    }
}
