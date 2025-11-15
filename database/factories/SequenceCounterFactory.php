<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\SequenceCounter;

class SequenceCounterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SequenceCounter::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'year' => fake()->year(),
            'sequence_type' => fake()->randomElement(["expense","transfer","approval_request","import_batch","simulation"]),
            'current_value' => fake()->numberBetween(-10000, 10000),
            'last_generated_at' => fake()->dateTime(),
            'last_generated_code' => fake()->regexify('[A-Za-z0-9]{50}'),
        ];
    }
}
