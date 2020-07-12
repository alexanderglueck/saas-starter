<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    protected $keyType = 'string';

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    /**
     * @param Builder $query
     * @param int $id
     * @return Builder
     */
    public function scopeForUser($query, $id)
    {
        return $query->where('user_id', '=', $id);
    }
}
