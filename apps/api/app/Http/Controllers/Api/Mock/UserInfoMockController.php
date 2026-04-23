<?php

namespace App\Http\Controllers\Api\Mock;

use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

#[Group('Mock (dev)', 'Substituto local do serviço externo do Bonus 1 (user-info). O Heroku original foi descontinuado.')]
class UserInfoMockController extends Controller
{
    #[Endpoint(
        title: 'Mock: checagem de elegibilidade por CPF',
        description: 'Imita https://user-info.herokuapp.com/users/{cpf}. Retorna 404 quando o CPF termina em "000" (simula "não existe"); caso contrário, devolve aleatoriamente ABLE_TO_VOTE ou UNABLE_TO_VOTE.',
    )]
    #[Response(status: 200, description: '{"status": "ABLE_TO_VOTE" | "UNABLE_TO_VOTE"}')]
    #[Response(status: 404, description: 'CPF considerado inexistente (termina em "000").')]
    public function __invoke(string $cpf): JsonResponse
    {
        if (str_ends_with($cpf, '000')) {
            return response()->json(null, HttpResponse::HTTP_NOT_FOUND);
        }

        $status = random_int(0, 1) === 0 ? 'ABLE_TO_VOTE' : 'UNABLE_TO_VOTE';

        return response()->json(['status' => $status]);
    }
}
