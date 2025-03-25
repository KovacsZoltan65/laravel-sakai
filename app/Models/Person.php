<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = [
        'name',
        'email',
        'address',
        'phone',
    ];

    public function scopeSearch(Builder $query, ?string $search)
    {
        $retval = $query->when($search, function ($query, string $search) {
            $query->where('name', 'like', "%{$search}%");
        });

        return $retval;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where( 'active', '=', 1);
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
