<?php

namespace App\Models;

use App\Enums\VoteOption;
use Database\Factories\VoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $voting_session_id
 * @property string $member_id
 * @property VoteOption $option
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read VotingSession $session
 */
class Vote extends Model
{
    /** @use HasFactory<VoteFactory> */
    use HasFactory;

    protected $fillable = ['voting_session_id', 'member_id', 'option'];

    protected function casts(): array
    {
        return [
            'option' => VoteOption::class,
        ];
    }

    /** @return BelongsTo<VotingSession, $this> */
    public function session(): BelongsTo
    {
        return $this->belongsTo(VotingSession::class, 'voting_session_id');
    }
}
