<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Phrase;
use App\Services\PhraseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PhraseController extends Controller
{
    protected PhraseService $phraseService;

    public function __construct(PhraseService $phraseService)
    {
        $this->phraseService = $phraseService;
    }
    /**
     * Display a listing of phrases.
     */
    public function index(Request $request)
    {
        $phrases = $this->phraseService->list($request->only('category'));
        return response()->json($phrases);
    }

    /**
     * Store a newly created phrase.
     */
    public function store(Request $request)
    {
        try {
            $phrase = $this->phraseService->create($request->all());
            return response()->json($phrase, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Display the specified phrase.
     */
    public function show(Phrase $phrase)
    {
        return response()->json($phrase);
    }

    /**
     * Update the specified phrase.
     */
    public function update(Request $request, Phrase $phrase)
    {
        try {
            $updated = $this->phraseService->update($phrase, $request->all());
            return response()->json($updated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Remove the specified phrase.
     */
    public function destroy(Phrase $phrase)
    {
        $this->phraseService->delete($phrase);
        return response()->json(null, 204);
    }

    /**
     * Get default phrases for the frontend
     */
    public function defaults()
    {
        return response()->json($this->phraseService->defaults());
    }
}
