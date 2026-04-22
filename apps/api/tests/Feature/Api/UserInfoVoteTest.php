<?php

use App\Models\VotingSession;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    config()->set('services.user_info.enabled', true);
    config()->set('services.user_info.url', 'https://user-info.test');
});

it('accepts the vote when the member is ABLE_TO_VOTE', function (): void {
    Http::fake([
        'user-info.test/users/12345678900' => Http::response(['status' => 'ABLE_TO_VOTE'], 200),
    ]);

    $session = VotingSession::factory()->create();

    $this->postJson("/api/v1/sessions/{$session->id}/votes", [
        'member_id' => '12345678900',
        'option' => 'Yes',
    ])->assertCreated();

    Http::assertSent(fn ($request) => str_ends_with($request->url(), '/users/12345678900'));
});

it('blocks the vote when the member is UNABLE_TO_VOTE', function (): void {
    Http::fake([
        'user-info.test/users/*' => Http::response(['status' => 'UNABLE_TO_VOTE'], 200),
    ]);

    $session = VotingSession::factory()->create();

    $this->postJson("/api/v1/sessions/{$session->id}/votes", [
        'member_id' => '111',
        'option' => 'Yes',
    ])->assertForbidden()
        ->assertJsonPath('code', 'MEMBER_NOT_ELIGIBLE');
});

it('blocks the vote when the external service returns 404', function (): void {
    Http::fake([
        'user-info.test/users/*' => Http::response(null, 404),
    ]);

    $session = VotingSession::factory()->create();

    $this->postJson("/api/v1/sessions/{$session->id}/votes", [
        'member_id' => '000',
        'option' => 'No',
    ])->assertForbidden()
        ->assertJsonPath('code', 'MEMBER_NOT_ELIGIBLE');
});
