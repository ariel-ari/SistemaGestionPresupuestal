<?php

namespace Database\Seeders;

use App\Models\GoalProcessProductFinancingClassifierOfficeDocument;
use Illuminate\Database\Seeder;

class GoalProcessProductFinancingClassifierOfficeDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GoalProcessProductFinancingClassifierOfficeDocument::factory()->count(5)->create();
    }
}
