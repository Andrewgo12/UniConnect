<?php

namespace App\Http\Requests\Api\V1\Message;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMessageRequest extends FormRequest
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
            'content' => 'nullable|string|max:5000',
            'status' => 'nullable|in:sent,delivered,read,failed',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'metadata' => 'nullable|array',
            'accessibility_data' => 'nullable|array',
            'is_edited' => 'nullable|boolean',
            'is_pinned' => 'nullable|boolean',
            'language' => 'nullable|string|max:10',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'content.max' => 'El contenido no puede exceder 5000 caracteres.',
            'status.in' => 'El estado del mensaje no es válido.',
            'priority.in' => 'La prioridad del mensaje no es válida.',
            'language.max' => 'El código de idioma no es válido.',
        ];
    }
}
