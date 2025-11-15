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

        Schema::create('annual_institutional_budgets', function (Blueprint $table) {
            $table->id();
            $table->year('year')->index();
            $table->foreignId('goal_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('process_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('activity_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('purpose_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('financing_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('classifier_id')->nullable()->constrained()->onDelete('restrict');
            $table->decimal('amount', 20, 2);
            $table->integer('version')->default(1);
            $table->enum('status', ["active","superseded","cancelled"])->default('active')->index();
            $table->foreignId('parent_id')->nullable()->constrained('annual_institutional_budgets')->onDelete('set null');
            $table->foreignId('superseded_by')->nullable()->constrained('annual_institutional_budgets', 'by')->onDelete('set null');
            $table->enum('modification_type', ["initial","increase","decrease","adjustment"])->default('initial');
            $table->text('modification_reason')->nullable();
            $table->string('resolution_number', 100)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users', 'by')->onDelete('set null');
            $table->foreignId('modified_by')->nullable()->constrained('users', 'by')->onDelete('set null');
            $table->foreignId('created_by_id');
            $table->foreignId('modified_by_id');
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
        Schema::dropIfExists('annual_institutional_budgets');
    }
};
