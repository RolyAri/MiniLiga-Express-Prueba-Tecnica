<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Game;

class MatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Game::with('homeTeam', 'awayTeam');

        if ($request->query('played') === 'false') {
            $query->whereNull('played_at');
        }

        return $query->orderBy('id', 'asc')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // POST /api/matches/{id}/result
    public function result(Request $request, $id) 
    {
        $validated = $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
        ]);

        // Usamos 'with' para cargar las relaciones y evitar más consultas
        $match = Game::with('homeTeam', 'awayTeam')->findOrFail($id);

        // Evitar registrar el resultado dos veces
        if ($match->played_at) {
            return response()->json(['message' => 'El resultado de este partido ya fue registrado.'], 409); // 409 Conflict
        }

        try {
            DB::beginTransaction();

            // Actualizar el partido
            $match->update([
                'home_score' => $validated['home_score'],
                'away_score' => $validated['away_score'],
                'played_at' => now(),
            ]);

            // Actualizar equipo LOCAL (homeTeam)
            // (Accedemos a la relación cargada 'homeTeam')
            $match->homeTeam->goals_for += $validated['home_score'];
            $match->homeTeam->goals_against += $validated['away_score'];
            $match->homeTeam->save();

            // Actualizar equipo VISITANTE (awayTeam)
            // (Accedemos a la relación cargada 'awayTeam')
            $match->awayTeam->goals_for += $validated['away_score'];
            $match->awayTeam->goals_against += $validated['home_score'];
            $match->awayTeam->save();
            
            DB::commit(); // Confirmar los cambios

            return response()->json($match);

        } catch (\Exception $e) {
            DB::rollBack(); // Revertir en caso de error
            return response()->json(['message' => 'Error al guardar el resultado.', 'error' => $e->getMessage()], 500);
        }
    }
}
