<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Classifier;
use App\Models\ExpenseAllocation;
use App\Models\Financing;
use App\Models\Goal;
use App\Models\Office;
use App\Models\SimulatedExpense;
use App\Models\User;

class SimulatedExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SimulatedExpense::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'year' => fake()->year(),
            'simulation_type' => fake()->randomElement(["procurement","accounting"]),
            'office_id' => Office::factory(),
            'goal_id' => Goal::factory(),
            'financing_id' => Financing::factory(),
            'classifier_id' => Classifier::factory(),
            'description' => fake()->text(),
            'amount' => fake()->randomFloat(2, 0, 999999999999999999.99),
            'estimated_date' => fake()->date(),
            'notes' => fake()->text(),
            'is_approved' => fake()->boolean(),
            'converted_to_real' => fake()->boolean(),
            'real_expense_allocation_id' => ExpenseAllocation::factory(),
            'created_by' => User::factory()->create()->created_by,
            'created_by_id' => User::factory(),
            'real_expense_allocation_id_id' => ExpenseAllocation::factory(),
        ];
    }
}
