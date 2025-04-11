<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';

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

    public function scopeToSelect(): array
    {
        return $this->active()
            ->select(['id', 'name'])
            ->orderBy('name', 'asc')
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
    
    public function entities()
    {
        return $this->hasMany(Entity::class);
    }
}
