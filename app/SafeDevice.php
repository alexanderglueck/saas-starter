<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SafeDevice extends Model
{
    protected $fillable = [
        'token', 'ip', 'added_at', 'name'
    ];

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
