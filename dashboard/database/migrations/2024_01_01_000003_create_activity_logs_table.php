<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DESIGN DECISION: activity_logs is append-only.
 *
 * Each row = one status-change event. We never UPDATE a row in this table.
 * Rationale: FR-4 (shift handover) requires showing "who updated what and when"
 * as a timeline — impossible with a last-write-wins model. The current status
 * of an activity for a given day is derived as the status of the LATEST log entry
 * (MAX id GROUP BY activity_id + date). See ReportingService for the query pattern.
 *
 * Tradeoff: Slightly more rows, but the volume is bounded (a support team logs
 * ~10–50 status changes per day). The composite index on (date, activity_id) keeps
 * the daily-view query fast. At 100x scale, a materialized "current_status" read
 * model table would be added and refreshed on write.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['pending', 'done'])->default('pending');
            $table->text('remark')->nullable();

            // Denormalised actor snapshot — never rely solely on FK because
            // users can be renamed/deleted; the audit trail must be immutable.
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_name');
            $table->string('actor_role')->nullable();
            $table->string('actor_designation')->nullable();
            $table->string('actor_ip', 45)->nullable();

            $table->timestamps();

            // Primary reporting index: date-range queries + daily view
            $table->index(['date', 'activity_id']);
            // Status filtering in reports
            $table->index(['date', 'status']);
            // Actor lookup
            $table->index('updated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
