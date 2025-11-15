<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ImportBatch;
use App\Models\User;

class ImportBatchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ImportBatch::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'year' => fake()->year(),
            'batch_code' => fake()->regexify('[A-Za-z0-9]{50}'),
            'import_type' => fake()->randomElement(["annual_budget","office_assignments","expenses","catalog_data"]),
            'file_name' => fake()->regexify('[A-Za-z0-9]{255}'),
            'file_path' => fake()->regexify('[A-Za-z0-9]{500}'),
            'file_size' => fake()->numberBetween(-10000, 10000),
            'total_rows' => fake()->numberBetween(-10000, 10000),
            'processed_rows' => fake()->numberBetween(-10000, 10000),
            'successful_rows' => fake()->numberBetween(-10000, 10000),
            'failed_rows' => fake()->numberBetween(-10000, 10000),
            'status' => fake()->randomElement(["pending","validating","processing","completed","failed","cancelled"]),
            'errors' => '{}',
            'validation_summary' => '{}',
            'imported_by' => User::factory()->create()->imported_by,
            'started_at' => fake()->dateTime(),
            'completed_at' => fake()->dateTime(),
            'imported_by_id' => User::factory(),
        ];
    }
}
