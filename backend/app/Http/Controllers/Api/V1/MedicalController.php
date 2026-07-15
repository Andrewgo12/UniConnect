<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Services\MedicalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MedicalController extends Controller
{
    protected MedicalService $medicalService;

    public function __construct(MedicalService $medicalService)
    {
        $this->medicalService = $medicalService;
    }

    /**
     * Display a listing of medical records.
     */
    public function index(Request $request)
    {
        $records = $this->medicalService->listRecords($request->user()->id);
        return response()->json($records);
    }

    /**
     * Store a newly created medical record.
     */
    public function store(Request $request)
    {
        try {
            $record = $this->medicalService->createRecord($request->all(), $request->user());
            return response()->json($record, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Display the specified medical record.
     */
    public function show(MedicalRecord $medicalRecord)
    {
        $this->authorize('view', $medicalRecord);
        return response()->json($this->medicalService->findRecord($medicalRecord));
    }

    /**
     * Update the specified medical record.
     */
    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $this->authorize('update', $medicalRecord);

        try {
            $updated = $this->medicalService->updateRecord($medicalRecord, $request->all());
            return response()->json($updated);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Remove the specified medical record.
     */
    public function destroy(MedicalRecord $medicalRecord)
    {
        $this->authorize('delete', $medicalRecord);
        $this->medicalService->deleteRecord($medicalRecord);
        return response()->json(null, 204);
    }

    /**
     * Add medication to medical record
     */
    public function addMedication(Request $request, MedicalRecord $medicalRecord)
    {
        $this->authorize('update', $medicalRecord);

        try {
            $medication = $this->medicalService->addMedication($medicalRecord, $request->user(), $request->all());
            return response()->json($medication, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Add appointment to medical record
     */
    public function addAppointment(Request $request, MedicalRecord $medicalRecord)
    {
        $this->authorize('update', $medicalRecord);

        try {
            $appointment = $this->medicalService->addAppointment($medicalRecord, $request->user(), $request->all());
            return response()->json($appointment, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Get medications for a medical record
     */
    public function medications(MedicalRecord $medicalRecord)
    {
        $this->authorize('view', $medicalRecord);
        return response()->json($this->medicalService->getMedications($medicalRecord));
    }

    /**
     * Get appointments for a medical record
     */
    public function appointments(MedicalRecord $medicalRecord)
    {
        $this->authorize('view', $medicalRecord);
        return response()->json($this->medicalService->getAppointments($medicalRecord));
    }
}
