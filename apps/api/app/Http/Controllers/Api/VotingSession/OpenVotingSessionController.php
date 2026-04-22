<?php

namespace App\Http\Controllers\Api\VotingSession;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpenSessionRequest;
use App\Http\Resources\VotingSessionResource;
use App\Models\Motion;
use App\Repositories\Contracts\VotingSessionRepositoryInterface;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

#[Group('Sessoes de votacao', 'Abertura de sessoes de votacao em pautas existentes')]
class OpenVotingSessionController extends Controller
{
    public function __construct(
        private readonly VotingSessionRepositoryInterface $votingSessionRepository,
    ) {}

    #[Endpoint(
        title: 'Abrir sessao de votacao',
        description: 'Abre uma sessao de votacao em uma pauta. Duracao padrao e 60 segundos quando `duration_seconds` nao e informado.',
    )]
    #[Response(status: 201, description: 'Sessao aberta.')]
    #[Response(status: 404, description: 'Pauta nao encontrada.')]
    #[Response(status: 422, description: 'Erro de validacao.')]
    public function __invoke(OpenSessionRequest $request, Motion $motion): JsonResponse
    {
        $session = $this->votingSessionRepository->openFor($motion, $request->durationSeconds());

        return VotingSessionResource::make($session)
            ->response()
            ->setStatusCode(HttpResponse::HTTP_CREATED);
    }
}
