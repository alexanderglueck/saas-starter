<?php

namespace App\Http\Controllers\Teamwork;

use Exception;
use App\Team;
use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Team\TeamStoreRequest;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        return view('teamwork.index', [
            'team' => auth()->user()->team
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        $team = Team::findOrFail($id);

        if ( ! auth()->user()->isOwnerOfTeam($team)) {
            abort(403);
        }

        return view('teamwork.edit')->withTeam($team);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TeamStoreRequest $request
     * @param int              $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(TeamStoreRequest $request, $id)
    {
        if ($this->isImpersonating()) {
            return redirect()->route('home');
        }

        $team = Team::findOrFail($id);
        $team->name = $request->name;
        $team->save();

        return redirect(route('teams.index'));
    }
}
