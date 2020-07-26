<?php

namespace App;

use App\Traits\HasConfirmationTokens;
use App\Traits\HasRoles;
use App\Traits\HasSubscriptions;
use App\Traits\HandlesTwoFactorAuth;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription;
use Mpociot\Teamwork\Traits\UserHasTeams;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use HandlesTwoFactorAuth;
    use HasRoles;
    use HasConfirmationTokens;
    use Billable;
    use HasSubscriptions;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'team_id', 'uuid', 'trial_ends_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'tfa_shared_secret'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = [
        'seen_at',
        'trial_ends_at'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function logEntries()
    {
        return $this->hasMany(LogEntry::class);
    }

    public function isOwnerOfTeam($team)
    {
        return $team->owner_id == $this->id;
    }

    public function plan()
    {
        return $this->plans->first();
    }

    public function getPlanAttribute()
    {
        return $this->plan();
    }

    public function plans()
    {
        return $this->hasManyThrough(
            Plan::class,
            Subscription::class,
            'user_id',
            'gateway_id',
            'id',
            'stripe_plan'
        )->orderBy('subscriptions.created_at', 'desc');
    }

    /**
     * Defines the has-many relationship with the BackupCode model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function backupCodes()
    {
        return $this->hasMany(BackupCode::class, 'user_id');
    }

    public function hasImage()
    {
        return trim($this->image) !== '';
    }

    public function hasTwoFactorAuthentication()
    {
        return trim($this->google2fa_secret) !== '';
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
