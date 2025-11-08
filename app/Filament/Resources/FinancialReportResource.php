<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinancialReportResource\Pages;
use App\Filament\Resources\FinancialReportResource\RelationManagers;
use App\Models\FinancialReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FinancialReportResource extends Resource
{
    protected static ?string $model = FinancialReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
     protected static ?string $navigationLabel = 'Cash';
    protected static ?string $modelLabel = 'Cash';
    protected static ?string $pluralModelLabel = 'Cash';

    protected static ?string $navigationGroup = 'Donations';

    protected static ?int $navigationSort = 1;
    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Financial Report Details')
                    ->schema([
                        Forms\Components\DatePicker::make('date_recieved')
                            ->required()
                            ->label('Date Received'),
                        Forms\Components\TextInput::make('donor_name')
                            ->required()
                            ->label('Donor Name'),
                        Forms\Components\TextInput::make('payment_method')
                            ->required()
                            ->label('Payment Method'),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('₱')
                            ->label('Amount'),
                        Forms\Components\Textarea::make('remarks')
                            ->label('Remarks')
                            ->columnSpanFull()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date_recieved')
                    ->label('Date Received')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('donor_name')
                    ->label('Donor Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('remarks')
                    ->label('Remarks')
                    ->limit(50)
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('date_recieved'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinancialReports::route('/'),
            'create' => Pages\CreateFinancialReport::route('/create'),
            'edit' => Pages\EditFinancialReport::route('/{record}/edit'),
        ];
    }
}
