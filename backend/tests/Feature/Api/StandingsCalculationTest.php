<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Team;
use App\Models\Game;

class StandingsCalculationTest extends TestCase
{
    // Esta línea es clave: resetea la base de datos
    // (ejecuta migrate:fresh) antes de cada test.
    use RefreshDatabase;

    /**
     * Prueba que los puntos se calculan correctamente
     * después de una victoria y un empate.
     */
    public function test_points_are_calculated_correctly_for_win_and_draw(): void
    {
        // 1. ARRANGE (Preparar)
        // Crea dos equipos
        $teamA = Team::create(['name' => 'Equipo Alfa']);
        $teamB = Team::create(['name' => 'Equipo Beta']);

        // Crea el Partido 1 (para la victoria)
        $match1 = Game::create([
            'home_team_id' => $teamA->id,
            'away_team_id' => $teamB->id,
        ]);
        
        // Crea el Partido 2 (para el empate)
        $match2 = Game::create([
            'home_team_id' => $teamA->id,
            'away_team_id' => $teamB->id,
        ]);

        // 2. ACT (Actuar)
        
        // Registra el resultado del Partido 1 (Victoria de A: 2-0)
        $this->postJson('/api/matches/' . $match1->id . '/result', [
            'home_score' => 2,
            'away_score' => 0,
        ]);
        
        // Registra el resultado del Partido 2 (Empate: 1-1)
        $this->postJson('/api/matches/' . $match2->id . '/result', [
            'home_score' => 1,
            'away_score' => 1,
        ]);

        // 3. ASSERT (Verificar)
        
        // Obtenemos la clasificación actualizada
        $response = $this->getJson('/api/standings');

        $response->assertStatus(200);
        
        // Convertimos el JSON de respuesta en un array
        $standings = $response->json();

        // Buscamos las estadísticas de nuestros equipos
        // (usamos $standings[0] y [1] porque sabemos que son los únicos)
        
        // El orden esperado es A (4 pts) y luego B (1 pt)
        $statsTeamA = $standings[0]['nombre'] === 'Equipo Alfa' ? $standings[0] : $standings[1];
        $statsTeamB = $standings[1]['nombre'] === 'Equipo Beta' ? $standings[1] : $standings[0];

        // Verificamos que el Equipo A está primero (tiene más puntos)
        $this->assertEquals('Equipo Alfa', $standings[0]['nombre']);
        $this->assertEquals('Equipo Beta', $standings[1]['nombre']);

        // Verificamos los puntos del Equipo A
        // (1 victoria + 1 empate = 3 + 1 = 4 puntos)
        $this->assertEquals(4, $statsTeamA['puntos']);
        $this->assertEquals(2, $statsTeamA['partidos_jugados']);
        $this->assertEquals(1, $statsTeamA['ganados']);
        $this->assertEquals(1, $statsTeamA['empatados']);
        $this->assertEquals(0, $statsTeamA['perdidos']);
        $this->assertEquals(3, $statsTeamA['goles_favor']); // (2+1)

        // Verificamos los puntos del Equipo B
        // (1 derrota + 1 empate = 0 + 1 = 1 punto)
        $this->assertEquals(1, $statsTeamB['puntos']);
        $this->assertEquals(2, $statsTeamB['partidos_jugados']);
        $this->assertEquals(0, $statsTeamB['ganados']);
        $this->assertEquals(1, $statsTeamB['empatados']);
        $this->assertEquals(1, $statsTeamB['perdidos']);
        $this->assertEquals(3, $statsTeamB['goles_contra']); // (2+1)
    }
}