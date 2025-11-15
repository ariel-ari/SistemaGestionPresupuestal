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

        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->year('year')->index();
            $table->string('request_code', 50)->unique();
            $table->enum('request_type', ["budget_assignment_create","budget_assignment_update","budget_assignment_delete","expense_create","expense_update","expense_delete","budget_modification","catalog_create","catalog_update","catalog_delete","transfer_request"]);
            $table->string('related_table', 100);
            $table->foreignId('related_id')->nullable();
            $table->json('request_data');
            $table->json('current_data')->nullable();
            $table->foreignId('office_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 20, 2)->nullable();
            $table->text('reason')->nullable();
            $table->enum('status', ["draft","pending","approved","rejected","cancelled"])->default('pending')->index();
            $table->foreignId('requested_by')->constrained('users', 'by')->onDelete('restrict');
            $table->foreignId('reviewed_by')->nullable()->constrained('users', 'by')->onDelete('set null');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('requested_by_id');
            $table->foreignId('reviewed_by_id');
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
        Schema::dropIfExists('approval_requests');
    }
};
