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
            $table->unsignedTinyInteger('weight')->default(1)->after('required_proficiency_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competency_talent_request', function (Blueprint $table) {
            $table->dropColumn('weight');
        });
    }
};
