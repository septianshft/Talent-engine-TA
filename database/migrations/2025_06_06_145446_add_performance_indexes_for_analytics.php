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
        // Add indexes for users table (talent analytics)
        Schema::table('users', function (Blueprint $table) {
            $table->index(['created_at'], 'idx_users_created_at');
            $table->index(['domicile_city'], 'idx_users_domicile_city');
            $table->index(['domicile_country'], 'idx_users_domicile_country');
            $table->index(['location'], 'idx_users_location');
            $table->index(['can_work_remote'], 'idx_users_can_work_remote');
        });

        // Add indexes for talent_requests table (analytics queries)
        Schema::table('talent_requests', function (Blueprint $table) {
            $table->index(['created_at'], 'idx_talent_requests_created_at');
            $table->index(['status'], 'idx_talent_requests_status');
            $table->index(['work_location_type'], 'idx_talent_requests_work_location_type');
            $table->index(['updated_at'], 'idx_talent_requests_updated_at');
        });

        // Add indexes for competency_user table (proficiency analytics)
        Schema::table('competency_user', function (Blueprint $table) {
            $table->index(['proficiency_level'], 'idx_competency_user_proficiency');
            $table->index(['user_id', 'proficiency_level'], 'idx_competency_user_composite');
        });

        // Add indexes for competencies table
        Schema::table('competencies', function (Blueprint $table) {
            $table->index(['category'], 'idx_competencies_category');
            $table->index(['name'], 'idx_competencies_name');
        });

        // Add indexes for talent_searches table (search analytics)
        Schema::table('talent_searches', function (Blueprint $table) {
            $table->index(['created_at'], 'idx_talent_searches_created_at');
            $table->index(['user_id', 'created_at'], 'idx_talent_searches_user_date');
        });

        // Add indexes for talent_shortlists table
        Schema::table('talent_shortlists', function (Blueprint $table) {
            $table->index(['created_at'], 'idx_talent_shortlists_created_at');
            $table->index(['user_id'], 'idx_talent_shortlists_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_created_at');
            $table->dropIndex('idx_users_domicile_city');
            $table->dropIndex('idx_users_domicile_country');
            $table->dropIndex('idx_users_location');
            $table->dropIndex('idx_users_can_work_remote');
        });

        // Remove indexes from talent_requests table
        Schema::table('talent_requests', function (Blueprint $table) {
            $table->dropIndex('idx_talent_requests_created_at');
            $table->dropIndex('idx_talent_requests_status');
            $table->dropIndex('idx_talent_requests_work_location_type');
            $table->dropIndex('idx_talent_requests_updated_at');
        });

        // Remove indexes from competency_user table
        Schema::table('competency_user', function (Blueprint $table) {
            $table->dropIndex('idx_competency_user_proficiency');
            $table->dropIndex('idx_competency_user_composite');
        });

        // Remove indexes from competencies table
        Schema::table('competencies', function (Blueprint $table) {
            $table->dropIndex('idx_competencies_category');
            $table->dropIndex('idx_competencies_name');
        });

        // Remove indexes from talent_searches table
        Schema::table('talent_searches', function (Blueprint $table) {
            $table->dropIndex('idx_talent_searches_created_at');
            $table->dropIndex('idx_talent_searches_user_date');
        });

        // Remove indexes from talent_shortlists table
        Schema::table('talent_shortlists', function (Blueprint $table) {
            $table->dropIndex('idx_talent_shortlists_created_at');
            $table->dropIndex('idx_talent_shortlists_user_id');
        });
    }
};
