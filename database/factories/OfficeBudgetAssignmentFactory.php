<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\AnnualInstitutionalBudget;
use App\Models\Office;
use App\Models\OfficeBudgetAssignment;
use App\Models\RelatedTransfer;
use App\Models\Subunit;
use App\Models\User;

class OfficeBudgetAssignmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OfficeBudgetAssignment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'year' => fake()->year(),
            'office_id' => Office::factory(),
            'subunit_id' => Subunit::factory(),
            'annual_institutional_budget_id' => AnnualInstitutionalBudget::factory(),
            'amount' => fake()->randomFloat(2, 0, 999999999999999999.99),
            'version' => fake()->numberBetween(-10000, 10000),
            'status' => fake()->randomElement(["active","superseded","transferred","cancelled"]),
            'parent_id' => OfficeBudgetAssignment::factory(),
            'superseded_by' => OfficeBudgetAssignment::factory()->create()->superseded_by,
            'assignment_type' => fake()->randomElement(["initial","modification","transfer_in","transfer_out"]),
            'comment' => fake()->text(),
            'related_transfer_id' => RelatedTransfer::factory(),
            'assigned_by' => User::factory()->create()->assigned_by,
            'modified_by' => User::factory()->create()->modified_by,
            'assigned_by_id' => User::factory(),
            'modified_by_id' => User::factory(),
        ];
    }
}
