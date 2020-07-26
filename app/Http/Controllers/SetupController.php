<?php

namespace App\Http\Controllers;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required'
        ]);

        $request->user()->team->update($validated);

        return redirect()->route('home');
    }
}
