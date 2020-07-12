<?php

namespace App\Http\Controllers;

use App\Country;
use Illuminate\Http\Request;

class SetupController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return view('setup.show', [
            'team' => $user->team
        ]);
    }
}
