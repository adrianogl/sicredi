<?php

namespace App\Repositories;

use App\Models\Motion;
use App\Models\VotingSession;
use App\Repositories\Contracts\VotingSessionRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class VotingSessionRepository implements VotingSessionRepositoryInterface
{
    public const DEFAULT_DURATION_SECONDS = 60;

    public function openFor(Motion $motion, ?int $durationSeconds = null): VotingSession
    {
        $duration = $durationSeconds ?? self::DEFAULT_DURATION_SECONDS;
        $opening = Carbon::now();

        $session = $motion->sessions()->create([
            'opened_at' => $opening,
            'closes_at' => $opening->copy()->addSeconds($duration),
        ]);

        Log::info('session.opened', [
            'session_id' => $session->id,
            'motion_id' => $motion->id,
            'duration_seconds' => $duration,
        ]);

        return $session;
    }
}
