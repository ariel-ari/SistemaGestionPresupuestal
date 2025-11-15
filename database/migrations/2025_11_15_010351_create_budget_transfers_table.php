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

        Schema::create('budget_transfers', function (Blueprint $table) {
            $table->id();
            $table->year('year')->index();
            $table->string('transfer_code', 50)->unique();
            $table->integer('transaction_number')->nullable();
            $table->foreignId('origin_office_budget_assignment_id')->constrained('office_budget_assignments')->onDelete('restrict');
            $table->foreignId('origin_office_id')->constrained('offices')->onDelete('restrict');
            $table->foreignId('origin_annual_budget_id')->constrained('annual_institutional_budgets')->onDelete('restrict');
            $table->foreignId('destination_office_budget_assignment_id')->constrained('office_budget_assignments')->onDelete('restrict');
            $table->foreignId('destination_office_id')->constrained('offices')->onDelete('restrict');
            $table->foreignId('destination_annual_budget_id')->constrained('annual_institutional_budgets')->onDelete('restrict');
            $table->decimal('amount', 20, 2);
            $table->text('reason');
            $table->string('resolution_number', 100)->nullable();
            $table->enum('status', ["draft","pending","approved","rejected","executed"])->default('pending')->index();
            $table->foreignId('requested_by')->constrained('users', 'by')->onDelete('restrict');
            $table->foreignId('approved_by')->nullable()->constrained('users', 'by')->onDelete('set null');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('requested_by_id');
            $table->foreignId('approved_by_id');
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
        Schema::dropIfExists('budget_transfers');
    }
};
