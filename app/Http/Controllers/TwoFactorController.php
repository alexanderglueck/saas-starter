<?php

namespace App\Http\Controllers;

use App\LogEntry;
use App\SafeDevice;
use App\User;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function show(Request $request)
    {
        if ( ! $request->user()->isTwoFactorAuthEnabled()) {
            return redirect()->route('user_settings.two-factor.create');
        }

        return view('user_settings.twofactor.show', [
            'backupCodes' => $request->user()->backupCodes,
            'devices' => SafeDevice::query()->forUser($request->user()->id)->get()
        ]);
    }

    public function create(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $user->createTwoFactorAuth();

        return view('user_settings.twofactor.create', [
            'as_qr_code' => $user->getTwoFactorAuthQRCode(),     // As QR Code
            'as_uri' => $user->getTwoFactorAuthUri(),    // As "otpauth://" URI.
            'as_string' => $user->tfa_shared_secret, // As a string
        ]);
    }

    public function store(Request $request)
    {
        if ($request->user()->confirmTwoFactorAuth($request->input('token'))) {
            return redirect()->route('user_settings.two-factor.show');
        } else {
            return back();
        }
    }

    public function destroy(Request $request)
    {
        $request->user()->disableTwoFactorAuth();

        return redirect()->route('user_settings.two-factor.create');
    }

    public function generateRecoveryCodes(Request $request)
    {
        return $request->user()->generateRecoveryCodes();
    }
}
