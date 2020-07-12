<?php

namespace App\Events;

use App\User;

class TwoFactorRecoveryCodesDepleted
{
    public $user;

    /**
     * TwoFactorRecoveryCodesDepleted constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
