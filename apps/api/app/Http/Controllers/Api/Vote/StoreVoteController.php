<?php

namespace App\Http\Controllers\Api\Vote;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterVoteRequest;
use App\Http\Resources\VoteResource;
use App\Models\VotingSession;
use App\Services\VoteService;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

#[Group('Votos', 'Registro de votos em sessoes de votacao')]
class StoreVoteController extends Controller
{
    public function __construct(
        private readonly VoteService $voteService,
    ) {}

    #[Endpoint(
        title: 'Registrar voto',
        description: 'Registra o voto (Sim/Nao) de um associado em uma sessao aberta.',
    )]
    #[Response(status: 201, description: 'Voto registrado.')]
    #[Response(status: 403, description: 'Associado nao habilitado a votar.')]
    #[Response(status: 404, description: 'Sessao nao encontrada.')]
    #[Response(status: 409, description: 'Sessao fechada ou voto duplicado.')]
    #[Response(status: 422, description: 'Erro de validacao.')]
    #[Response(status: 503, description: 'Servico externo de user-info indisponivel.')]
    public function __invoke(RegisterVoteRequest $request, VotingSession $session): JsonResponse
    {
        $vote = $this->voteService->register($session, $request->memberId(), $request->option());

        return VoteResource::make($vote)
            ->response()
            ->setStatusCode(HttpResponse::HTTP_CREATED);
    }
}
