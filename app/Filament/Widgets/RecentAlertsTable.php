<?php

namespace App\Filament\Widgets;

use App\Enums\AlertType;
use App\Models\RecentAlert;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentAlertsTable extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                RecentAlert::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('patient.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alert_type')
                    ->formatStateUsing(fn(AlertType $state) => $state->description),
                Tables\Columns\TextColumn::make('bpm')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
