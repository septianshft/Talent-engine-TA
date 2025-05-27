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
            $table->boolean('is_critical')->default(false)->after('weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competency_talent_request', function (Blueprint $table) {
            $table->dropColumn('is_critical');
        });
    }
};
