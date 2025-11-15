<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ImportBatch;
use App\Models\ImportStaging;
use App\Models\ImportedRecord;

class ImportStagingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ImportStaging::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'import_batch_id' => ImportBatch::factory(),
            'row_number' => fake()->numberBetween(-10000, 10000),
            'raw_data' => '{}',
            'parsed_data' => '{}',
            'status' => fake()->randomElement(["pending","validated","imported","failed","skipped"]),
            'validation_errors' => '{}',
            'imported_record_type' => fake()->regexify('[A-Za-z0-9]{100}'),
            'imported_record_id' => ImportedRecord::factory(),
            'created_at' => fake()->dateTime(),
        ];
    }
}
