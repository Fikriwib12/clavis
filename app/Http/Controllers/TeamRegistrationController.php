<?php

namespace App\Http\Controllers;

use App\Actions\RegisterTeam;
use App\Enums\Gender;
use App\Http\Requests\StoreTeamRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamRegistrationController extends Controller
{
    /**
     * Show the team registration form with the captain filled in from the session user.
     */
    public function create(Request $request): View
    {
        return view('tournament.create', [
            'user' => $request->user(),
            'genders' => Gender::cases(),
        ]);
    }

    /**
     * Register the user's team for the tournament.
     */
    public function store(StoreTeamRequest $request, RegisterTeam $registerTeam): RedirectResponse
    {
        $registerTeam->handle($request->user(), $request->validated());

        return redirect()->route('tournament.finish');
    }

    /**
     * Show the confirmation page for a registered team.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $team = $request->user()->team;

        if ($team === null) {
            return redirect()->route('tournament.create');
        }

        return view('tournament.finish', ['team' => $team]);
    }
}
