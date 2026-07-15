<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:255|confirmed',
            'password_confirmation' => 'required|string|min:8|max:255',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'role' => 'nullable|in:user,admin,medical_staff,moderator',
            'accessibility_needs' => 'nullable|array',
            'accessibility_needs.*' => 'in:blind,deaf,mute,mobility_impaired,cognitive_disability',
            'preferences' => 'nullable|array',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
            'is_active' => 'boolean',
            'language' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:50',
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
            'role.in' => 'El rol seleccionado no es válido.',
            'accessibility_needs.*.in' => 'Una de las necesidades de accesibilidad no es válida.',
            'profile_photo.image' => 'La foto de perfil debe ser una imagen.',
            'profile_photo.mimes' => 'La foto de perfil debe ser JPEG, PNG o GIF.',
            'profile_photo.max' => 'La foto de perfil no puede exceder 2MB.',
            'language.max' => 'El código de idioma no es válido.',
            'timezone.max' => 'La zona horaria no es válida.',
        ];
    }
}
