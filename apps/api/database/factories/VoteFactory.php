<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\VoteOption;
use App\Models\Vote;
use App\Models\VotingSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vote>
 */
class VoteFactory extends Factory
{
    protected $model = Vote::class;

    public function definition(): array
    {
        return [
            'voting_session_id' => VotingSession::factory(),
            'member_id' => fake()->numerify('##########'),
            'option' => fake()->randomElement(VoteOption::cases()),
        ];
    }
}
