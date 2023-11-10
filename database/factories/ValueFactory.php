<?php

namespace Database\Factories;

use App\Models\Counter;
use App\Models\Meter;
use App\Models\Reading;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Value>
 */
class ValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reading_id' => Reading::factory(),
            'counter_id' => Counter::factory(),
            'value' => $this->faker->numberBetween(0, 999999999999)/10000,
        ];
    }
}
