<?php

namespace App\Observers;

use App\Models\RecentAlert;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;

class RecentAlertObserver
{
    /**
     * Handle the RecentAlert "created" event.
     */
    public function created(RecentAlert $recentAlert): void
    {
        // Reload with relationships to ensure they're available
        $recentAlert->load(['patient', 'caregiver']);

        Log::warning('BPM Alert triggered', [
            'patient_id' => $recentAlert->patient_id,
            'bpm_value' => $recentAlert->bpm,
            'caregiver_number' => $recentAlert->caregiver?->mobile_number
        ]);

        // Store alert in cache queue for batch processing
        $alertData = [
            'recent_alert_id' => $recentAlert->id,
            'patient_id' => $recentAlert->patient_id,
            'caregiver_id' => $recentAlert->caregiver_id,
            'bpm' => $recentAlert->bpm,
            'patient_name' => $recentAlert->patient->last_name . ', ' . $recentAlert->patient->first_name . ' ' . $recentAlert->patient->middle_name,
            'room' => $recentAlert->patient->room,
            'bed_number' => $recentAlert->patient->bed_number,
            'created_at' => now()->toIso8601String(),
        ];

        // Add to cache queue
        $queue = Cache::get('recent_alerts_queue', []);
        $queue[] = $alertData;
        Cache::put('recent_alerts_queue', $queue, now()->addHours(1));
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
