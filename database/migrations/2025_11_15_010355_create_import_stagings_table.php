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

        Schema::create('import_stagings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->constrained()->cascadeOnDelete();
            $table->integer('row_number');
            $table->json('raw_data');
            $table->json('parsed_data')->nullable();
            $table->enum('status', ["pending","validated","imported","failed","skipped"])->default('pending')->index();
            $table->json('validation_errors')->nullable();
            $table->string('imported_record_type', 100)->nullable();
            $table->foreignId('imported_record_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_stagings');
    }
};
