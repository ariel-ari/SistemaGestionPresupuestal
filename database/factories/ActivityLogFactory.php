<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\ActivityLog;
use App\Models\Subject;
use App\Models\User;

class ActivityLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ActivityLog::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'year' => fake()->year(),
            'user_id' => User::factory(),
            'user_name' => fake()->userName(),
            'user_role' => fake()->regexify('[A-Za-z0-9]{100}'),
            'action' => fake()->regexify('[A-Za-z0-9]{50}'),
            'subject_type' => fake()->regexify('[A-Za-z0-9]{100}'),
            'subject_id' => Subject::factory(),
            'description' => fake()->text(),
            'old_values' => '{}',
            'new_values' => '{}',
            'ip_address' => fake()->regexify('[A-Za-z0-9]{45}'),
            'user_agent' => fake()->text(),
            'created_at' => fake()->dateTime(),
        ];
    }
}
