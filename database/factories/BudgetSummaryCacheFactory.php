<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\BudgetSummaryCache;
use App\Models\Classifier;
use App\Models\Financing;
use App\Models\Goal;
use App\Models\Office;

class BudgetSummaryCacheFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BudgetSummaryCache::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'year' => fake()->year(),
            'office_id' => Office::factory(),
            'goal_id' => Goal::factory(),
            'financing_id' => Financing::factory(),
            'classifier_id' => Classifier::factory(),
            'summary_level' => fake()->randomElement(["institutional","office","goal","financing","classifier","detailed"]),
            'total_budget' => fake()->randomFloat(2, 0, 999999999999999999.99),
            'total_assigned' => fake()->randomFloat(2, 0, 999999999999999999.99),
            'total_executed' => fake()->randomFloat(2, 0, 999999999999999999.99),
            'total_available' => fake()->randomFloat(2, 0, 999999999999999999.99),
            'execution_percentage' => fake()->randomFloat(2, 0, 999.99),
            'last_calculated_at' => fake()->dateTime(),
            'is_valid' => fake()->boolean(),
        ];
    }
}
