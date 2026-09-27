<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccessTokenController extends Controller
{
    /**
     * Exchange valid credentials for a Bearer token.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $user = $request->authenticateOnce();

        return response()->json([
            'message' => 'Login berhasil.',
            'data' => [
                'token_type' => 'Bearer',
                'access_token' => $user->issueApiToken(),
            ],
        ]);
    }

    /**
     * Revoke the token used for the request.
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->revokeApiToken();

        return response()->json([
            'message' => 'Logout berhasil. Token sudah tidak berlaku.',
        ]);
    }
}
