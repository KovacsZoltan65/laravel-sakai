<?php

/**
 * Oszágok
 */

namespace App\Models\Geo;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Override;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Country
 *
 * @property int $id
 * @property string $name
 * @property string $code
 *
 * @package App\Models
 */
class Country extends Model
{
    use HasFactory,
        LogsActivity;

    protected $table = 'countries';
    public $timestamps = false;

    protected $fillable = ['name', 'code', 'active'];

    protected $casts = [
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
    protected static $logName = 'country';

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

    public function scopeSearch(Builder $query, Request $request): Builder
    {
        return $query->when($request->search, function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%");
            });
        })->where('active', APP_ACTIVE);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    public function scopeToSelect(): array
    {
        return $this->active()
            ->select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get()->toArray();
    }

    /**
     * A kapcsolódó városok listája.
     *
     * Egy országnak több városa is lehet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cities()
    {
        return $this->hasMany(City::class); // keres: cities.country_id → countries.id
    }

    /**
     * A kapcsolódó régiók listája.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function regions()
    {
        return $this->hasMany(Region::class); // keres: regions.country_id → countries.id
    }

    #[Override]
    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->logAll();
    }
}
