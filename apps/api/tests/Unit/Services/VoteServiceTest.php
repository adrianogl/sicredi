<?php

use App\Enums\MemberStatus;
use App\Enums\VoteOption;
use App\Exceptions\DuplicateVoteException;
use App\Exceptions\MemberNotEligibleException;
use App\Exceptions\VotingSessionClosedException;
use App\Models\Vote;
use App\Models\VotingSession;
use App\Repositories\Contracts\VoteRepositoryInterface;
use App\Services\UserInfoClient;
use App\Services\VoteService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Carbon;

function makeOpenSession(): VotingSession
{
    $session = new VotingSession;
    $session->id = 1;
    $session->motion_id = 1;
    $session->opened_at = Carbon::now()->subMinute();
    $session->closes_at = Carbon::now()->addMinute();

    return $session;
}

function makeClosedSession(): VotingSession
{
    $session = new VotingSession;
    $session->id = 2;
    $session->motion_id = 1;
    $session->opened_at = Carbon::now()->subMinutes(5);
    $session->closes_at = Carbon::now()->subMinute();

    return $session;
}

it('throws VotingSessionClosedException when session is closed', function (): void {
    $userInfo = Mockery::mock(UserInfoClient::class);
    $userInfo->shouldNotReceive('statusFor');

    $voteRepo = Mockery::mock(VoteRepositoryInterface::class);
    $voteRepo->shouldNotReceive('createFor');

    $service = new VoteService($userInfo, $voteRepo);

    $service->register(makeClosedSession(), 'm1', VoteOption::Yes);
})->throws(VotingSessionClosedException::class);

it('throws MemberNotEligibleException when user-info returns Ineligible', function (): void {
    $userInfo = Mockery::mock(UserInfoClient::class);
    $userInfo->shouldReceive('statusFor')->once()->with('m1')->andReturn(MemberStatus::Ineligible);

    $voteRepo = Mockery::mock(VoteRepositoryInterface::class);
    $voteRepo->shouldNotReceive('createFor');

    $service = new VoteService($userInfo, $voteRepo);

    $service->register(makeOpenSession(), 'm1', VoteOption::Yes);
})->throws(MemberNotEligibleException::class);

it('throws MemberNotEligibleException when user-info returns null (404)', function (): void {
    $userInfo = Mockery::mock(UserInfoClient::class);
    $userInfo->shouldReceive('statusFor')->once()->andReturn(null);

    $voteRepo = Mockery::mock(VoteRepositoryInterface::class);
    $voteRepo->shouldNotReceive('createFor');

    $service = new VoteService($userInfo, $voteRepo);

    $service->register(makeOpenSession(), 'ghost', VoteOption::No);
})->throws(MemberNotEligibleException::class);

it('throws DuplicateVoteException when repository raises UniqueConstraintViolation', function (): void {
    $userInfo = Mockery::mock(UserInfoClient::class);
    $userInfo->shouldReceive('statusFor')->once()->andReturn(MemberStatus::Eligible);

    $voteRepo = Mockery::mock(VoteRepositoryInterface::class);
    $voteRepo->shouldReceive('createFor')->once()->andThrow(
        new UniqueConstraintViolationException('mysql', 'insert', [], new Exception('duplicate'))
    );

    $service = new VoteService($userInfo, $voteRepo);

    $service->register(makeOpenSession(), 'm1', VoteOption::Yes);
})->throws(DuplicateVoteException::class);

it('returns the persisted Vote on happy path', function (): void {
    $userInfo = Mockery::mock(UserInfoClient::class);
    $userInfo->shouldReceive('statusFor')->once()->with('m1')->andReturn(MemberStatus::Eligible);

    $persisted = new Vote;
    $persisted->id = 42;
    $persisted->voting_session_id = 1;
    $persisted->member_id = 'm1';
    $persisted->option = VoteOption::Yes;

    $voteRepo = Mockery::mock(VoteRepositoryInterface::class);
    $voteRepo->shouldReceive('createFor')->once()->andReturn($persisted);

    $service = new VoteService($userInfo, $voteRepo);

    $vote = $service->register(makeOpenSession(), 'm1', VoteOption::Yes);

    expect($vote->id)->toBe(42)
        ->and($vote->option)->toBe(VoteOption::Yes);
});
