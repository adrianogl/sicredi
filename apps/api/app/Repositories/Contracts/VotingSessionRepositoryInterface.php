<?php

namespace App\Repositories\Contracts;

use App\Models\Motion;
use App\Models\VotingSession;

interface VotingSessionRepositoryInterface
{
    /**
     * Abre uma sessao de votacao para a pauta informada.
     * Quando `durationSeconds` e null, o repositorio aplica o default.
     */
    public function openFor(Motion $motion, ?int $durationSeconds = null): VotingSession;
}
