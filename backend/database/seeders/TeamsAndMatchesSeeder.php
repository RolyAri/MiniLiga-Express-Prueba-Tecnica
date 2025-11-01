<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Team;
use App\Models\Game;

class TeamsAndMatchesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = collect(['Dragons','Sharks','Tigers','Wolves'])
            ->map(fn($n) => Team::create(['name' => $n]));

        // crea 2-3 partidos sin resultado
        Game::create([
            'home_team_id' => $teams[0]->id, 
            'away_team_id' => $teams[1]->id
        ]);
        Game::create([
            'home_team_id' => $teams[2]->id, 
            'away_team_id' => $teams[3]->id
        ]);
    }
}
