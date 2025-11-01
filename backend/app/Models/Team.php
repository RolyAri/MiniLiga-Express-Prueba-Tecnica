<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Game;

class Team extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'goals_for',
        'goals_against',
    ];

    /**
     * Get the matches played by the team as home team.
     */
    public function homeMatches(): HasMany
    {
        return $this->hasMany(Game::class, 'home_team_id');
    }

    /**
     * Get the matches played by the team as away team.
     */
    public function awayMatches(): HasMany
    {
        return $this->hasMany(Game::class, 'away_team_id');
    }
}