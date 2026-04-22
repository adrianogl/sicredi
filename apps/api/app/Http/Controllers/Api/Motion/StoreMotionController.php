<?php

namespace App\Http\Controllers\Api\Motion;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMotionRequest;
use App\Http\Resources\MotionResource;
use App\Repositories\Contracts\MotionRepositoryInterface;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

#[Group('Pautas', 'Endpoints de gerenciamento de pautas de votacao')]
class StoreMotionController extends Controller
{
    public function __construct(
        private readonly MotionRepositoryInterface $motionRepository,
    ) {}

    #[Endpoint(
        title: 'Criar pauta',
        description: 'Cadastra uma nova pauta que podera ter sessoes de votacao abertas depois.',
    )]
    #[Response(status: 201, description: 'Pauta criada.')]
    #[Response(status: 422, description: 'Erro de validacao.')]
    public function __invoke(CreateMotionRequest $request): JsonResponse
    {
        $motion = $this->motionRepository->create($request->validated());

        return MotionResource::make($motion)
            ->response()
            ->setStatusCode(HttpResponse::HTTP_CREATED);
    }
}
