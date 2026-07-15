<?php

namespace App\Http\Requests\Api\V1\Media;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadSignRequest extends FormRequest
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
            'video' => 'required|file|mimes:mp4,webm,mov,avi|max:51200', // 50MB max
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:basic,medical,emergency,education,custom',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'region' => 'required|in:colombian,international,local',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'is_public' => 'boolean',
            'language' => 'nullable|string|max:10',
            'transcript' => 'nullable|string|max:5000',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'video.required' => 'El video es obligatorio.',
            'video.mimes' => 'El video debe ser MP4, WebM, MOV o AVI.',
            'video.max' => 'El video no puede exceder 50MB.',
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no puede exceder 255 caracteres.',
            'description.max' => 'La descripción no puede exceder 1000 caracteres.',
            'category.required' => 'La categoría es obligatoria.',
            'category.in' => 'La categoría seleccionada no es válida.',
            'difficulty_level.required' => 'El nivel de dificultad es obligatorio.',
            'difficulty_level.in' => 'El nivel de dificultad no es válido.',
            'region.required' => 'La región es obligatoria.',
            'region.in' => 'La región seleccionada no es válida.',
            'tags.*.max' => 'Las etiquetas no pueden exceder 50 caracteres.',
            'language.max' => 'El código de idioma no es válido.',
            'transcript.max' => 'La transcripción no puede exceder 5000 caracteres.',
        ];
    }
}
