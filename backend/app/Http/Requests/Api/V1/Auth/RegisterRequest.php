<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:255|confirmed',
            'password_confirmation' => 'required|string|min:8|max:255',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'accessibility_needs' => 'nullable|array',
            'accessibility_needs.*' => 'in:blind,deaf,mute,mobility_impaired,cognitive_disability',
            'device_type' => 'nullable|in:mobile,desktop,tablet,voice_assistant',
            'preferred_language' => 'nullable|string|max:10',
            'terms_accepted' => 'required|boolean|accepted',
            'privacy_accepted' => 'required|boolean|accepted',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password_confirmation.required' => 'La confirmación de contraseña es obligatoria.',
            'date_of_birth.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'gender.in' => 'El género seleccionado no es válido.',
            'accessibility_needs.*.in' => 'Una de las necesidades de accesibilidad no es válida.',
            'device_type.in' => 'El tipo de dispositivo no es válido.',
            'terms_accepted.required' => 'Debes aceptar los términos y condiciones.',
            'terms_accepted.accepted' => 'Debes aceptar los términos y condiciones.',
            'privacy_accepted.required' => 'Debes aceptar la política de privacidad.',
            'privacy_accepted.accepted' => 'Debes aceptar la política de privacidad.',
        ];
    }
}
