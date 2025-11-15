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

        Schema::create('sequence_counters', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->enum('sequence_type', ["expense","transfer","approval_request","import_batch","simulation"]);
            $table->integer('current_value')->default(0);
            $table->timestamp('last_generated_at')->nullable();
            $table->string('last_generated_code', 50)->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sequence_counters');
    }
};
