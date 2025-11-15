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

        Schema::create('simulated_expenses', function (Blueprint $table) {
            $table->id();
            $table->year('year')->index();
            $table->enum('simulation_type', ["procurement","accounting"]);
            $table->foreignId('office_id')->constrained()->onDelete('restrict');
            $table->foreignId('goal_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('financing_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('classifier_id')->nullable()->constrained()->onDelete('set null');
            $table->text('description')->nullable();
            $table->decimal('amount', 20, 2);
            $table->date('estimated_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('converted_to_real')->default(false);
            $table->foreignId('real_expense_allocation_id')->nullable()->constrained('expense_allocations')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users', 'by')->onDelete('restrict');
            $table->foreignId('created_by_id');
            $table->foreignId('real_expense_allocation_id_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulated_expenses');
    }
};
