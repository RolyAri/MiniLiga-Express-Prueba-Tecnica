<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Game; 
use Illuminate\Http\Request;

class StandingsController extends Controller
{
    // GET /api/standings
    public function index()
    {
        // 1. Obtener todos los equipos y partidos jugados
        $teams = Team::all();
        // Usamos 'Match' (el modelo correcto)
        $playedMatches = Game::whereNotNull('played_at')->get();

        // 2. Inicializar estadísticas (CON NOMBRES CAMBIADOS)
        $standings = $teams->mapWithKeys(function ($team) {
            return [$team->id => [
                'nombre' => $team->name, // 'name' -> 'nombre'
                'partidos_jugados' => 0, // 'PJ' -> 'partidos_jugados'
                'ganados' => 0,          // 'G' -> 'ganados'
                'empatados' => 0,        // 'E' -> 'empatados'
                'perdidos' => 0,         // 'P' -> 'perdidos'
                'goles_favor' => $team->goals_for,       // 'GF' -> 'goles_favor'
                'goles_contra' => $team->goals_against,  // 'GC' -> 'goles_contra'
                'diferencia_goles' => $team->goals_for - $team->goals_against, // 'DG' -> 'diferencia_goles'
                'puntos' => 0,           // 'Pts' -> 'puntos'
            ]];
        })->all(); // Convertir a array

        // 3. Calcular W/D/L y Puntos (CON NOMBRES CAMBIADOS)
        foreach ($playedMatches as $match) {
            $homeStats = &$standings[$match->home_team_id];
            $awayStats = &$standings[$match->away_team_id];

            $homeStats['partidos_jugados']++;
            $awayStats['partidos_jugados']++;

            if ($match->home_score > $match->away_score) {
                // Gana Local
                $homeStats['ganados']++;
                $homeStats['puntos'] += 3;
                $awayStats['perdidos']++;
            } elseif ($match->home_score < $match->away_score) {
                // Gana Visitante
                $awayStats['ganados']++;
                $awayStats['puntos'] += 3;
                $homeStats['perdidos']++;
            } else {
                // Empate
                $homeStats['empatados']++;
                $homeStats['puntos'] += 1;
                $awayStats['empatados']++;
                $awayStats['puntos'] += 1;
            }
        }

        // 4. Ordenar la clasificación (CON NOMBRES CAMBIADOS)
        $sortedStandings = array_values($standings);
        usort($sortedStandings, function ($a, $b) {
            if ($a['puntos'] != $b['puntos']) {
                return $b['puntos'] <=> $a['puntos']; // Ordenar por 'puntos'
            }
            if ($a['diferencia_goles'] != $b['diferencia_goles']) {
                return $b['diferencia_goles'] <=> $a['diferencia_goles']; // Ordenar por 'diferencia_goles'
            }
            return $b['goles_favor'] <=> $a['goles_favor']; // Ordenar por 'goles_favor'
        });

        return response()->json($sortedStandings);
    }
}