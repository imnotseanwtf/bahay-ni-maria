<?php

namespace App\Console\Commands;

use App\Models\RecentAlert;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;

class ProcessBatchedAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:process-batch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process batched recent alerts (5 at a time)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $queue = Cache::get('recent_alerts_queue', []);
        
        if (empty($queue)) {
            $this->info('No alerts in queue.');
            return Command::SUCCESS;
        }

        // Process up to 5 alerts
        $batch = array_slice($queue, 0, 5);
        $remaining = array_slice($queue, 5);

        $this->info('Processing ' . count($batch) . ' alerts...');

        foreach ($batch as $alertData) {
            try {
                // Get the caregiver
                $caregiver = User::find($alertData['caregiver_id']);
                
                if (!$caregiver) {
                    $this->warn("Caregiver not found for alert ID: {$alertData['recent_alert_id']}");
                    continue;
                }

                // Send notification
                Notification::make()
                    ->title('Alert Type: Critical Health Alert 🚨')
                    ->body(
                        'Patient Name: ' . $alertData['patient_name'] . "\n" .
                        'Condition: High BPM Alert - ' . $alertData['bpm'] . ' BPM' . "\n" .
                        'Time Detected: ' . date('Y-m-d H:i:s', strtotime($alertData['created_at'])) . "\n" .
                        'Location: ' . $alertData['room'] . '/' . $alertData['bed_number'] . "\n" .
                        'Action Needed: Immediate check-up required.'
                    )
                    ->persistent()
                    ->actions([
                        NotificationAction::make('view')
                            ->label('View Patient Record')
                            ->button()
                            ->url(route('filament.auth.resources.patients.edit', $alertData['patient_id'])),
                    ])
                    ->sendToDatabase($caregiver);

                $this->info("Sent notification for alert ID: {$alertData['recent_alert_id']}");
            } catch (\Exception $e) {
                $this->error("Error processing alert ID {$alertData['recent_alert_id']}: " . $e->getMessage());
            }
        }

        // Update cache with remaining alerts
        if (empty($remaining)) {
            Cache::forget('recent_alerts_queue');
        } else {
            Cache::put('recent_alerts_queue', $remaining, now()->addHours(1));
        }

        $this->info('Batch processing complete. ' . count($remaining) . ' alerts remaining in queue.');
        return Command::SUCCESS;
    }
}

