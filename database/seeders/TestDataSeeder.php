<?php

namespace Database\Seeders;

use App\Models\Counter;
use App\Models\Meter;
use App\Models\Reading;
use App\Models\User;
use App\Models\Value;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $meters = Meter::factory(5)
            ->recycle($user)
            ->create();

        $counters = collect();

        $meters->each(function(Meter $meter) use (&$counters) {
            $c = Counter::factory(fake()->numberBetween(1, 3))
                ->barcoded()
                ->recycle($meter)
                ->create();

            $counters = $c->merge($counters);
        });

        $readings = Reading::factory(5)
            ->recycle($user)
            ->create();

        $readings->each(function(Reading $reading) use ($counters) {
            $c = $counters->random(fake()->numberBetween(1, $counters->count()));

            /** @var Counter $counter */
            foreach ($c as $counter) {
                Value::factory()
                    ->recycle($counter)
                    ->recycle($reading)
                    ->create();
            }
        });
    }
}
