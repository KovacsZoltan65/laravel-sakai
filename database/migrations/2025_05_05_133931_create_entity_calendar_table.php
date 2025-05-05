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
        Schema::create('entity_calendar', function (Blueprint $table) {
            $table->id()->comment('Rekord azonosító');

            // Foreign key to users (entity)
            // Helyes típus és foreign kulcs kapcsolás
            $table->foreignId('entity_id')
                ->constrained('entities')
                ->cascadeOnDelete()
                ->comment('Entitás azonosító');


            // Year and month of the record
            $table->integer('year')->comment('Naptári év');
            $table->integer('month')->comment('Naptári hónap (1-12)');

            // JSON column for per-day data: {"01": {"hours":8, "note":"..."}, ...}
            $table->json('calendar_data')->comment('Daily data JSON indexed by day of month');

            // Summary columns for fast querying and reporting
            $table->unsignedInteger('total_hours')->default(0)
                  ->comment('Summed work hours for month');
            $table->unsignedTinyInteger('total_days_worked')->default(0)
                  ->comment('Count of days with hours > 0');
            $table->unsignedTinyInteger('total_leaves')->default(0)
                  ->comment('Count of days marked as leave');

            $table->timestamps();

            // Composite unique index for fast lookup by user/year/month
            $table->unique(['entity_id', 'year', 'month'], 'ecm_entity_year_month_unique');
            $table->index(['total_hours'], 'ecm_total_hours_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_calendar');
    }
};
