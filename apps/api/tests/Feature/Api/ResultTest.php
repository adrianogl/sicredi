<?php

use App\Models\Motion;
use App\Models\Vote;
use App\Models\VotingSession;

it('computes consolidated yes/no counts across sessions of the motion', function (): void {
    $motion = Motion::factory()->create();
    $session = VotingSession::factory()->for($motion, 'motion')->create();

    Vote::factory()->count(3)->create([
        'voting_session_id' => $session->id,
        'option' => 'Yes',
    ])->each(fn (Vote $v, int $i) => $v->update(['member_id' => "yes-{$i}"]));

    Vote::factory()->count(2)->create([
        'voting_session_id' => $session->id,
        'option' => 'No',
    ])->each(fn (Vote $v, int $i) => $v->update(['member_id' => "no-{$i}"]));

    $this->getJson("/api/v1/motions/{$motion->id}/result")
        ->assertOk()
        ->assertJsonPath('data.motion_id', $motion->id)
        ->assertJsonPath('data.yes_count', 3)
        ->assertJsonPath('data.no_count', 2)
        ->assertJsonPath('data.total', 5);
});

it('returns zeros when there are no votes', function (): void {
    $motion = Motion::factory()->create();

    $this->getJson("/api/v1/motions/{$motion->id}/result")
        ->assertOk()
        ->assertJsonPath('data.yes_count', 0)
        ->assertJsonPath('data.no_count', 0)
        ->assertJsonPath('data.total', 0);
});
