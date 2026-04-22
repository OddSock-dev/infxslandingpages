<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Journey;
use App\Models\JourneyAnswer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JourneyAnswer>
 */
class JourneyAnswerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'journey_id' => Journey::factory(),
            'step_key' => 'qualification',
            'field_key' => fake()->randomElement(['business_size', 'primary_need', 'timeline']),
            'value' => fake()->word(),
        ];
    }
}
