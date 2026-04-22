<?php

namespace App\Http\Resources;

use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Vote
 */
class VoteResource extends JsonResource
{
    /**
     * @return array{id: int, voting_session_id: int, member_id: string, option: string, created_at: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'voting_session_id' => $this->voting_session_id,
            'member_id' => $this->member_id,
            'option' => $this->option->value,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
