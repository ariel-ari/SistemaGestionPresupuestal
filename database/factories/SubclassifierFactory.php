<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Classifier;
use App\Models\Subclassifier;

class SubclassifierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subclassifier::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'classifier_id' => Classifier::factory(),
            'code' => fake()->regexify('[A-Za-z0-9]{20}'),
            'name' => fake()->name(),
            'description' => fake()->text(),
            'is_active' => fake()->boolean(),
            'softDeletes' => fake()->word(),
        ];
    }
}
