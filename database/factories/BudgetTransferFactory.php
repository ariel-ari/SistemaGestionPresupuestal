<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\AnnualInstitutionalBudget;
use App\Models\BudgetTransfer;
use App\Models\Office;
use App\Models\OfficeBudgetAssignment;
use App\Models\User;

class BudgetTransferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BudgetTransfer::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'year' => fake()->year(),
            'transfer_code' => fake()->regexify('[A-Za-z0-9]{50}'),
            'transaction_number' => fake()->numberBetween(-10000, 10000),
            'origin_office_budget_assignment_id' => OfficeBudgetAssignment::factory(),
            'origin_office_id' => Office::factory(),
            'origin_annual_budget_id' => AnnualInstitutionalBudget::factory(),
            'destination_office_budget_assignment_id' => OfficeBudgetAssignment::factory(),
            'destination_office_id' => Office::factory(),
            'destination_annual_budget_id' => AnnualInstitutionalBudget::factory(),
            'amount' => fake()->randomFloat(2, 0, 999999999999999999.99),
            'reason' => fake()->text(),
            'resolution_number' => fake()->regexify('[A-Za-z0-9]{100}'),
            'status' => fake()->randomElement(["draft","pending","approved","rejected","executed"]),
            'requested_by' => User::factory()->create()->requested_by,
            'approved_by' => User::factory()->create()->approved_by,
            'requested_at' => fake()->dateTime(),
            'approved_at' => fake()->dateTime(),
            'executed_at' => fake()->dateTime(),
            'rejection_reason' => fake()->text(),
            'requested_by_id' => User::factory(),
            'approved_by_id' => User::factory(),
        ];
    }
}
