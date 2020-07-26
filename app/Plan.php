<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'gateway_id',
        'price',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'teams_enabled' => 'boolean',
        'active' => 'boolean'
    ];

    public function scopeActive(Builder $builder)
    {
        return $builder->where('active', true);
    }

    public function scopeForUsers(Builder $builder)
    {
        return $builder->where('teams_enabled', false);
    }

    public function scopeForTeams(Builder $builder)
    {
        return $builder->where('teams_enabled', true);
    }

    public function scopeExcept(Builder $builder, $id)
    {
        return $builder->where('id', '!=', $id);
    }

    public function isForTeams()
    {
        return $this->teams_enabled;
    }

    public function isNotForTeams()
    {
        return ! $this->isForTeams();
    }
}
