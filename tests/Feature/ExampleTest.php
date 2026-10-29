<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_redirects_to_the_team_panel(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('teams.index', absolute: false));
    }
}
