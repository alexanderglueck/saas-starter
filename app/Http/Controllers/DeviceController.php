<?php

namespace App\Http\Controllers;

use App\SafeDevice;
use App\Session;
use Illuminate\Http\Request;

class DeviceController extends Controller
{


    public function destroy(Request $request)
    {
        if ($request->has('device')) {
            $device = SafeDevice::find($request->input('device'));

            $device->delete();
        } else {
            $devices = SafeDevice::query()->forUser($request->user()->id)->get();

            foreach ($devices as $device) {
                if ($device->id == $request->session()->getId()) {
                    continue;
                }

                $device->delete();
            }
        }

        return redirect()->route('user_settings.two-factor.show');
    }
}
