<?php


namespace App\Events;


use App\User;

class TwoFactorDisabled
{
    /**
     * The User using Two Factor Authentication.
     *
     * @var User user
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
