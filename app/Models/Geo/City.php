<?php

/**
 * Városok
 */

namespace App\Models\Geo;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Override;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class City extends Model
{
    use HasFactory,
        SoftDeletes,
        LogsActivity;

    protected $table = 'cities';

    protected $fillable = [
        'region_id',
        'country_id',
        'latitude',
        'longitude',
        'name'
    ];

    protected $casts = [
        'region_id' => 'int',
        'country_id' => 'int',
        'latitude' => 'float',
        'longitude' => 'float',
        'active' => 'integer'
    ];

    /*
     * ==============================================================
     * LOGOLÁS
     * ==============================================================
     */
    // Ha szeretnéd, hogy minden mezőt automatikusan naplózzon:
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true; // Csak a változásokat naplózza
    protected static $logName = 'city';

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

    public function scopeSearch(Builder $query, Request $request)
    {
        /**
         * Módosítsa a lekérdezést a keresési paraméter alapján.
         *
         * Ha a keresési paraméter nem üres, akkor a lekérdezés tartalmazza
         * a feltételt, hogy a város neve tartalmazza a keresési paramétert.
         *
         * @param Builder $query A lekérdezéskészítő példány.
         * @param Request $request A keresési paramétert tartalmazó HTTP kérés objektum.
         * @return Builder A módosított lekérdezéskészítő példány.
         */
        return $query->when($request->search, function (Builder $query) use ($request) {
            return $query->where(function (Builder $query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%");
            });
        });
    }

    /**
     * Határozza meg a lekérdezést, hogy csak az aktív városokat tartalmazza.
     *
     * @param Builder $query A lekérdezéskészítő példány.
     * @return Builder A módosított lekérdezéskészítő példány.
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('active', '=', 1);
    }

    public function scopeByRegionToSelect(int $regionId): array
    {
        return $this->active()
            ->where('region_id', '=', $regionId)
            ->select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->toArray();
    }

    public function scopeByCountryToSelect(int $countryId): array
    {
        return $this->active()
            ->where('country_id', '=', $countryId)
            ->select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->toArray();
    }

    /**
     * Szerezd meg a várost birtokló régiót
     *
     * @return BelongsTo
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * Szerezd meg azt az országot, amelyik a város tulajdonosa
     *
     * @return BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    #[Override]
    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()
            ->logFillable()
            ->logAll();
    }
}
