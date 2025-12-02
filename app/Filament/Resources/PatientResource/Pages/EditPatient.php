<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Enums\MaritalStatus;
use App\Filament\Resources\PatientResource;
use App\Filament\Widgets\BpmMonitoring;
use App\Filament\Widgets\MapWidget;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditPatient extends EditRecord
{
    protected static string $resource = PatientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


    public function getHeaderWidgets(): array
    {
        BpmMonitoring::$can_view = true;
        MapWidget::$can_view = true;
        return [
            BpmMonitoring::make([
                'patient_id' => $this->record->id,
            ]),
            MapWidget::make([
                'patient_id' => $this->record->id,
            ]),
        ];
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Initial Information')
                    ->schema([
                        TextInput::make('device_identifier')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Select::make('caregiver_id')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->relationship('caregiver', 'name'),

                        TextInput::make('room')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('bed_number')
                            ->required()
                            ->maxLength(255),
                    ]),
                Fieldset::make('Personal Information')
                    ->schema([
                        TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('middle_name')
                            ->nullable()
                            ->maxLength(255),
                        TextInput::make('maiden_name')
                            ->nullable()
                            ->maxLength(255),
                    ])->columns(2),

                Fieldset::make('Contact Information')
                    ->schema([
                        TextInput::make('address')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('city')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('province')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('zip')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Fieldset::make('Birth Information')
                    ->schema([
                          DatePicker::make('birth_date')
    ->required()
    ->maxDate(now()),
                        TextInput::make('birth_place')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Fieldset::make('Physical Information')
                    ->schema([
                        TextInput::make('height')
                            ->nullable()
                            ->maxLength(255)
                            ->suffix('cm'),
                        TextInput::make('weight')
                            ->nullable()
                            ->maxLength(255)
                            ->suffix('kg'),
                    ])->columns(2),

                Fieldset::make('Marital Information')
                    ->schema([
                        Select::make('marital_status')
                            ->required()
                            ->options(MaritalStatus::asSelectArray()),
                        TextInput::make('spouse_last_name')
                            ->nullable()
                            ->maxLength(255),
                        TextInput::make('spouse_first_name')
                            ->nullable()
                            ->maxLength(255),
                        TextInput::make('spouse_middle_name')
                            ->nullable()
                            ->maxLength(255),
                        TextInput::make('spouse_maiden_name')
                            ->nullable()
                            ->maxLength(255),
                    ])->columns(2),

                Fieldset::make('Emergency Contact')
                    ->schema([
                        TextInput::make('emergency_contact_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('emergency_contact_relationship')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('emergency_contact_phone')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                    ])->columns(3),

                Fieldset::make('Guardian Information')
                    ->schema([
                        TextInput::make('guardian_name')
                            ->nullable()
                            ->maxLength(255),
                        TextInput::make('guardian_phone')
                            ->tel()
                            ->nullable()
                            ->maxLength(255),
                    ])->columns(2),

            ]);
    }
}
