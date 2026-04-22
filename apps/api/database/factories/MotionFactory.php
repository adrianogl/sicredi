<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Motion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Motion>
 */
class MotionFactory extends Factory
{
    protected $model = Motion::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
        ];
    }
}
