<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'role' => 'sometimes|in:user,admin,medical_staff,moderator',
            'accessibility_needs' => 'nullable|array',
            'accessibility_needs.*' => 'in:blind,deaf,mute,mobility_impaired,cognitive_disability',
            'preferences' => 'nullable|array',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
            'is_active' => 'sometimes|boolean',
            'language' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:50',
            'current_password' => 'required_with:password|string',
            'password' => 'nullable|string|min:8|max:255|confirmed',
            'password_confirmation' => 'nullable|string|min:8|max:255|required_with:password',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.sometimes' => 'El nombre debe ser proporcionado cuando se actualiza.',
            'email.sometimes' => 'El correo electrónico debe ser proporcionado cuando se actualiza.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'date_of_birth.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'gender.in' => 'El género seleccionado no es válido.',
            'role.in' => 'El rol seleccionado no es válido.',
            'accessibility_needs.*.in' => 'Una de las necesidades de accesibilidad no es válida.',
            'profile_photo.image' => 'La foto de perfil debe ser una imagen.',
            'profile_photo.mimes' => 'La foto de perfil debe ser JPEG, PNG o GIF.',
            'profile_photo.max' => 'La foto de perfil no puede exceder 2MB.',
            'language.max' => 'El código de idioma no es válido.',
            'timezone.max' => 'La zona horaria no es válida.',
            'current_password.required_with' => 'La contraseña actual es obligatoria para cambiar la contraseña.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password_confirmation.required_with' => 'La confirmación de contraseña es obligatoria.',
        ];
    }
}
