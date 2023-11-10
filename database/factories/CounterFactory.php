<?php

namespace Database\Factories;

use App\Models\Meter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Counter>
 */
class CounterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'meter_id' => Meter::factory(),
            'barcode' => null,
        ];
    }

    public function barcoded(): static
    {
        return $this->state(fn (array $attributes) => [
            'barcode' => $this->faker->uuid,
        ]);
    }
}
