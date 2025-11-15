<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Document;
use App\Models\ExpenseAllocation;
use App\Models\OfficeBudgetAssignment;
use App\Models\Subclassifier;
use App\Models\User;

class ExpenseAllocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExpenseAllocation::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'year' => fake()->year(),
            'expense_code' => fake()->regexify('[A-Za-z0-9]{50}'),
            'office_budget_assignment_id' => OfficeBudgetAssignment::factory(),
            'document_id' => Document::factory(),
            'subclassifier_id' => Subclassifier::factory(),
            'type' => fake()->randomElement(["goods","services","others"]),
            'report_number' => fake()->regexify('[A-Za-z0-9]{255}'),
            'siaf_code' => fake()->regexify('[A-Za-z0-9]{255}'),
            'reference_document' => fake()->regexify('[A-Za-z0-9]{255}'),
            'reference_document_2' => fake()->regexify('[A-Za-z0-9]{255}'),
            'reference_document_3' => fake()->regexify('[A-Za-z0-9]{255}'),
            'date' => fake()->date(),
            'amount' => fake()->randomFloat(2, 0, 999999999999999999.99),
            'description' => fake()->text(),
            'subject' => fake()->text(),
            'notes' => fake()->text(),
            'status' => fake()->randomElement(["draft","pending","approved","rejected","cancelled"]),
            'created_by' => User::factory()->create()->created_by,
            'approved_by' => User::factory()->create()->approved_by,
            'approved_at' => fake()->dateTime(),
            'rejection_reason' => fake()->text(),
            'created_by_id' => User::factory(),
            'approved_by_id' => User::factory(),
        ];
    }
}
