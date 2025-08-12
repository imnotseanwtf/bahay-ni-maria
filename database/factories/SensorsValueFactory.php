<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SensorsValue>
 */
class SensorsValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => 1,
            'bpm' => $this->faker->numberBetween(40, 200),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
