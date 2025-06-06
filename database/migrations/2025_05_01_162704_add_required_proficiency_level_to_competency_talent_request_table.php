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
        Schema::table('competency_talent_request', function (Blueprint $table) {
            // Add the column to store the required proficiency level (1=Completion, 2=Intermediate, 3=Advanced, 4=Expert)
            $table->unsignedTinyInteger('required_proficiency_level')->after('talent_request_id'); // Place it after the foreign key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competency_talent_request', function (Blueprint $table) {
            $table->dropColumn('required_proficiency_level');
        });
    }
};
