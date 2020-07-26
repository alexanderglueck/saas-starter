<?php

namespace App\Http\Controllers\Account;

use App\Events\ProfileEmailUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Account\ProfileUpdateRequest;

class ProfileController extends Controller
{
    public function show()
    {
        return view('user_settings.profile.show');
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $oldEmail = $user->email;

        if ($user->update($request->only('name', 'email'))) {
            Session::flash('alert-success', trans('flash_message.settings.profile.updated'));

            if ($oldEmail != $user->email) {
                event(new ProfileEmailUpdated($user, $oldEmail));
            }
        } else {
            Session::flash('alert-danger', trans('flash_message.settings.profile.not_updated'));
        }

        return back();
    }
}
