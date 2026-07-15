<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'profile' => 'sometimes|array',
            'profile.blind' => 'boolean',
            'profile.deaf' => 'boolean',
            'profile.mute' => 'boolean',
            'profile.preferences' => 'nullable|array',
        ];
    }

    /**
     * Get custom error messages for validation failures.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'profile.blind.boolean' => 'El valor para ceguera debe ser verdadero o falso.',
            'profile.deaf.boolean' => 'El valor para sordera debe ser verdadero o falso.',
            'profile.mute.boolean' => 'El valor para mudez debe ser verdadero o falso.',
            'profile.preferences.array' => 'Las preferencias deben estar en formato de array.',
        ];
    }
}
