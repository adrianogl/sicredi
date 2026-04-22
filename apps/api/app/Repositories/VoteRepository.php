<?php

namespace App\Repositories;

use App\Enums\VoteOption;
use App\Models\Motion;
use App\Models\Vote;
use App\Models\VotingSession;
use App\Repositories\Contracts\VoteRepositoryInterface;

class VoteRepository implements VoteRepositoryInterface
{
    public function createFor(VotingSession $session, string $memberId, VoteOption $option): Vote
    {
        return $session->votes()->create([
            'member_id' => $memberId,
            'option' => $option,
        ]);
    }

    /**
     * @return array{yes: int, no: int}
     */
    public function countsForMotion(Motion $motion): array
    {
        $counts = Vote::query()
            ->whereIn('voting_session_id', $motion->sessions()->select('id'))
            ->groupBy('option')
            ->selectRaw('option, COUNT(*) as total')
            ->pluck('total', 'option');

        return [
            'yes' => (int) ($counts[VoteOption::Yes->value] ?? 0),
            'no' => (int) ($counts[VoteOption::No->value] ?? 0),
        ];
    }
}
