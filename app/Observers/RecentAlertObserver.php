<?php

namespace App\Observers;

use App\Models\RecentAlert;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;

class RecentAlertObserver
{
    /**
     * Handle the RecentAlert "created" event.
     */
    public function created(RecentAlert $recentAlert): void
    {
        Log::warning('BPM Alert triggered', [
            'patient_id' => $recentAlert->patient_id,
            'bpm_value' => $recentAlert->bpm,
            'caregiver_number' => $recentAlert->caregiver->mobile_number
        ]);

        $smsService = new SmsService();
        $smsService->sendSms(
            $recentAlert->caregiver->mobile_number,
            '[Alert Type]: Critical Health Alert 🚨\n' .
                'Patient Name: ' . $recentAlert->patient->last_name . ', ' . $recentAlert->patient->first_name . ' ' . $recentAlert->patient->middle_name . '\n' .
                'Condition: High BPM Alert - ' . $recentAlert->bpm . ' BPM\n' .
                'Time Detected: ' . now()->format('Y-m-d H:i:s') . '\n' .
                'Location: ' . $recentAlert->patient->room . '/' . $recentAlert->patient->bed_number . '\n' .
                'Action Needed: Immediate check-up required.'
        );
    }

    /**
     * Handle the RecentAlert "updated" event.
     */
    public function updated(RecentAlert $recentAlert): void
    {
        //
    }

    /**
     * Handle the RecentAlert "deleted" event.
     */
    public function deleted(RecentAlert $recentAlert): void
    {
        //
    }

    /**
     * Handle the RecentAlert "restored" event.
     */
    public function restored(RecentAlert $recentAlert): void
    {
        //
    }

    /**
     * Handle the RecentAlert "force deleted" event.
     */
    public function forceDeleted(RecentAlert $recentAlert): void
    {
        //
    }
}
