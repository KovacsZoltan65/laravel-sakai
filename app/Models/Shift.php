<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Override;

class Shift extends Model
{
    use HasFactory,
        SoftDeletes,
        LogsActivity;

    protected $table = 'shifts';

    protected $fillable = ['name','code','start_time','end_time', 'active'];

    protected $casts = [
        'active' => 'integer',
        'start_time' => 'datetime:Y-m-d',
        'end_time' => 'datetime:Y-m-d',
    ];

    public function scopeToSelect(): array
    {
        return $this->active()
            ->select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get()->toArray();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', '=', 1);
    }

    public function scopeWithArchived(Builder $query)
    {
        return $query->withTrashed();
    }

    /*
     * ==============================================================
     * LOGOLÁS
     * ==============================================================
     */

    // Ha szeretnéd, hogy minden mezőt automatikusan naplózzon:
    protected static $logAttributes = [
        'name', 'email',
        'start_date', 'end_date',
        'last_export', 'user_id',
        'company_id', 'active'
    ];
    protected static $logOnlyDirty = true; // Csak a változásokat naplózza
    protected static $logName = 'entities'; // Naplózás név
    protected static $recordEvents = [
        'created',
        'updated',
        'deleted',
    ];

    /*
     * ==============================================================
     */

    #[Override]
    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable();
    }
}
