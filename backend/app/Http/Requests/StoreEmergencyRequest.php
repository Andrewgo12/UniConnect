<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmergencyRequest extends FormRequest
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
            'type' => 'required|string|in:medical,security,help',
            'description' => 'nullable|string|max:1000',
            'location' => 'nullable|array',
            'location.lat' => 'required_with:location|numeric|between:-90,90',
            'location.lng' => 'required_with:location|numeric|between:-180,180',
        ];
    }

    /**
     * Get the custom error messages for validation failures.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required' => 'El tipo de emergencia es obligatorio.',
            'type.in' => 'El tipo debe ser uno de: medical, security, help.',
            'description.max' => 'La descripción no puede exceder los 1000 caracteres.',
            'location.required_with' => 'La ubicación es requerida cuando se proporciona latitud o longitud.',
            'location.lat.numeric' => 'La latitud debe ser un número.',
            'location.lat.between' => 'La latitud debe estar entre -90 y 90 grados.',
            'location.lng.numeric' => 'La longitud debe ser un número.',
            'location.lng.between' => 'La longitud debe estar entre -180 y 180 grados.',
        ];
    }
}
