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
        Schema::create('talent_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // The user making the request
            $table->foreignId('talent_id')->constrained('users')->cascadeOnDelete(); // The talent being requested
            $table->text('details')->nullable(); // Optional details about the request
            $table->string('status')->default('pending_admin'); // e.g., pending_admin, pending_talent, approved, rejected_admin, rejected_talent, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talent_requests');
    }
};
