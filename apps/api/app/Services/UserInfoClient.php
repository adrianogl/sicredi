<?php

namespace App\Services;

use App\Enums\MemberStatus;
use App\Exceptions\ExternalServiceUnavailableException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Client for the external user-info service (Bonus 1).
 *
 * GET {base_url}/users/{id}
 *  - 404       -> member not found / invalid id
 *  - 200 JSON  -> {"status": "ABLE_TO_VOTE" | "UNABLE_TO_VOTE"} (random)
 */
class UserInfoClient
{
    /**
     * Returns the voting eligibility for the given member id,
     * or null if the id is not found in the external service (HTTP 404).
     *
     * When the integration is disabled via config, returns Eligible.
     *
     * @throws ExternalServiceUnavailableException when the external service is unreachable
     */
    public function statusFor(string $memberId): ?MemberStatus
    {
        if (! (bool) config('services.user_info.enabled', true)) {
            return MemberStatus::Eligible;
        }

        $baseUrl = rtrim((string) config('services.user_info.url'), '/');
        $timeout = (int) config('services.user_info.timeout', 5);

        try {
            $response = Http::baseUrl($baseUrl)
                ->timeout($timeout)
                ->acceptJson()
                ->get("/users/{$memberId}");
        } catch (ConnectionException $e) {
            Log::warning('user_info.connection_failed', [
                'member_hash' => hash('sha256', $memberId),
                'error' => $e->getMessage(),
            ]);

            throw new ExternalServiceUnavailableException(
                'Failed to reach user-info service.',
                previous: $e,
            );
        }

        if ($response->status() === 404) {
            return null;
        }

        $value = (string) $response->json('status', '');

        return MemberStatus::tryFrom($value);
    }
}
