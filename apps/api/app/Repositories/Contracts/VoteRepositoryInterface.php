<?php

namespace App\Repositories\Contracts;

use App\Enums\VoteOption;
use App\Models\Motion;
use App\Models\Vote;
use App\Models\VotingSession;

interface VoteRepositoryInterface
{
    public function createFor(VotingSession $session, string $memberId, VoteOption $option): Vote;

    /**
     * @return array{yes: int, no: int}
     */
    public function countsForMotion(Motion $motion): array;
}
