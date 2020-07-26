<?php

namespace App\Events;

use App\User;
use Illuminate\Queue\SerializesModels;

class TwoFactorRecoveryCodesGenerated
{
    use SerializesModels;

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
