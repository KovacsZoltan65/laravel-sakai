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
        DB::statement(<<<SQL
        CREATE OR REPLACE VIEW view_entity_calendar_daily AS
        SELECT
            ec.id                 AS calendar_id,
            ec.entity_id,
            ec.year,
            ec.month,
            jt.day               AS day,
            CONCAT(ec.year, '-', LPAD(ec.month, 2, '0'), '-', jt.day) AS full_date,

            jt.shift_id,
            jt.planned_shift_start,
            jt.planned_shift_end,
            jt.actual_shift_start,
            jt.actual_shift_end,
            jt.worked_hours,
            jt.note

        FROM entity_calendar ec
        JOIN JSON_TABLE(
            ec.calendar_data,
            '$.*'
            COLUMNS (
                day VARCHAR(2) PATH '$.key',
                shift_id INT PATH '$.shift_id',
                planned_shift_start VARCHAR(5) PATH '$.planned_shift.start',
                planned_shift_end   VARCHAR(5) PATH '$.planned_shift.end',
                actual_shift_start  VARCHAR(5) PATH '$.actual_shift.start',
                actual_shift_end    VARCHAR(5) PATH '$.actual_shift.end',
                worked_hours DECIMAL(5,2) PATH '$.worked_hours',
                note VARCHAR(255) PATH '$.note'
            )
        ) AS jt;
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS view_entity_calendar_daily");
    }
};
