<?php

namespace App\Listeners;

use App\User;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

class EnforceTwoFactorAuth
{
    /**
     * Current Request being handled.
     *
     * @var Request
     */
    protected $request;

    /**
     * Credentials used for Login in.
     *
     * @var array
     */
    protected $credentials;

    /**
     * If the user should be remembered.
     *
     * @var bool
     */
    protected $remember;

    /**
     * Create the event listener.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Saves the credentials temporarily into the class instance.
     *
     * @param \Illuminate\Auth\Events\Attempting $event
     * @return void
     */
    public function saveCredentials(Attempting $event)
    {
        $this->credentials = $event->credentials;
        $this->remember = $event->remember;
    }

    public function checkTwoFactor(Validated $event)
    {
        if ($this->shouldUseTwoFactorAuth($event->user)) {

            if ($this->isSafeDevice($event->user) || ($this->hasCode() && $invalid = $this->hasValidCode($event->user))) {
                if ($this->rememberBrowser()) {
                    $this->addSafeDevice($event->user);
                }
                return;
            }

            $this->throwResponse($event->user, isset($invalid));
        } else {
            if ($this->isSafeDevice($event->user)) {
                $this->renewSafeDevice($event->user);
            }
        }
    }

    /**
     * @param User $user
     * @return bool
     */
    protected function shouldUseTwoFactorAuth($user = null): bool
    {
        $shouldUse = $user->isTwoFactorAuthEnabled();

        return $shouldUse && ! $user->isSafeDevice($this->request);
    }

    /**
     * @return bool
     */
    protected function hasCode(): bool
    {
        return $this->request->has('token');
    }

    /**
     * @param User $user
     * @return bool
     */
    protected function hasValidCode($user)
    {
        return ! validator($this->request->only('token'), ['token' => 'alphanum'])->fails()
            && $user->validateTwoFactorCode($this->request->input('token'));
    }

    /**
     * @param User $user
     * @return mixed
     */
    protected function isSafeDevice($user)
    {
        return $user->isSafeDevice($this->request);
    }

    /**
     * @param User $user
     */
    protected function addSafeDevice($user)
    {
        $user->addSafeDevice($this->request);
    }

    /**
     * @param User $user
     * @param bool $error
     */
    protected function throwResponse($user, bool $error = false)
    {
        $view = view('auth.twofactor.auth', [
            'action' => request()->fullUrl(),
            'credentials' => $this->credentials,
            'user' => $user,
            'error' => $error,
            'remember' => $this->remember,
        ]);

        return response($view, $error ? 422 : 403)->throwResponse();
    }

    /**
     * @return bool
     */
    public function rememberBrowser(): bool
    {
        return $this->request->has('remember_browser');
    }

    /**
     * @param User $user
     */
    public function renewSafeDevice($user)
    {
        $user->renewSafeDevice($this->request);
    }
}
