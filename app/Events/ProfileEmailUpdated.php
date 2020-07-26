<?php


namespace App\Events;

use App\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProfileEmailUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * The User
     *
     * @var User user
     */
    public $user;

    /**
     * The users old email
     *
     * @var User user
     */
    public $oldEmail;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param string $oldEmail
     * @return void
     */
    public function __construct(User $user, string $oldEmail)
    {
        $this->user = $user;
        $this->oldEmail = $oldEmail;
    }
}
