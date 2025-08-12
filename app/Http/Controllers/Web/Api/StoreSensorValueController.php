<?php

namespace App\Http\Controllers\Web\Api;

use App\Enums\AlertType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSensorValueRequest;
use App\Models\Patient;
use App\Models\RecentAlert;
use App\Models\SensorsValue;
use App\Services\BPMService;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class StoreSensorValueController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreSensorValueRequest $request): JsonResponse
    {
        try {
            Log::info('Attempting to store sensor value', [
                'device_identifier' => $request->device_identifier,
                'request_data' => $request->except('device_identifier'),
                'timestamp' => now()->toDateTimeString()
            ]);

            $patient = Patient::where('device_identifier', $request->device_identifier)->first();

            if (!$patient) {
                Log::warning('Patient not found for device identifier', [
                    'device_identifier' => $request->device_identifier,
                    'timestamp' => now()->toDateTimeString(),
                    'request_ip' => $request->ip()
                ]);
                return response()->json([
                    'message' => 'Patient not found.'
                ], Response::HTTP_NOT_FOUND);
            }

            Log::info('Patient found', [
                'patient_id' => $patient->id,
                'patient_name' => $patient->full_name,
                'device_identifier' => $patient->device_identifier,
                'timestamp' => now()->toDateTimeString()
            ]);

            $sensor = SensorsValue::create($request->except('device_identifier') + [
                'patient_id' => $patient->id,
            ]);

            Log::info('Sensor value stored successfully', [
                'patient_id' => $patient->id,
                'sensor_id' => $sensor->id,
                'sensor_data' => $sensor->toArray(),
                'timestamp' => now()->toDateTimeString()
            ]);

            Log::debug('Fetching BPM data for analysis', [
                'patient_id' => $patient->id,
                'time_range' => '10 minutes',
                'timestamp' => now()->toDateTimeString(),
                'query_start' => now()->subMinutes(10)->toDateTimeString()
            ]);

            $bpmData = SensorsValue::where('patient_id', $patient->id)
                ->where('created_at', '>=', now()->subMinutes(10))
                ->get()
                ->pluck('bpm')
                ->limit(10)
                ->toArray();

            Log::info('BPM data retrieved', [
                'patient_id' => $patient->id,
                'bpm_count' => count($bpmData),
                'bpm_values' => $bpmData,
                'timestamp' => now()->toDateTimeString(),
                'min_bpm' => !empty($bpmData) ? min($bpmData) : null,
                'max_bpm' => !empty($bpmData) ? max($bpmData) : null,
                'avg_bpm' => !empty($bpmData) ? array_sum($bpmData) / count($bpmData) : null
            ]);

            $isBPMAlert = BPMService::checkBPM($bpmData);

            Log::info('BPM check completed', [
                'patient_id' => $patient->id,
                'is_alert' => $isBPMAlert,
                'latest_bpm' => end($bpmData),
                'timestamp' => now()->toDateTimeString(),
                'alert_threshold_reached' => $isBPMAlert ? 'yes' : 'no'
            ]);

            if ($isBPMAlert) {
                $alert = RecentAlert::create([
                    'patient_id' => $patient->id,
                    'alert_type' => AlertType::PulseRate(),
                    'bpm' => end($bpmData),
                    'caregiver_id' => $patient->caregiver_id
                ]);

                Log::warning('BPM Alert created', [
                    'alert_id' => $alert->id,
                    'patient_id' => $patient->id,
                    'caregiver_id' => $patient->caregiver_id,
                    'bpm' => end($bpmData),
                    'timestamp' => now()->toDateTimeString()
                ]);
            }

            return response()->json([
                'message' => 'Sensor Value Stored Successfully!',
                'sensor_value' => $sensor
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Failed to store sensor value', [
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'device_identifier' => $request->device_identifier,
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toDateTimeString(),
                'request_ip' => $request->ip(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'message' => 'Failed to store sensor value.'
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
