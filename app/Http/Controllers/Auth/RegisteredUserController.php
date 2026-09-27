<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Show the account registration form.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Create the account, then ask the user to log in before registering a team.
     */
    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());

        return redirect()->route('login')
            ->with('status', 'Akun berhasil dibuat. Silakan masuk untuk mendaftarkan tim Anda.')
            ->withInput(['username' => $user->username]);
    }
}
