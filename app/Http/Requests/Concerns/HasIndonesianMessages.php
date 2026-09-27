<?php

namespace App\Http\Requests\Concerns;

/**
 * Indonesian wording for the validation rules shared by the application's forms.
 *
 * The same wording is used by the client-side validator in resources/js/form-validation.js.
 */
trait HasIndonesianMessages
{
    /**
     * Get the messages for the validation rules shared by every form.
     *
     * @return array<string, string>
     */
    protected function commonMessages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute maksimal :max karakter.',
            'between' => ':attribute harus terdiri dari :min-:max karakter.',
            'alpha_num' => ':attribute hanya boleh berisi huruf dan angka.',
            'digits_between' => ':attribute harus berupa angka dengan panjang :min-:max digit.',
            'email' => ':attribute harus berupa alamat email yang valid.',
            'unique' => ':attribute sudah digunakan.',
        ];
    }
}
