<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Activity;
use App\Models\AnnualInstitutionalBudget;
use App\Models\Classifier;
use App\Models\Financing;
use App\Models\Goal;
use App\Models\Process;
use App\Models\Product;
use App\Models\Purpose;
use App\Models\User;

class AnnualInstitutionalBudgetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AnnualInstitutionalBudget::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'year' => fake()->year(),
            'goal_id' => Goal::factory(),
            'process_id' => Process::factory(),
            'product_id' => Product::factory(),
            'activity_id' => Activity::factory(),
            'purpose_id' => Purpose::factory(),
            'financing_id' => Financing::factory(),
            'classifier_id' => Classifier::factory(),
            'amount' => fake()->randomFloat(2, 0, 999999999999999999.99),
            'version' => fake()->numberBetween(-10000, 10000),
            'status' => fake()->randomElement(["active","superseded","cancelled"]),
            'parent_id' => AnnualInstitutionalBudget::factory(),
            'superseded_by' => AnnualInstitutionalBudget::factory()->create()->superseded_by,
            'modification_type' => fake()->randomElement(["initial","increase","decrease","adjustment"]),
            'modification_reason' => fake()->text(),
            'resolution_number' => fake()->regexify('[A-Za-z0-9]{100}'),
            'created_by' => User::factory()->create()->created_by,
            'modified_by' => User::factory()->create()->modified_by,
            'created_by_id' => User::factory(),
            'modified_by_id' => User::factory(),
        ];
    }
}
