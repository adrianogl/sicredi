<?php

namespace App\Models;

use Database\Factories\MotionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Motion extends Model
{
    /** @use HasFactory<MotionFactory> */
    use HasFactory;

    protected $fillable = ['title', 'description'];

    /** @return HasMany<VotingSession, $this> */
    public function sessions(): HasMany
    {
        return $this->hasMany(VotingSession::class);
    }
}
