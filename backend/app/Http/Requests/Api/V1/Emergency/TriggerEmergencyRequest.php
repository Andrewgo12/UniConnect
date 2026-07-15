<?php

namespace App\Http\Requests\Api\V1\Emergency;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TriggerEmergencyRequest extends FormRequest
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
            'type' => 'required|in:medical,accident,violence,natural_disaster,technical,other',
            'severity' => 'required|in:low,medium,high,critical',
            'description' => 'required|string|max:2000',
            'location' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'contact_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_relationship' => 'nullable|string|max:100',
            'medical_conditions' => 'nullable|array',
            'medical_conditions.*' => 'string|max:255',
            'accessibility_needs' => 'nullable|array',
            'accessibility_needs.*' => 'in:blind,deaf,mute,mobility_impaired,cognitive_disability',
            'media_files' => 'nullable|array',
            'media_files.*' => 'file|max:10240', // 10MB max per file
            'additional_info' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'El tipo de emergencia es obligatorio.',
            'type.in' => 'El tipo de emergencia no es válido.',
            'severity.required' => 'La severidad es obligatoria.',
            'severity.in' => 'La severidad seleccionada no es válida.',
            'description.required' => 'La descripción es obligatoria.',
            'description.max' => 'La descripción no puede exceder 2000 caracteres.',
            'location.required' => 'La ubicación es obligatoria.',
            'location.max' => 'La ubicación no puede exceder 500 caracteres.',
            'latitude.numeric' => 'La latitud debe ser un número.',
            'latitude.between' => 'La latitud debe estar entre -90 y 90.',
            'longitude.numeric' => 'La longitud debe ser un número.',
            'longitude.between' => 'La longitud debe estar entre -180 y 180.',
            'contact_name.max' => 'El nombre del contacto no puede exceder 255 caracteres.',
            'contact_phone.max' => 'El teléfono del contacto no puede exceder 20 caracteres.',
            'contact_relationship.max' => 'La relación con el contacto no puede exceder 100 caracteres.',
            'medical_conditions.*.max' => 'Una condición médica no puede exceder 255 caracteres.',
            'accessibility_needs.*.in' => 'Una necesidad de accesibilidad no es válida.',
            'media_files.*.max' => 'Cada archivo no puede exceder 10MB.',
            'additional_info.max' => 'La información adicional no puede exceder 1000 caracteres.',
        ];
    }
}
