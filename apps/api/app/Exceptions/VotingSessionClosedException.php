<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use RuntimeException;

class VotingSessionClosedException extends RuntimeException
{
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => 'Voting session is closed.',
            'code' => 'SESSION_CLOSED',
        ], Response::HTTP_CONFLICT);
    }
}
