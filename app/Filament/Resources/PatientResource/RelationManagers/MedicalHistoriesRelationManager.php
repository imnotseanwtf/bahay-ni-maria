<?php

namespace App\Filament\Resources\PatientResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MedicalHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'medicalHistories';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Medical History Details')
                    ->schema([
                        Forms\Components\TextInput::make('medical_problems')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan('full'),
                        Forms\Components\Textarea::make('list_all_allergies')
                            ->required()
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpan(1),
                        Forms\Components\Textarea::make('list_all_medications')
                            ->required() 
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('schedule_date')
                            ->required()
                            ->columnSpan('full'),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('medical_problems'),
                Tables\Columns\TextColumn::make('schedule_date')
                    ->date(),   
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
