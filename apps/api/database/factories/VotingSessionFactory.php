<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Motion;
use App\Models\VotingSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<VotingSession>
 */
class VotingSessionFactory extends Factory
{
    protected $model = VotingSession::class;

    public function definition(): array
    {
        $opening = Carbon::now();

        return [
            'motion_id' => Motion::factory(),
            'opened_at' => $opening,
            'closes_at' => $opening->copy()->addMinute(),
        ];
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'opened_at' => Carbon::now()->subMinutes(5),
            'closes_at' => Carbon::now()->subMinute(),
        ]);
    }
}
