<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use RuntimeException;

class MemberNotEligibleException extends RuntimeException
{
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => 'Member is not eligible to vote.',
            'code' => 'MEMBER_NOT_ELIGIBLE',
        ], Response::HTTP_FORBIDDEN);
    }
}
