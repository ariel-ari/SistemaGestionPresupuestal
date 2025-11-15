<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ApprovalRequest;
use App\Models\Office;
use App\Models\Related;
use App\Models\User;

class ApprovalRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ApprovalRequest::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'year' => fake()->year(),
            'request_code' => fake()->regexify('[A-Za-z0-9]{50}'),
            'request_type' => fake()->randomElement(["budget_assignment_create","budget_assignment_update","budget_assignment_delete","expense_create","expense_update","expense_delete","budget_modification","catalog_create","catalog_update","catalog_delete","transfer_request"]),
            'related_table' => fake()->regexify('[A-Za-z0-9]{100}'),
            'related_id' => Related::factory(),
            'request_data' => '{}',
            'current_data' => '{}',
            'office_id' => Office::factory(),
            'amount' => fake()->randomFloat(2, 0, 999999999999999999.99),
            'reason' => fake()->text(),
            'status' => fake()->randomElement(["draft","pending","approved","rejected","cancelled"]),
            'requested_by' => User::factory()->create()->requested_by,
            'reviewed_by' => User::factory()->create()->reviewed_by,
            'requested_at' => fake()->dateTime(),
            'reviewed_at' => fake()->dateTime(),
            'rejection_reason' => fake()->text(),
            'requested_by_id' => User::factory(),
            'reviewed_by_id' => User::factory(),
        ];
    }
}
