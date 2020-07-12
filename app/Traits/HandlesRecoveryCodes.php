<?php

namespace App\Traits;

use App\BackupCode;
use App\Events\TwoFactorRecoveryCodesDepleted;
use App\Events\TwoFactorRecoveryCodesGenerated;
use Illuminate\Support\Collection;

trait HandlesRecoveryCodes
{
    /**
     * Returns if there are Recovery Codes available.
     *
     * @return bool
     */
    public function containsUnusedRecoveryCodes()
    {
        return $this->backupCodes()->whereNull('used_at')->exists();
    }

    /**
     * Generates a new set of Recovery Codes.
     *
     * @return \Illuminate\Support\Collection
     */
    public function generateRecoveryCodes(): Collection
    {
        $this->backupCodes()->delete();

        for ($i = 0; $i < 10; $i++) {
            $this->backupCodes()->create([
                'code' => static::generateRandomString(20)
            ]);
        }

        event(new TwoFactorRecoveryCodesGenerated($this));

        return $this->backupCodes;
    }

    public function backupCodes()
    {
        return $this->hasMany(BackupCode::class);
    }

    public function getUnusedRecoveryCode($code)
    {
        return $this->backupCodes()->where('code', $code)->whereNull('used_at')->first();
    }

    /**
     * Sets a Recovery Code as used.
     *
     * @param string $code
     * @return bool
     */
    public function setRecoveryCodeAsUsed(string $code)
    {
        if (null === $backupCode = $this->getUnusedRecoveryCode($code)) {
            return false;
        }

        $backupCode->update([
            'used_at' => now()
        ]);

        return true;
    }

    /**
     * Uses a one-time Recovery Code if there is one available.
     *
     * @param string $code
     * @return bool
     */
    protected function useRecoveryCode(string $code): bool
    {
        if ( ! $this->setRecoveryCodeAsUsed($code)) {
            return false;
        }

        if ( ! $this->containsUnusedRecoveryCodes()) {
            event(new TwoFactorRecoveryCodesDepleted($this));
        }

        return true;
    }

    public function flushRecoveryCodes()
    {
        $this->backupCodes()->delete();
    }
}
