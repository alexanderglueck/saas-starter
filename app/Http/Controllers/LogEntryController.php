<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogEntryController extends Controller
{
    public function index(Request $request)
    {
        return view('log_entry.index', [
            'logs' => $request->user()->logEntries()->latest()->paginate(50)
        ]);
    }
}
