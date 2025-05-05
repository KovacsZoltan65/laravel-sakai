<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityCalendar extends Model
{
    use HasFactory;

    protected $table = 'entity_calendar';

    protected $fillable = [
        'entity_id',
        'year',
        'month',
        'calendar_data',
        'total_hours',
        'total_days_worked',
        'total_leaves',
    ];

    protected $casts = [
        'calendar_data' => 'array',
        'total_hours' => 'integer',
        'total_days_worked' => 'integer',
        'total_leaves' => 'integer',
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Számítsa ki egy adott napra a munkaidőt (actual_shift - breaks)
     *
     * @param array $dayData A napi adat a calendar_data-ból
     * @return float|null
     */
    public static function calculateWorkedHours(array $dayData): ?float
    {
        if (empty($dayData['actual_shift']['start']) || empty($dayData['actual_shift']['end'])) {
            return null;
        }

        $start = \Carbon\Carbon::createFromFormat('H:i', $dayData['actual_shift']['start']);
        $end   = \Carbon\Carbon::createFromFormat('H:i', $dayData['actual_shift']['end']);

        if ($end->lessThan($start)) {
            $end->addDay(); // átnyúl éjfél utánra
        }

        $totalMinutes = $start->diffInMinutes($end);

        if (!empty($dayData['breaks']) && is_array($dayData['breaks'])) {
            foreach ($dayData['breaks'] as $break) {
                if (!empty($break['start']) && !empty($break['end'])) {
                    $bStart = \Carbon\Carbon::createFromFormat('H:i', $break['start']);
                    $bEnd   = \Carbon\Carbon::createFromFormat('H:i', $break['end']);

                    if ($bEnd->lessThan($bStart)) {
                        $bEnd->addDay();
                    }

                    $totalMinutes -= $bStart->diffInMinutes($bEnd);
                }
            }
        }

        return round($totalMinutes / 60, 2);
    }
}
