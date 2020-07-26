<?php

namespace App;

use App\User;
use App\Traits\HasPermissions;
use App\Traits\RefreshesPermissionCache;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasPermissions, RefreshesPermissionCache;

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

    public function syncUsers($users)
    {
        $currentUsers = $this->users;

        foreach ($currentUsers as $user) {
            /** @var $user User */
            $user->removeRole($this->id);
        }

        if (is_array($users)) {
            foreach ($users as $user) {
                User::find($user)->assignRole($this->id);
            }
        }
    }

    public function hasPermissionTo($permission)
    {
        return $this->hasDirectPermission($permission);
    }

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

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
