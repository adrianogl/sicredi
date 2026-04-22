<?php

use App\Models\Vote;
use App\Models\VotingSession;
use Illuminate\Support\Carbon;

it('registers a valid vote with 201', function (): void {
    $session = VotingSession::factory()->create();

    $this->postJson("/api/v1/sessions/{$session->id}/votes", [
        'member_id' => '12345678900',
        'option' => 'Yes',
    ])->assertCreated()
        ->assertJsonPath('data.option', 'Yes')
        ->assertJsonPath('data.member_id', '12345678900');

    $this->assertDatabaseHas('votes', [
        'voting_session_id' => $session->id,
        'member_id' => '12345678900',
        'option' => 'Yes',
    ]);
});

it('returns 422 for an invalid option', function (): void {
    $session = VotingSession::factory()->create();

    $this->postJson("/api/v1/sessions/{$session->id}/votes", [
        'member_id' => '1',
        'option' => 'Maybe',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['option']);
});

it('returns 409 on duplicate vote from the same member', function (): void {
    $session = VotingSession::factory()->create();
    Vote::factory()->create([
        'voting_session_id' => $session->id,
        'member_id' => 'ABC',
        'option' => 'Yes',
    ]);

    $this->postJson("/api/v1/sessions/{$session->id}/votes", [
        'member_id' => 'ABC',
        'option' => 'No',
    ])->assertConflict()
        ->assertJsonPath('code', 'DUPLICATE_VOTE');
});

it('returns 409 when the voting session is closed', function (): void {
    $session = VotingSession::factory()->closed()->create();

    $this->postJson("/api/v1/sessions/{$session->id}/votes", [
        'member_id' => 'XYZ',
        'option' => 'Yes',
    ])->assertConflict()
        ->assertJsonPath('code', 'SESSION_CLOSED');
});

it('blocks voting after the session expires in real time', function (): void {
    Carbon::setTestNow('2026-01-01 12:00:00');
    $session = VotingSession::factory()->create([
        'opened_at' => Carbon::now(),
        'closes_at' => Carbon::now()->addMinute(),
    ]);

    Carbon::setTestNow('2026-01-01 12:02:00');

    $this->postJson("/api/v1/sessions/{$session->id}/votes", [
        'member_id' => 'LATE',
        'option' => 'Yes',
    ])->assertConflict()
        ->assertJsonPath('code', 'SESSION_CLOSED');
});
