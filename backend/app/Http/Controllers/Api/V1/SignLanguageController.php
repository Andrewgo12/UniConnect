<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SignLanguage;
use App\Services\SignLanguageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SignLanguageController extends Controller
{
    protected SignLanguageService $signLanguageService;

    public function __construct(SignLanguageService $signLanguageService)
    {
        $this->signLanguageService = $signLanguageService;
    }

    /**
     * Display a listing of sign languages.
     */
    public function index(Request $request)
    {
        $signLanguages = $this->signLanguageService->listForUser($request->user()->id);
        return response()->json($signLanguages);
    }

    /**
     * Store a newly created sign language.
     */
    public function store(Request $request)
    {
        try {
            $signLanguage = $this->signLanguageService->create($request->all(), $request->user()->id);
            return response()->json($signLanguage, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Display the specified sign language.
     */
    public function show(SignLanguage $signLanguage)
    {
        $this->authorize('view', $signLanguage);
        return response()->json($this->signLanguageService->find($signLanguage));
    }

    /**
     * Update the specified sign language.
     */
    public function update(Request $request, SignLanguage $signLanguage)
    {
        $this->authorize('update', $signLanguage);

        try {
            $updated = $this->signLanguageService->update($signLanguage, $request->all());
            return response()->json($updated);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Remove the specified sign language.
     */
    public function destroy(SignLanguage $signLanguage)
    {
        $this->authorize('delete', $signLanguage);
        $this->signLanguageService->delete($signLanguage);
        return response()->json(null, 204);
    }

    /**
     * Get sign language categories
     */
    public function categories()
    {
        return response()->json($this->signLanguageService->getCategories());
    }

    /**
     * Get basic signs for frontend
     */
    public function basicSigns(Request $request)
    {
        $category = $request->category ?? 'colombian';
        return response()->json($this->signLanguageService->basicSigns($category));
    }

    /**
     * Get emergency signs
     */
    public function emergencySigns(Request $request)
    {
        return response()->json($this->signLanguageService->emergencySigns());
    }
}
