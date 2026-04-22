<?php

namespace App\Services;

use App\Enums\MemberStatus;
use App\Enums\VoteOption;
use App\Exceptions\DuplicateVoteException;
use App\Exceptions\MemberNotEligibleException;
use App\Exceptions\VotingSessionClosedException;
use App\Models\Vote;
use App\Models\VotingSession;
use App\Repositories\Contracts\VoteRepositoryInterface;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Log;

class VoteService
{
    public function __construct(
        private readonly UserInfoClient $userInfo,
        private readonly VoteRepositoryInterface $voteRepository,
    ) {}

    public function register(VotingSession $session, string $memberId, VoteOption $option): Vote
    {
        if (! $session->isOpen()) {
            throw new VotingSessionClosedException;
        }

        $status = $this->userInfo->statusFor($memberId);
        if ($status !== MemberStatus::Eligible) {
            Log::info('vote.blocked', [
                'session_id' => $session->id,
                'member_hash' => self::hashMemberId($memberId),
                'reason' => $status instanceof MemberStatus ? $status->value : 'MEMBER_NOT_FOUND',
            ]);

            throw new MemberNotEligibleException;
        }

        try {
            $vote = $this->voteRepository->createFor($session, $memberId, $option);
        } catch (UniqueConstraintViolationException) {
            throw new DuplicateVoteException;
        }

        Log::info('vote.registered', [
            'vote_id' => $vote->id,
            'session_id' => $session->id,
            'member_hash' => self::hashMemberId($memberId),
            'option' => $option->value,
        ]);

        return $vote;
    }

    private static function hashMemberId(string $memberId): string
    {
        return hash('sha256', $memberId);
    }
}
