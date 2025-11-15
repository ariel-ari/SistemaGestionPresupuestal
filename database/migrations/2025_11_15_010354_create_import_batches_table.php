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

        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();
            $table->year('year')->index();
            $table->string('batch_code', 50)->unique();
            $table->enum('import_type', ["annual_budget","office_assignments","expenses","catalog_data"]);
            $table->string('file_name', 255);
            $table->string('file_path', 500);
            $table->integer('file_size')->nullable();
            $table->integer('total_rows');
            $table->integer('processed_rows')->default(0);
            $table->integer('successful_rows')->default(0);
            $table->integer('failed_rows')->default(0);
            $table->enum('status', ["pending","validating","processing","completed","failed","cancelled"])->default('pending')->index();
            $table->json('errors')->nullable();
            $table->json('validation_summary')->nullable();
            $table->foreignId('imported_by')->constrained('users', 'by')->onDelete('restrict');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('imported_by_id');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};
