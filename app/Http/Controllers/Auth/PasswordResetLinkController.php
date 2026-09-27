<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendPasswordResetLinkRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Show the form to request a password reset link.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Email a password reset link to the account with the given email address.
     */
    public function store(SendPasswordResetLinkRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::ResetLinkSent) {
            return back()->with('status', 'Tautan untuk mengatur ulang kata sandi telah dikirim ke email Anda.');
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => match ($status) {
                Password::ResetThrottled => 'Harap tunggu sebentar sebelum meminta tautan baru.',
                default => 'Kami tidak menemukan akun dengan email tersebut.',
            },
        ]);
    }
}
