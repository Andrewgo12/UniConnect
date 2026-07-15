<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|max:255',
            'remember' => 'boolean',
            'device_token' => 'nullable|string|max:500',
            'device_type' => 'nullable|in:mobile,desktop,tablet,voice_assistant',
            'accessibility_mode' => 'nullable|in:standard,screen_reader,voice_control,sign_language,high_contrast,large_text',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'device_type.in' => 'El tipo de dispositivo no es válido.',
            'accessibility_mode.in' => 'El modo de accesibilidad no es válido.',
        ];
    }
}
