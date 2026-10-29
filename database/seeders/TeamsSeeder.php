<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamsSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            ['nombre' => 'Celtics', 'ciudad' => 'Boston', 'es_inventado' => false],
            ['nombre' => 'Nuggets', 'ciudad' => 'Denver', 'es_inventado' => false],
            ['nombre' => 'Warriors', 'ciudad' => 'San Francisco', 'es_inventado' => false],
            ['nombre' => 'Lakers', 'ciudad' => 'Los Angeles', 'es_inventado' => false],
            ['nombre' => 'Heat', 'ciudad' => 'Miami', 'es_inventado' => false],
            ['nombre' => 'Knicks', 'ciudad' => 'New York', 'es_inventado' => false],
            ['nombre' => 'Bulls', 'ciudad' => 'Chicago', 'es_inventado' => false],
            ['nombre' => 'Mavericks', 'ciudad' => 'Dallas', 'es_inventado' => false],
            ['nombre' => 'Suns', 'ciudad' => 'Phoenix', 'es_inventado' => false],
            ['nombre' => 'Spurs', 'ciudad' => 'San Antonio', 'es_inventado' => false],
            ['nombre' => 'Coyotes', 'ciudad' => 'Saltillo', 'es_inventado' => true],
            ['nombre' => 'Navegantes', 'ciudad' => 'La Paz', 'es_inventado' => true],
            ['nombre' => 'Tormenta', 'ciudad' => 'Xalapa', 'es_inventado' => true],
            ['nombre' => 'Aurora', 'ciudad' => 'Mérida', 'es_inventado' => true],
            ['nombre' => 'Truenos', 'ciudad' => 'Chihuahua', 'es_inventado' => true],
            ['nombre' => 'Fieras', 'ciudad' => 'León', 'es_inventado' => true],
        ];

        foreach ($teams as $team) {
            Team::updateOrCreate(
                ['nombre' => $team['nombre'], 'ciudad' => $team['ciudad']],
                [
                    'entrenador' => 'Por anunciar',
                    'es_inventado' => $team['es_inventado'],
                ],
            );
        }
    }
}
