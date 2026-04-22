<?php

namespace App\Models;

use Database\Factories\VotingSessionFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $motion_id
 * @property Carbon $opened_at
 * @property Carbon $closes_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Motion $motion
 * @property-read Collection<int, Vote> $votes
 */
class VotingSession extends Model
{
    /** @use HasFactory<VotingSessionFactory> */
    use HasFactory;

    protected $fillable = ['motion_id', 'opened_at', 'closes_at'];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'closes_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Motion, $this> */
    public function motion(): BelongsTo
    {
        return $this->belongsTo(Motion::class);
    }

    /** @return HasMany<Vote, $this> */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function isOpen(?Carbon $now = null): bool
    {
        $now ??= Carbon::now();

        return $now->between($this->opened_at, $this->closes_at);
    }
}
