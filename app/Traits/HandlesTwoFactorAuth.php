<?php

namespace App\Traits;

use App\Events\TwoFactorDisabled;
use App\Events\TwoFactorEnabled;
use Illuminate\Support\Str;
use ParagonIE\ConstantTime\Base32;

trait HandlesTwoFactorAuth
{
    use HandlesRecoveryCodes;
    use HandlesCodes;
    use HandlesSafeDevices;
    use SerializesSharedSecret;

    /**
     * Code taken and modified from:
     * @see https://github.com/DarkGhostHunter/Laraguard
     */

    /**
     * Creates a new Two Factor Auth mechanisms from scratch, and returns a new Shared Secret.
     *
     * @return
     */
    public function createTwoFactorAuth()
    {
        $this->flushTwoFactorAuth()->save();

        return $this;
    }

    /**
     * Confirms the Shared Secret and fully enables the Two Factor Authentication.
     *
     * @param string $code
     * @return bool
     */
    public function confirmTwoFactorAuth(string $code): bool
    {
        if ($this->isTwoFactorAuthEnabled()) {
            return true;
        }

        if ($this->validateCode($code)) {
            $this->enableTwoFactorAuth();
            return true;
        }

        return false;
    }

    /**
     * Enables Two Factor Authentication for the given user.
     *
     * @return void
     */
    public function enableTwoFactorAuth(): void
    {
        $this->tfa_enabled_at = now();
        $this->save();

        $this->generateRecoveryCodes();

        event(new TwoFactorEnabled($this));
    }

    /**
     * Disables Two Factor Authentication for the given user.
     *
     * @return void
     */
    public function disableTwoFactorAuth(): void
    {
        $this->flushTwoFactorAuth()->save();

        event(new TwoFactorDisabled($this));
    }

    protected function flushTwoFactorAuth()
    {
        $this->flushRecoveryCodes();
        $this->flushSafeDevices();

        $this->tfa_enabled_at = null;
        $this->tfa_shared_secret = static::generateTwoFactorAuthRandomSecret();

        return $this;
    }

    /**
     * Validates the TOTP Code or Recovery Code.
     *
     * @param string $code
     * @return bool
     */
    public function validateTwoFactorCode(?string $code = null): bool
    {
        if ( ! $code || ! $this->isTwoFactorAuthEnabled()) {
            return false;
        }

        return $this->useRecoveryCode($code) || $this->validateCode($code);
    }

    /**
     * Determines if the User has Two Factor Authentication enabled.
     *
     * @return bool
     */
    public function isTwoFactorAuthEnabled(): bool
    {
        return $this->tfa_enabled_at !== null;
    }

    /**
     * Returns if the Two Factor Authentication is not been enabled.
     *
     * @return bool
     */
    public function isTwoFactorAuthDisabled()
    {
        return ! $this->isTwoFactorAuthEnabled();
    }

    /**
     * Creates a new Random Secret.
     *
     * @return string
     */
    protected static function generateTwoFactorAuthRandomSecret()
    {
        return Base32::encodeUpper(
            random_bytes(40)
        );
    }

    protected static function generateRandomString($length)
    {
        return Str::upper(Str::random($length));
    }
}
