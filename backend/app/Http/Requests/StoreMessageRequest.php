<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
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
            'content' => 'required|string|max:1000',
            'type' => 'string|in:text,phrase,emergency',
            'conversation_id' => 'nullable|exists:conversations,id',
            'vibration_pattern' => 'nullable|array',
            'vibration_pattern.*' => 'integer',
            'metadata' => 'nullable|array',
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
            'content.required' => 'El contenido del mensaje es obligatorio.',
            'content.max' => 'El contenido no puede exceder los 1000 caracteres.',
            'type.in' => 'El tipo de mensaje debe ser uno de: text, phrase, emergency.',
            'conversation_id.exists' => 'La conversación seleccionada no existe.',
            'vibration_pattern.array' => 'El patrón de vibración debe ser un array.',
            'vibration_pattern.*.integer' => 'Los valores del patrón de vibración deben ser números enteros.',
        ];
    }
}
