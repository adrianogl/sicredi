<?php

use App\Models\Motion;
use App\Models\VotingSession;

it('renders the motion selection message (Annex 1)', function (): void {
    config()->set('app.callback_domain', 'http://sicredi.test');

    $motion = Motion::factory()->create(['title' => 'Assembleia anual']);

    $response = $this->getJson('/api/v1/ui/motions');

    $response->assertOk()
        ->assertJsonPath('tipo', 'SELECAO')
        ->assertJsonPath('itens.0.texto', 'Assembleia anual')
        ->assertJsonPath('itens.0.url', "http://sicredi.test/api/v1/motions/{$motion->id}/sessions");
});

it('renders the vote form message (Annex 1)', function (): void {
    config()->set('app.callback_domain', 'http://sicredi.test');

    $session = VotingSession::factory()->create();

    $response = $this->getJson("/api/v1/ui/sessions/{$session->id}/vote");

    $response->assertOk()
        ->assertJsonPath('tipo', 'FORMULARIO')
        ->assertJsonPath('itens.0.tipo', 'TEXTO')
        ->assertJsonPath('itens.0.id', 'member_id')
        ->assertJsonPath('botoes.0.texto', 'Yes')
        ->assertJsonPath('botoes.0.url', "http://sicredi.test/api/v1/sessions/{$session->id}/votes?option=Yes")
        ->assertJsonPath('botoes.1.texto', 'No')
        ->assertJsonPath('botoes.1.url', "http://sicredi.test/api/v1/sessions/{$session->id}/votes?option=No");
});
