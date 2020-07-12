<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'team_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'team_id' => 'integer',
    ];


    public function users()
    {
        return $this->hasMany(\App\User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(\App\Permission::class);
    }

    public function team()
    {
        return $this->belongsTo(\App\Team::class);
    }
}
