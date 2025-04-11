<?php

/**
 * Régiók, megyék
 */

namespace App\Models\Geo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Override;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Region
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $country_id
 *
 * @package App\Models
 */
class Region extends Model
{
    use HasFactory,
        LogsActivity;

    protected $table = 'regions';

    protected $casts = [
        'country_id' => 'integer',
        'active' => 'integer',
    ];

    protected $fillable = [
        'name',
        'code',
        'country_id'
    ];

    /*
     * ==============================================================
     * LOGOLÁS
     * ==============================================================
     */

    // Ha szeretnéd, hogy minden mezőt automatikusan naplózzon:
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true; // Csak a változásokat naplózza
    protected static $logName = 'regions';
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

    /**
     * A régiók listázásához keresési feltételek
     *
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    public function scopeSearch(Builder $query, Request $request): Builder
    {
        return $query->when($request->search, function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%");
            });
        })->where('active', 1);
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

    public function scopeByCountryToSelect(int $countryId): array
    {
        return $this->active()->where('country_id', '=', $countryId)
            ->select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get()->toArray();
    }

    /**
     * A régióhoz tartozó ország
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    /**
     * A régióban levő városok
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'region_id', 'id');
    }

    #[Override]
    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->logAll();
    }
}
