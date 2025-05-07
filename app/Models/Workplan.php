<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Workplan extends Model
{
    use HasFactory,
        SoftDeletes,
        LogsActivity;

    protected $table = 'workplans';

    protected $fillable = [
        'name', 'code',
        'company_id',
        'daily_workhours',
        //'start_date','end_date', 
        'active'
    ];

    protected $attributes = [
        'daily_workhours' => 8
    ];

    protected $casts = [
        'active' => 'integer',
        //'start_date' => 'datetime:Y-m-d',
        //'end_date' => 'datetime:Y-m-d',
        'daily_workhours' => 'integer'
    ];

    public function getStartDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function getEndDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function scopeToSelect(): array
    {
        return $this->active()
            ->select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get()->toArray();
    }

    /*
     * ==============================================================
     * LOGOLÁS
     * ==============================================================
     */

    // Ha szeretnéd, hogy minden mezőt automatikusan naplózzon:
    protected static $logAttributes = [
        'name', 'code',
        'company_id',
        'daily_workhours',
        //'start_date','end_date', 
        'active'
    ];
    protected static $logOnlyDirty = true; // Csak a változásokat naplózza
    protected static $logName = 'workplans'; // Naplózás név
    protected static $recordEvents = [
        'created',
        'updated',
        'deleted',
    ];

    /*
     * ==============================================================
     */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', '=', 1);
    }

    public function scopeWithArchived(Builder $query)
    {
        return $query->withTrashed();
    }


    #[Override]
    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable();
    }
}
