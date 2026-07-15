<?php

namespace App\Http\Requests\Api\V1\Media;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240', // 10MB max
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:profile,sign_language,emergency,medical,general',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'alt_text' => 'nullable|string|max:500',
            'is_public' => 'boolean',
            'language' => 'nullable|string|max:10',
            'metadata' => 'nullable|array',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'image.required' => 'La imagen es obligatoria.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe ser JPEG, PNG, GIF, WebP o SVG.',
            'image.max' => 'La imagen no puede exceder 10MB.',
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no puede exceder 255 caracteres.',
            'description.max' => 'La descripción no puede exceder 1000 caracteres.',
            'type.required' => 'El tipo de imagen es obligatorio.',
            'type.in' => 'El tipo de imagen no es válido.',
            'tags.*.max' => 'Las etiquetas no pueden exceder 50 caracteres.',
            'alt_text.max' => 'El texto alternativo no puede exceder 500 caracteres.',
            'language.max' => 'El código de idioma no es válido.',
        ];
    }
}
