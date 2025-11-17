<?php

namespace App\Filament\Resources\PatientResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiseasesRelationManager extends RelationManager
{
    protected static string $relationship = 'diseases';
 
    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return 'Health Status';
    }

    public static function getModelLabel(): string
    {
        return 'Health Status';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Health Status';
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('disease_id')
                    ->label('Health Status')
                    ->options(\App\Models\Disease::query()->pluck('name', 'id'))
                    ->preload()
                    ->searchable()
                    ->required(),
            ]);
    }
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->createAnother(false)
                ->label('Add Health')
                ->action(function($data) {
                    $this->getOwnerRecord()->diseases()->attach($data['disease_id']);
                    \Filament\Notifications\Notification::make()
                        ->title('Disease added successfully')
                        ->success()
                        ->send();
                }),
            ])
            ->actions([
                Tables\Actions\Action::make('detach')
                    ->label('Remove')
                    ->requiresConfirmation()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->action(fn($record) => $this->getOwnerRecord()->diseases()->detach($record->id)),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
