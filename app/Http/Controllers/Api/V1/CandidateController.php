<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CandidateResource;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    /**
     * Show the authenticated candidate with its registered team, if any.
     */
    public function show(Request $request): CandidateResource
    {
        return CandidateResource::make($request->user()->load(['team.captain', 'team.member']));
    }
}
