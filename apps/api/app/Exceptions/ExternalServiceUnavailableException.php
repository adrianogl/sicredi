<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use RuntimeException;

class ExternalServiceUnavailableException extends RuntimeException
{
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => 'External service temporarily unavailable.',
            'code' => 'EXTERNAL_SERVICE_UNAVAILABLE',
        ], Response::HTTP_SERVICE_UNAVAILABLE);
    }
}
