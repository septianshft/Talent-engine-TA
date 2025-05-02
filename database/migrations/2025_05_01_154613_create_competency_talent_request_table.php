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
        Schema::create('competency_talent_request', function (Blueprint $table) {
            $table->foreignId('competency_id')->constrained('competencies')->cascadeOnDelete();
            $table->foreignId('talent_request_id')->constrained('talent_requests')->cascadeOnDelete();
            $table->primary(['competency_id', 'talent_request_id']);
            // No timestamps needed for this pivot table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competency_talent_request');
    }
};
