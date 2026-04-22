<?php

use App\Models\Motion;
use App\Models\VotingSession;
use Illuminate\Support\Carbon;

it('opens a session with default duration of 60 seconds', function (): void {
    Carbon::setTestNow('2026-01-01 12:00:00');

    $motion = Motion::factory()->create();

    $response = $this->postJson("/api/v1/motions/{$motion->id}/sessions");

    $response->assertCreated()
        ->assertJsonPath('data.motion_id', $motion->id)
        ->assertJsonPath('data.is_open', true);

    $session = VotingSession::query()->latest('id')->firstOrFail();
    expect((int) $session->opened_at->diffInSeconds($session->closes_at))->toBe(60);
});

it('opens a session with custom duration', function (): void {
    $motion = Motion::factory()->create();

    $this->postJson("/api/v1/motions/{$motion->id}/sessions", [
        'duration_seconds' => 120,
    ])->assertCreated();

    $session = VotingSession::query()->latest('id')->firstOrFail();
    expect((int) $session->opened_at->diffInSeconds($session->closes_at))->toBe(120);
});

it('returns 404 when opening a session for a missing motion', function (): void {
    $this->postJson('/api/v1/motions/999/sessions')
        ->assertNotFound();
});
