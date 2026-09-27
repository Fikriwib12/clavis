<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\RegisterTeam;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Resources\TeamResource;

class TeamRegistrationController extends Controller
{
    /**
     * Register the authenticated candidate's team for the tournament.
     */
    public function store(StoreTeamRequest $request, RegisterTeam $registerTeam): TeamResource
    {
        $team = $registerTeam->handle($request->user(), $request->validated());

        return TeamResource::make($team->load(['captain', 'member']))
            ->additional(['message' => 'Tim berhasil didaftarkan.']);
    }
}
