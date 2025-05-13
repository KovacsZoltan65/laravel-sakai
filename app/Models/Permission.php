<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as ModelsPermission;
use Illuminate\Database\Eloquent\Builder;


class Permission extends ModelsPermission
{
    use HasFactory;

    public function getCreatedAtAttribute() {
        return date('d-m-Y H:i', strtotime($this->attributes['created_at']));
    }

    public function getUpdatedAtAttribute() {
        return date('d-m-Y H:i', strtotime($this->attributes['updated_at']));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', '=', 1);
    }

    public function scopeToSelect(): array
    {
        return $this->select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get()->toArray();
    }
}
