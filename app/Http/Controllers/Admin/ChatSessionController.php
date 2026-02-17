<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use Inertia\Inertia;

class ChatSessionController extends Controller
{
    public function index()
    {
        $sessions = ChatSession::latest('updated_at')
            ->paginate(20)
            ->through(fn($s) => [
                'id'           => $s->id,
                'phone_number' => $s->phone_number,
                'step'         => $s->step,
                'session_data' => $s->session_data,
                'expires_at'   => $s->expires_at?->format('M d, Y H:i'),
                'updated_at'   => $s->updated_at->format('M d, Y H:i'),
                'is_expired'   => $s->expires_at?->isPast() ?? false,
            ]);

        $stats = [
            'total'   => ChatSession::count(),
            'active'  => ChatSession::where('expires_at', '>', now())->where('step', '!=', 'idle')->count(),
            'idle'    => ChatSession::where('step', 'idle')->count(),
            'expired' => ChatSession::where('expires_at', '<=', now())->count(),
        ];

        return Inertia::render('Admin/ChatSessions', compact('sessions', 'stats'));
    }
}
