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
        Schema::table('talent_requests', function (Blueprint $table) {
            $table->string('work_location_type')->default('remote')->after('status'); // e.g., remote, on-site, hybrid
            $table->string('work_location_country')->nullable()->after('work_location_type');
            $table->string('work_location_city')->nullable()->after('work_location_country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talent_requests', function (Blueprint $table) {
            $table->dropColumn(['work_location_type', 'work_location_country', 'work_location_city']);
        });
    }
};
