<?php

namespace App\Filament\Resources\DiseaseResource\Pages;

use App\Filament\Resources\DiseaseResource;
use App\Models\Disease;
use App\Models\Patient;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SortPatients extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = DiseaseResource::class;

    protected static string $view = 'filament.resources.disease-resource.pages.sort-patients';

    public Disease $record;

    public function mount(Disease $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        return "Patients with {$this->record->name}";
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Patient::query()
                    ->whereHas('diseases', function (Builder $query) {
                        $query->where('disease_id', $this->record->id);
                    })
                    ->orderBy('last_name')
                    ->orderBy('first_name')
            )
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Patient Name')
                    ->searchable(['first_name', 'middle_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('room')
                    ->label('Room')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bed_number')
                    ->label('Bed')
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Birth Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Address')
                    ->searchable()
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\Action::make('view')
                //     ->label('View Patient')
                //     ->icon('heroicon-o-eye')
                //     ->url(fn (Patient $record): string => route('filament.admin.resources.patients.edit', $record)),
            ])
            ->bulkActions([
                //
            ])
            ->emptyStateHeading('No patients found')
            ->emptyStateDescription("No patients are currently diagnosed with {$this->record->name}.")
            ->defaultSort('last_name');
    }
}
