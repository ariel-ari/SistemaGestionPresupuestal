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

        Schema::create('office_budget_assignments', function (Blueprint $table) {
            $table->id();
            $table->year('year')->index();
            $table->foreignId('office_id')->constrained()->onDelete('restrict');
            $table->foreignId('subunit_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('annual_institutional_budget_id')->constrained()->onDelete('restrict');
            $table->decimal('amount', 20, 2);
            $table->integer('version')->default(1);
            $table->enum('status', ["active","superseded","transferred","cancelled"])->default('active')->index();
            $table->foreignId('parent_id')->nullable()->constrained('office_budget_assignments')->onDelete('set null');
            $table->foreignId('superseded_by')->nullable()->constrained('office_budget_assignments', 'by')->onDelete('set null');
            $table->enum('assignment_type', ["initial","modification","transfer_in","transfer_out"])->default('initial');
            $table->text('comment')->nullable();
            $table->foreignId('related_transfer_id')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users', 'by')->onDelete('set null');
            $table->foreignId('modified_by')->nullable()->constrained('users', 'by')->onDelete('set null');
            $table->foreignId('assigned_by_id');
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
        Schema::dropIfExists('office_budget_assignments');
    }
};
