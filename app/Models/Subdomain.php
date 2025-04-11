<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Subdomain extends Model
{
    use HasFactory,
        SoftDeletes,
        LogsActivity;

    protected $table = 'subdomains';

    protected $fillable = [
        'subdomain',
        'url',
        'name',
        'db_host',
        'db_port',
        'db_name',
        'db_user',
        'db_password',
        'notification',
        'state_id',
        'is_mirror',
        'sso',
        'acs_id',
        'active',
    ];

    protected $casts = [
        'db_port' => 'integer',
        'notification' => 'bool',
        'state_id' => 'integer',
        'is_mirror' => 'bool',
        'sso' => 'bool',
        'acs_id' => 'int',
        'active' => 'integer',
    ];

    /*
     * ==============================================================
     * LOGOLÁS
     * ==============================================================
     */

    // Ha szeretnéd, hogy minden mezőt automatikusan naplózzon:
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true; // Csak a változásokat naplózza
    protected static $logName = 'subdomain';

    protected static $recordEvents = [
        'created',
        'updated',
        'deleted',
    ];

    /*
     * ==============================================================
     */

    public static function getTag(): string
    {
        return self::$logName;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', '=', 1);
    }

    public function scopeToSelect(): array
    {
        return $this->active()
            ->select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get()->toArray();
    }

    public function subdomainState(): BelongsTo
    {
        return $this->belongsTo(SubdomainState::class, 'state_id');
    }

    #[Override()]
    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->logAll();
    }
}
