<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Concerns\HasIndonesianMessages;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegisterUserRequest extends FormRequest
{
    use HasIndonesianMessages;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Collapse repeated whitespace in the name before it is validated and stored.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('name'))) {
            $this->merge(['name' => Str::squish($this->input('name'))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * The name and phone rules match the team registration form, because the captain's
     * name and phone are filled in from the account created here.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:100', 'regex:/^[\pL ]+$/u'],
            'username' => ['bail', 'required', 'string', 'between:6,15', 'alpha_num:ascii', Rule::unique(User::class, 'username')],
            'email' => ['bail', 'required', 'string', 'max:100', 'email:filter', Rule::unique(User::class, 'email')],
            'password' => ['bail', 'required', 'string', 'between:8,16'],
            'phone' => ['bail', 'required', 'string', 'digits_between:7,14'],
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
            'name.regex' => ':attribute hanya boleh berisi huruf dan spasi.',
            'email.unique' => ':attribute sudah terdaftar.',
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
            'name' => 'Nama lengkap',
            'username' => 'Nama pengguna',
            'email' => 'Email',
            'password' => 'Kata sandi',
            'phone' => 'Nomor telepon',
        ];
    }
}
