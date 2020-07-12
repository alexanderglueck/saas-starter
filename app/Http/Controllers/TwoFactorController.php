<?php


namespace App\Http\Controllers;


use App\User;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function create(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $user->createTwoFactorAuth();

        return view('twofactor.create', [
            'as_qr_code' => $user->getTwoFactorAuthQRCode(),     // As QR Code
            'as_uri' => $user->getTwoFactorAuthUri(),    // As "otpauth://" URI.
            'as_string' => $user->tfa_shared_secret, // As a string
        ]);
    }

    public function store(Request $request)
    {
        if ($request->user()->confirmTwoFactorAuth($request->input('token'))) {
            return redirect()->route('two-factor.show');
        } else {
            return back();
        }
    }

    public function show(Request $request)
    {
        if ( ! $request->user()->isTwoFactorAuthEnabled()) {
            return redirect()->route('two-factor.create');
        }

        return view('twofactor.show');
    }

    public function destroy(Request $request)
    {
        $request->user()->disableTwoFactorAuth();

        return redirect()->route('two-factor.create');
    }

    public function generateRecoveryCodes(Request $request)
    {
        return $request->user()->generateRecoveryCodes();
    }
}
