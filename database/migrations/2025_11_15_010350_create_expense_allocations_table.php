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

        Schema::create('expense_allocations', function (Blueprint $table) {
            $table->id();
            $table->year('year')->index();
            $table->string('expense_code', 50)->unique();
            $table->foreignId('office_budget_assignment_id')->constrained()->onDelete('restrict');
            $table->foreignId('document_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('subclassifier_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ["goods","services","others"])->default('others');
            $table->string('report_number', 255)->nullable();
            $table->string('siaf_code', 255)->nullable();
            $table->string('reference_document', 255)->nullable();
            $table->string('reference_document_2', 255)->nullable();
            $table->string('reference_document_3', 255)->nullable();
            $table->date('date');
            $table->decimal('amount', 20, 2);
            $table->text('description')->nullable();
            $table->text('subject')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ["draft","pending","approved","rejected","cancelled"])->default('approved')->index();
            $table->foreignId('created_by')->constrained('users', 'by')->onDelete('restrict');
            $table->foreignId('approved_by')->nullable()->constrained('users', 'by')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('created_by_id');
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
        Schema::dropIfExists('expense_allocations');
    }
};
