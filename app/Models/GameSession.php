<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class GameSession extends Model
{
    protected $fillable = ['user_id', 'name', 'status', 'lives', 'total_mistakes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the game session is still playable (has lives remaining)
     */
    public function isPlayable(): bool
    {
        return $this->lives > 0;
    }

    /**
     * Deduct one life from the game session
     */
    public function deductLife(): void
    {
        $this->lives = max(0, $this->lives - 1);
        $this->save();
    }

    /**
     * Increment total mistakes
     */
    public function incrementMistakes(int $count = 1): void
    {
        $this->total_mistakes += $count;
        $this->save();
    }
}
