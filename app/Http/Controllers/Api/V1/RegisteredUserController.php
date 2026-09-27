<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\CandidateResource;
use App\Models\User;

class RegisteredUserController extends Controller
{
    /**
     * Create a candidate account.
     */
    public function store(RegisterUserRequest $request): CandidateResource
    {
        $user = User::create($request->validated());

        return CandidateResource::make($user)
            ->additional(['message' => 'Registrasi akun berhasil. Silakan login untuk mendapatkan token.']);
    }
}
