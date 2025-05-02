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
        Schema::create('competency_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Link to the talent user
            $table->foreignId('competency_id')->constrained('competencies')->cascadeOnDelete(); // Link to the competency
            $table->integer('proficiency_level')->default(1); // Add proficiency level (e.g., 1-5)
            $table->primary(['user_id', 'competency_id']); // Ensure a user can't have the same competency twice
            // No timestamps needed for a typical pivot table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competency_user');
    }
};
