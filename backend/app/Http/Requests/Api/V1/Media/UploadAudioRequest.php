<?php

namespace App\Http\Requests\Api\V1\Media;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadAudioRequest extends FormRequest
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
            'audio' => 'required|file|mimes:mp3,wav,ogg,m4a|max:10240', // 10MB max
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:speech,voice_note,emergency,sign_language',
            'quality' => 'required|in:low,medium,high',
            'language' => 'nullable|string|max:10',
            'duration' => 'nullable|integer|min:1|max:3600', // max 1 hour
            'transcript' => 'nullable|string|max:5000',
            'is_public' => 'boolean',
            'metadata' => 'nullable|array',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'audio.required' => 'El archivo de audio es obligatorio.',
            'audio.mimes' => 'El audio debe ser MP3, WAV, OGG o M4A.',
            'audio.max' => 'El audio no puede exceder 10MB.',
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no puede exceder 255 caracteres.',
            'description.max' => 'La descripción no puede exceder 1000 caracteres.',
            'type.required' => 'El tipo de audio es obligatorio.',
            'type.in' => 'El tipo de audio no es válido.',
            'quality.required' => 'La calidad es obligatoria.',
            'quality.in' => 'La calidad seleccionada no es válida.',
            'duration.min' => 'La duración debe ser al menos 1 segundo.',
            'duration.max' => 'La duración no puede exceder 1 hora.',
            'language.max' => 'El código de idioma no es válido.',
            'transcript.max' => 'La transcripción no puede exceder 5000 caracteres.',
        ];
    }
}
