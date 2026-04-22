<?php

namespace App\Http\Resources;

use App\Models\VotingSession;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin VotingSession
 */
class VotingSessionResource extends JsonResource
{
    /**
     * @return array{id: int, motion_id: int, opened_at: string, closes_at: string, is_open: bool}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'motion_id' => $this->motion_id,
            'opened_at' => $this->opened_at->toIso8601String(),
            'closes_at' => $this->closes_at->toIso8601String(),
            'is_open' => $this->isOpen(),
        ];
    }
}
