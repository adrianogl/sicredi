<?php

namespace App\Http\Controllers\Api\Result;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResultResource;
use App\Models\Motion;
use App\Repositories\Contracts\VoteRepositoryInterface;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response;

#[Group('Pautas', 'Endpoints de gerenciamento de pautas de votacao')]
class ShowMotionResultController extends Controller
{
    public function __construct(
        private readonly VoteRepositoryInterface $voteRepository,
    ) {}

    #[Endpoint(
        title: 'Exibir resultado da pauta',
        description: 'Retorna a contagem consolidada de votos Sim/Nao da pauta, somando todas as sessoes.',
    )]
    #[Response(status: 200, description: 'Resultado consolidado da pauta.')]
    #[Response(status: 404, description: 'Pauta nao encontrada.')]
    public function __invoke(Motion $motion): ResultResource
    {
        $counts = $this->voteRepository->countsForMotion($motion);

        return ResultResource::make([
            'motion_id' => $motion->id,
            'yes_count' => $counts['yes'],
            'no_count' => $counts['no'],
        ]);
    }
}
