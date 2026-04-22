<?php

namespace App\Http\Controllers\Api\Motion;

use App\Http\Controllers\Controller;
use App\Http\Resources\MotionResource;
use App\Repositories\Contracts\MotionRepositoryInterface;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[Group('Pautas', 'Endpoints de gerenciamento de pautas de votacao')]
class ListMotionsController extends Controller
{
    public function __construct(
        private readonly MotionRepositoryInterface $motionRepository,
    ) {}

    #[Endpoint(
        title: 'Listar pautas',
        description: 'Retorna lista paginada de pautas, da mais recente para a mais antiga.',
    )]
    #[Response(status: 200, description: 'Lista paginada de pautas.')]
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->input('per_page', 15);

        return MotionResource::collection(
            $this->motionRepository->paginate($perPage)
        );
    }
}
