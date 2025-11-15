<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Classifier;

class ClassifierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Classifier::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'code' => fake()->regexify('[A-Za-z0-9]{20}'),
            'name' => fake()->name(),
            'alternate_name' => fake()->regexify('[A-Za-z0-9]{255}'),
            'description' => fake()->text(),
            'is_active' => fake()->boolean(),
            'softDeletes' => fake()->word(),
        ];
    }
}
