<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Import the DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('talent_requests', function (Blueprint $table) {
            if (Schema::hasColumn('talent_requests', 'talent_id')) {
                // The foreign key constraint on talent_id was conditionally added in the original migration
                // for non-SQLite databases. For SQLite, it was just a column.
                // So, we only need to explicitly drop the foreign key for non-SQLite databases.
                if (DB::getDriverName() !== 'sqlite') {
                    try {
                        $table->dropForeign(['talent_id']);
                    } catch (\Exception $e) {
                        // Fallback: Attempt to drop by specific conventional name if array fails
                        // This might be necessary if the FK name isn't the default.
                        // Log::warning("Could not drop FK 'talent_requests_talent_id_foreign' by array: " . $e->getMessage());
                        // try { $table->dropForeign('talent_requests_talent_id_foreign'); } catch (\Exception $ex) { }
                    }
                }
                $table->dropColumn('talent_id');
            }
        });

        // Create a new pivot table for many-to-many relationship
        Schema::create('talent_request_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('talent_request_id')->constrained('talent_requests')->cascadeOnDelete();
            $table->foreignId('talent_id')->constrained('users')->cascadeOnDelete(); // This refers to the users table (talents are users)
            $table->string('status')->default('pending_assignment_response');
            $table->timestamps();
            $table->unique(['talent_request_id', 'talent_id'], 'talent_request_talent_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talent_request_assignments');

        Schema::table('talent_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('talent_requests', 'talent_id')) {
                // Re-add the talent_id column.
                // Conditionally add the foreign key constraint, similar to the original migration.
                if (DB::getDriverName() !== 'sqlite') {
                    $table->foreignId('talent_id')->nullable()->constrained('users')->cascadeOnDelete();
                } else {
                    $table->foreignId('talent_id')->nullable();
                }
            }
        });
    }
};
