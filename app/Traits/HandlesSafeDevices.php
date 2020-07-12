<?php

namespace App\Traits;

use App\SafeDevice;
use Illuminate\Http\Request;

trait HandlesSafeDevices
{
    public function isSafeDevice(Request $request)
    {
        return $this->safeDevices()
            ->where('token', $this->getTwoFactorAuthSafeDeviceTokenFromRequest($request))
            ->exists();
    }

    /**
     * Determines if the Request has been made through a not-previously-known device.
     *
     * @param null|\Illuminate\Http\Request $request
     * @return bool
     */
    public function isNotSafeDevice(Request $request): bool
    {
        return ! $this->isSafeDevice($request);
    }

    protected function getTwoFactorAuthSafeDeviceTokenFromRequest(Request $request)
    {
        return $request->cookie('2fa_remember');
    }

    public function safeDevices()
    {
        return $this->hasMany(SafeDevice::class);
    }

    public function flushSafeDevices()
    {
        $this->safeDevices()->delete();
    }

    /**
     * Adds a "safe" Device from the Request.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function addSafeDevice(Request $request): string
    {
        $this->safeDevices()->create([
            'name' => $request->userAgent(),
            'token' => $token = $this->generateRandomString(50),
            'ip' => $request->ip(),
            'added_at' => now(),
        ]);

        cookie()->queue('2fa_remember', $token, 30 * 1440);

        return $token;
    }

    public function renewSafeDevice(Request $request)
    {
        $token = $this->generateRandomString(50);

        $this->safeDevices()->where('token', $this->getTwoFactorAuthSafeDeviceTokenFromRequest($request))
            ->update([
                'token' => $token,
                'ip' => $request->ip(),
            ]);


        cookie()->queue('2fa_remember', $token, 30 * 1440);
    }
}
