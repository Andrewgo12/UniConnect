<?php

namespace App\Http\Requests\Api\V1\Message;

use Illuminate\Contracts\Validation\ValidationRule;
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
            'conversation_id' => 'required|integer|exists:conversations,id',
            'content' => 'required_without:file|string|max:5000',
            'type' => 'required|in:text,voice,video,image,file,sign_language,emergency',
            'status' => 'nullable|in:sent,delivered,read,failed',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'parent_id' => 'nullable|integer|exists:messages,id',
            'metadata' => 'nullable|array',
            'accessibility_data' => 'nullable|array',
            'file' => 'nullable|file|max:10240', // 10MB max
            'voice_duration' => 'nullable|integer|min:1|max:3600', // max 1 hour
            'language' => 'nullable|string|max:10',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'conversation_id.required' => 'La conversación es obligatoria.',
            'conversation_id.exists' => 'La conversación seleccionada no existe.',
            'content.required_without' => 'El contenido es obligatorio cuando no se envía archivo.',
            'content.max' => 'El contenido no puede exceder 5000 caracteres.',
            'type.required' => 'El tipo de mensaje es obligatorio.',
            'type.in' => 'El tipo de mensaje no es válido.',
            'status.in' => 'El estado del mensaje no es válido.',
            'priority.in' => 'La prioridad del mensaje no es válida.',
            'parent_id.exists' => 'El mensaje padre no existe.',
            'file.max' => 'El archivo no puede exceder 10MB.',
            'voice_duration.min' => 'La duración del audio debe ser al menos 1 segundo.',
            'voice_duration.max' => 'La duración del audio no puede exceder 1 hora.',
            'language.max' => 'El código de idioma no es válido.',
        ];
    }
}
