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
        Schema::create('talent_shortlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('talent_id')->constrained('users')->onDelete('cascade');
            $table->string('list_name')->default('default');
            $table->text('notes')->nullable();
            $table->integer('priority')->default(0); // 0=normal, 1=high, 2=urgent
            $table->timestamps();

            $table->unique(['user_id', 'talent_id', 'list_name']);
            $table->index(['user_id', 'list_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talent_shortlists');
    }
};
