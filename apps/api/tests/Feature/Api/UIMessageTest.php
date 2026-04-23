<?php

use App\Models\Motion;
use App\Models\VotingSession;

it('renders the motion selection message (Annex 1)', function (): void {
    config()->set('app.callback_domain', 'http://sicredi.test');

    $motion = Motion::factory()->create(['title' => 'Assembleia anual']);

    $response = $this->getJson('/api/v1/ui/motions');

    $response->assertOk()
        ->assertJsonPath('tipo', 'SELECAO')
        ->assertJsonPath('titulo', 'Selecione uma pauta para abrir uma sessão de votação')
        ->assertJsonPath('itens.0.texto', 'Assembleia anual')
        ->assertJsonPath('itens.0.url', "http://sicredi.test/api/v1/motions/{$motion->id}/sessions");
});

it('renders the motion create form message (Annex 1)', function (): void {
    config()->set('app.callback_domain', 'http://sicredi.test');
    config()->set('app.frontend_url', 'http://sicredi.test:3000');

    $response = $this->getJson('/api/v1/ui/motions/new');

    $response->assertOk()
        ->assertJsonPath('tipo', 'FORMULARIO')
        ->assertJsonPath('titulo', 'Cadastrar nova pauta')
        ->assertJsonPath('itens.0.tipo', 'TEXTO')
        ->assertJsonPath('itens.0.id', 'title')
        ->assertJsonPath('itens.0.label', 'Título')
        ->assertJsonPath('itens.1.id', 'description')
        ->assertJsonPath('itens.1.label', 'Descrição')
        ->assertJsonPath('botoes.0.texto', 'Cancelar')
        ->assertJsonPath('botoes.0.metodo', 'GET')
        ->assertJsonPath('botoes.0.url', 'http://sicredi.test:3000/motions')
        ->assertJsonPath('botoes.1.texto', 'Cadastrar')
        ->assertJsonPath('botoes.1.url', 'http://sicredi.test/api/v1/motions');
});

it('renders the vote form message (Annex 1)', function (): void {
    config()->set('app.callback_domain', 'http://sicredi.test');
    config()->set('app.frontend_url', 'http://sicredi.test:3000');

    $session = VotingSession::factory()->create();

    $response = $this->getJson("/api/v1/ui/sessions/{$session->id}/vote");

    $response->assertOk()
        ->assertJsonPath('tipo', 'FORMULARIO')
        ->assertJsonPath('titulo', "Votar na sessão #{$session->id}")
        ->assertJsonPath('itens.0.tipo', 'TEXTO')
        ->assertJsonPath('itens.0.id', 'member_id')
        ->assertJsonPath('itens.0.label', 'ID do associado')
        ->assertJsonPath('botoes.0.texto', 'Cancelar')
        ->assertJsonPath('botoes.0.metodo', 'GET')
        ->assertJsonPath('botoes.0.url', 'http://sicredi.test:3000/motions')
        ->assertJsonPath('botoes.1.texto', 'Sim')
        ->assertJsonPath('botoes.1.url', "http://sicredi.test/api/v1/sessions/{$session->id}/votes?option=Yes")
        ->assertJsonPath('botoes.2.texto', 'Não')
        ->assertJsonPath('botoes.2.url', "http://sicredi.test/api/v1/sessions/{$session->id}/votes?option=No");
});
