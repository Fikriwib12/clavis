<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Concerns\HasIndonesianMessages;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    use HasIndonesianMessages;

    /**
     * The number of failed attempts allowed per minute before the login is locked.
     */
    private const int MAX_ATTEMPTS = 5;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['bail', 'required', 'string', 'between:6,15', 'alpha_num:ascii', Rule::exists(User::class, 'username')],
            'password' => ['bail', 'required', 'string', 'between:8,16'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            ...$this->commonMessages(),
            'username.exists' => ':attribute tidak terdaftar.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'username' => 'Username',
            'password' => 'Kata sandi',
        ];
    }

    /**
     * Authenticate the credentials and start a session for the user.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->attemptUsing(fn (array $credentials): bool => Auth::attempt($credentials, $this->boolean('remember')));
    }

    /**
     * Authenticate the credentials for the current request only, without starting a session.
     *
     * @throws ValidationException
     */
    public function authenticateOnce(): User
    {
        $this->attemptUsing(fn (array $credentials): bool => Auth::once($credentials));

        /** @var User */
        return Auth::user();
    }

    /**
     * Refuse the attempt before validation when the login is locked.
     *
     * @throws ValidationException
     */
    protected function prepareForValidation(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), self::MAX_ATTEMPTS)) {
            return;
        }

        throw ValidationException::withMessages([
            'username' => 'Terlalu banyak percobaan masuk. Silakan coba lagi dalam '.RateLimiter::availableIn($this->throttleKey()).' detik.',
        ]);
    }

    /**
     * Count invalid input, including an unknown username, as a failed attempt.
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        RateLimiter::hit($this->throttleKey());

        parent::failedValidation($validator);
    }

    /**
     * Run the given authentication attempt and track its outcome in the rate limiter.
     *
     * @param  Closure(array{username: string, password: string}): bool  $attempt
     *
     * @throws ValidationException
     */
    private function attemptUsing(Closure $attempt): void
    {
        if (! $attempt($this->only('username', 'password'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'password' => 'Kata sandi yang Anda masukkan salah.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Get the rate limiting key for the username and IP address of the request.
     */
    private function throttleKey(): string
    {
        $username = $this->input('username');

        return Str::transliterate(Str::lower(is_string($username) ? $username : '').'|'.$this->ip());
    }
}
