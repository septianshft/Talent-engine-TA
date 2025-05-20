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

            // Conditionally add foreign key for talent_id to avoid issues with SQLite when dropping this column later
            if (Schema::connection(null)->getConnection()->getDriverName() !== 'sqlite') {
                $table->foreignId('talent_id')->nullable()->constrained('users')->cascadeOnDelete(); // The talent being requested (can be null initially)
            } else {
                $table->foreignId('talent_id')->nullable(); // For SQLite, add the column without the constraint initially
                // The constraint will be effectively removed when the column is dropped in a later migration.
            }

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
