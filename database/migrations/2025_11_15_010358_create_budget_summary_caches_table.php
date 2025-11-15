<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('budget_summary_caches', function (Blueprint $table) {
            $table->id();
            $table->year('year')->index();
            $table->foreignId('office_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('goal_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('financing_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('classifier_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('summary_level', ["institutional","office","goal","financing","classifier","detailed"]);
            $table->decimal('total_budget', 20, 2)->default(0);
            $table->decimal('total_assigned', 20, 2)->default(0);
            $table->decimal('total_executed', 20, 2)->default(0);
            $table->decimal('total_available', 20, 2)->default(0);
            $table->decimal('execution_percentage', 5, 2)->nullable();
            $table->timestamp('last_calculated_at');
            $table->boolean('is_valid')->default(true);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_summary_caches');
    }
};
