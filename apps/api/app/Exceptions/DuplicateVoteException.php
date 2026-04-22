<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use RuntimeException;

class DuplicateVoteException extends RuntimeException
{
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => 'This member has already voted on this session.',
            'code' => 'DUPLICATE_VOTE',
        ], Response::HTTP_CONFLICT);
    }
}
