<?php

namespace App\Filament\Widgets;

use App\Enums\AlertType;
use App\Models\RecentAlert;
use App\Services\SmsService;
use Filament\Actions\Action;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class RecentAlertsTable extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';
   
    public static function canView(): bool
    {
        return auth()->user()->isCaregiver();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                auth()->user()->isCaregiver()
                    ? RecentAlert::whereHas('patient', fn($q) => $q->where('caregiver_id', auth()->id()))
                    : RecentAlert::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('patient.full_name')
                    ->searchable(['first_name', 'middle_name', 'last_name']),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        $bpm = $record->bpm;
                        if ($bpm >= 120)
                            return 'Critical';
                        if ($bpm >= 80)
                            return 'High';
                        if ($bpm < 40)
                            return 'Low';
                        return 'Normal';
                    })
                    ->badge()
                    ->color(function ($state) {
                        if ($state === 'Critical')
                            return 'danger';
                        if ($state === 'High')
                            return 'warning';
                        if ($state === 'Low')
                            return 'info';
                        return 'success';
                    }),
                Tables\Columns\TextColumn::make('bpm')
                    ->label('BPM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alert_type')
                    ->formatStateUsing(fn(AlertType $state) => $state->description),
                Tables\Columns\TextColumn::make('caregiver.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\Action::make('send_alert')
                    ->label('Notify')
                    ->icon('heroicon-o-bell-alert')
                    ->action(function (RecentAlert $record) {
                        Log::warning('BPM Alert triggered', [
                            'patient_id' => $record->patient_id,
                            'bpm_value' => $record->bpm,
                            'caregiver_number' => $record->caregiver->mobile_number
                        ]);

                        Notification::make()
                            ->title('Alert Type: Critical Health Alert 🚨')
                            ->body(
                                'Patient Name: ' . $record->patient->last_name . ', ' . $record->patient->first_name . ' ' . $record->patient->middle_name . "\n" .
                                'Condition: High BPM Alert - ' . $record->bpm . ' BPM' . "\n" .
                                'Time Detected: ' . now()->format('Y-m-d H:i:s') . "\n" .
                                'Location: ' . $record->patient->room . '/' . $record->patient->bed_number . "\n" .
                                'Action Needed: Immediate check-up required.'
                            )
                            ->persistent()
                            ->actions([
                                NotificationAction::make('view')
                                    ->label('View Patient Record')
                                    ->button()
                                    ->url(route('filament.auth.resources.patients.edit', $record->patient_id)),
                            ])
                            ->sendToDatabase($record->caregiver);

                        Notification::make()
                            ->title('Alert Sent Successfully')
                            ->body('The alert has been sent to the caregiver.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Send Alert SMS')
                    ->modalDescription('Are you sure you want to send an alert SMS to the caregiver?')
                    ->color('warning')
                    ->visible(auth()->user()->isAdmin()),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
