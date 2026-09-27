<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Http\Requests\Concerns\HasIndonesianMessages;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreTeamRequest extends FormRequest
{
    use HasIndonesianMessages;

    /**
     * Determine if the user is authorized to make this request.
     *
     * A user may register only one team, as its captain.
     */
    public function authorize(): bool
    {
        return ! $this->user()->hasRegisteredTeam();
    }

    /**
     * Refuse a second registration with a conflict instead of a generic "forbidden" response.
     *
     * @throws HttpResponseException
     */
    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Tim Anda sudah terdaftar pada turnamen ini.',
        ], 409));
    }

    /**
     * Fill the captain's name and phone from the authenticated user, ignoring submitted values.
     */
    protected function prepareForValidation(): void
    {
        $memberName = $this->input('member.name');

        $this->merge([
            'captain' => [
                'name' => $this->user()->name,
                'phone' => $this->user()->phone,
                'gender' => $this->input('captain.gender'),
            ],
            'member' => [
                'name' => is_string($memberName) ? Str::squish($memberName) : $memberName,
                'phone' => $this->input('member.phone'),
                'gender' => $this->input('member.gender'),
            ],
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $playerRules = [
            'name' => ['bail', 'required', 'string', 'max:100', 'regex:/^[\pL ]+$/u', Rule::unique(TeamMember::class, 'name')],
            'phone' => ['bail', 'required', 'string', 'digits_between:7,14'],
            'gender' => ['required', Rule::enum(Gender::class)],
        ];

        return [
            'team_name' => ['bail', 'required', 'string', 'between:4,15', 'regex:/^[A-Za-z0-9_]+$/', Rule::unique(Team::class, 'team_name')],
            'captain.name' => $playerRules['name'],
            'captain.phone' => $playerRules['phone'],
            'captain.gender' => $playerRules['gender'],
            'member.name' => $playerRules['name'],
            'member.phone' => $playerRules['phone'],
            'member.gender' => $playerRules['gender'],
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['captain.name', 'member.name'])) {
                    return;
                }

                if (Str::lower($this->input('captain.name')) === Str::lower($this->input('member.name'))) {
                    $validator->errors()->add('member.name', 'Nama anggota tidak boleh sama dengan nama kapten.');
                }
            },
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
            'team_name.regex' => ':attribute hanya boleh berisi huruf, angka, dan garis bawah (_).',
            'team_name.unique' => ':attribute sudah digunakan oleh tim lain.',
            '*.name.regex' => ':attribute hanya boleh berisi huruf dan spasi.',
            '*.name.unique' => ':attribute sudah terdaftar di tim lain.',
            '*.gender.required' => ':attribute wajib dipilih.',
            '*.gender.enum' => ':attribute yang dipilih tidak valid.',
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
            'team_name' => 'Nama tim',
            'captain.name' => 'Nama kapten',
            'captain.phone' => 'Nomor telepon kapten',
            'captain.gender' => 'Jenis kelamin kapten',
            'member.name' => 'Nama anggota',
            'member.phone' => 'Nomor telepon anggota',
            'member.gender' => 'Jenis kelamin anggota',
        ];
    }
}
