<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    protected $table = 'calendars';

    protected $fillable = [
        'name',
        'starts_at',
        'ends_at',
        'color',
    ];

    public function scopeSearch(Builder $query, ?string $search)
    {
        $retval = $query->when($search, function ($query, string $search) {
            $query->where('date', 'like', "%{$search}%");
        });

        return $retval;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where( 'active', '=', 1);
    }

    public function scopeToSelect(): array
    {
        return $this->active()
            ->select(['id', 'date'])
            ->orderBy('date', 'asc')
            ->get()->toArray();
    }

    public function getCreatedAtAttribute()
    {
        return date('d-m-Y H:i', strtotime($this->attributes['created_at']));
    }

    public function getUpdatedAtAttribute()
    {
        return date('d-m-Y H:i', strtotime($this->attributes['updated_at']));
    }
}
