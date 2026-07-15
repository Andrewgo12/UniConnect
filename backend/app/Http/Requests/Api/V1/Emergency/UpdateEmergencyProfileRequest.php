<?php

namespace App\Http\Requests\Api\V1\Emergency;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmergencyProfileRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array>|string>
     */
    public function rules(): array
    {
        return [
            'emergency_contacts' => 'required|array|min:1',
            'emergency_contacts.*.name' => 'required|string|max:255',
            'emergency_contacts.*.phone' => 'required|string|max:20',
            'emergency_contacts.*.relationship' => 'required|string|max:100',
            'medical_conditions' => 'nullable|array',
            'medical_conditions.*' => 'string|max:255',
            'allergies' => 'nullable|array',
            'allergies.*' => 'string|max:255',
            'medications' => 'nullable|array',
            'medications.*' => 'string|max:255',
            'blood_type' => 'nullable|string|max:10',
            'medical_notes' => 'nullable|string|max:1000',
            'preferred_hospital' => 'nullable|string|max:255',
            'insurance_info' => 'nullable|array',
            'accessibility_preferences' => 'nullable|array',
            'accessibility_preferences.voice_commands' => 'boolean',
            'accessibility_preferences.high_contrast' => 'boolean',
            'accessibility_preferences.large_text' => 'boolean',
            'accessibility_preferences.screen_reader' => 'boolean',
            'accessibility_preferences.vibration_alerts' => 'boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'emergency_contacts.required' => 'Debe proporcionar al menos un contacto de emergencia.',
            'emergency_contacts.min' => 'Debe proporcionar al menos un contacto de emergencia.',
            'emergency_contacts.*.name.required' => 'El nombre del contacto es obligatorio.',
            'emergency_contacts.*.name.max' => 'El nombre del contacto no puede exceder 255 caracteres.',
            'emergency_contacts.*.phone.required' => 'El teléfono del contacto es obligatorio.',
            'emergency_contacts.*.phone.max' => 'El teléfono del contacto no puede exceder 20 caracteres.',
            'emergency_contacts.*.relationship.required' => 'La relación con el contacto es obligatoria.',
            'emergency_contacts.*.relationship.max' => 'La relación con el contacto no puede exceder 100 caracteres.',
            'medical_conditions.*.max' => 'Una condición médica no puede exceder 255 caracteres.',
            'allergies.*.max' => 'Una alergia no puede exceder 255 caracteres.',
            'medications.*.max' => 'Un medicamento no puede exceder 255 caracteres.',
            'blood_type.max' => 'El tipo de sangre no puede exceder 10 caracteres.',
            'medical_notes.max' => 'Las notas médicas no pueden exceder 1000 caracteres.',
            'preferred_hospital.max' => 'El hospital preferido no puede exceder 255 caracteres.',
        ];
    }
}
