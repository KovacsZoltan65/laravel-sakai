<?php

namespace Database\Factories;

use App\Models\EntityCalendar;
use App\Models\Entity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EntityCalendar>
 */
class EntityCalendarFactory extends Factory
{
    protected $model = EntityCalendar::class;

    public function definition(): array
    {
        $year = now()->year;
        $month = now()->month;
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $calendarData = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayKey = str_pad($day, 2, '0', STR_PAD_LEFT);

            $start = $this->faker->randomElement(['08:00', '09:00', '07:30']);
            $end   = $this->faker->randomElement(['16:00', '17:00', '15:30']);
            $break = ['start' => '12:00', 'end' => '12:30'];

            $workedHours = rand(6, 8) - 0.5; // egyszerűsített érték

            $calendarData[$dayKey] = [
                'shift_id' => rand(1, 3),
                'planned_shift' => ['start' => $start, 'end' => $end],
                'actual_shift' => ['start' => $start, 'end' => $end],
                'breaks' => [$break],
                'worked_hours' => $workedHours,
                'note' => $this->faker->optional()->sentence(),
            ];
        }

        return [
            'entity_id' => Entity::factory(), // feltételezzük, hogy van Entity factory
            'year' => $year,
            'month' => $month,
            'calendar_data' => $calendarData,
            'total_hours' => array_sum(array_column($calendarData, 'worked_hours')),
            'total_days_worked' => count(array_filter($calendarData, fn($d) => ($d['worked_hours'] ?? 0) > 0)),
            'total_leaves' => 0, // egyszerűsítve most 0
        ];
    }
}
