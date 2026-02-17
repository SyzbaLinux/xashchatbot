<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    protected $fillable = [
        'phone_number',
        'step',
        'session_data',
        'expires_at',
    ];

    protected $casts = [
        'session_data' => 'array',
        'expires_at'   => 'datetime',
    ];

    /**
     * Get or create a session for the given phone number.
     * Expired sessions are reset to idle.
     */
    public static function getOrCreate(string $phone): static
    {
        $session = static::where('phone_number', $phone)->first();

        if (!$session) {
            return static::create([
                'phone_number' => $phone,
                'step'         => 'idle',
                'session_data' => [],
                'expires_at'   => now()->addMinutes(30),
            ]);
        }

        // Reset expired sessions
        if ($session->expires_at && $session->expires_at->isPast()) {
            $session->update([
                'step'         => 'idle',
                'session_data' => [],
                'expires_at'   => now()->addMinutes(30),
            ]);
        }

        return $session;
    }

    /**
     * Advance the session to a new step and optionally merge data.
     */
    public function advance(string $step, array $data = []): void
    {
        $merged = array_merge($this->session_data ?? [], $data);

        $this->update([
            'step'         => $step,
            'session_data' => $merged,
            'expires_at'   => now()->addMinutes(30),
        ]);
    }

    /**
     * Reset the session back to idle.
     */
    public function reset(): void
    {
        $this->update([
            'step'         => 'idle',
            'session_data' => [],
            'expires_at'   => now()->addMinutes(30),
        ]);
    }
}
