<?php

namespace App\Http\Controllers;

use App\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        return view('session.index', [
            'sessions' => Session::query()->forUser($request->user()->id)->get()
        ]);
    }

    public function destroy(Request $request)
    {
        if ($request->has('session')) {
            $session = Session::find($request->input('session'));

            $session->delete();
        } else {
            $sessions = Session::query()->forUser($request->user()->id)->get();

            foreach ($sessions as $session) {
                if ($session->id == $request->session()->getId()) {
                    continue;
                }

                $session->delete();
            }
        }

        return redirect()->route('session.index');
    }
}
