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
        // Schema::table('talent_request_assignments', function (Blueprint $table) {
        //     // These columns are already added in the 2025_05_20_155204_alter_talent_requests_for_multiple_talents.php migration
        //     // $table->string('assignment_type')->nullable()->after('status'); // Add assignment_type column
        //     // $table->foreignId('assigned_by')->nullable()->after('assignment_type')->constrained('users')->onDelete('set null'); // Add assigned_by column
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('talent_request_assignments', function (Blueprint $table) {
        //     // These columns are handled by the 2025_05_20_155204_alter_talent_requests_for_multiple_talents.php migration's down method
        //     // if (Schema::hasColumn('talent_request_assignments', 'assigned_by')) {
        //     //     $table->dropForeign(['assigned_by']);
        //     //     $table->dropColumn('assigned_by');
        //     // }
        //     // if (Schema::hasColumn('talent_request_assignments', 'assignment_type')) {
        //     //     $table->dropColumn('assignment_type');
        //     // }
        // });
    }
};
