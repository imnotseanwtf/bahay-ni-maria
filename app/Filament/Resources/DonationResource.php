<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationResource\Pages;
use App\Filament\Resources\DonationResource\RelationManagers;
use App\Models\Donation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;

    protected static ?string $navigationLabel = 'Donations';
    protected static ?string $modelLabel = 'Donation';
    protected static ?string $pluralModelLabel = 'Donations';
    protected static ?string $navigationGroup = 'Finance Management';

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Donation Details')
                    ->schema([
                        Forms\Components\DatePicker::make('date_recieved')
                            ->required(),
                        Forms\Components\TextInput::make('donor_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('item_description')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric(),
                        Forms\Components\DatePicker::make('expiration_date')
                            ->required(),
                        Forms\Components\TextInput::make('remarks')
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date_recieved')
                    ->sortable()
                    ->date(),
                Tables\Columns\TextColumn::make('donor_name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('item_description')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->sortable()
                    ->numeric(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->sortable()
                    ->date(),
                Tables\Columns\TextColumn::make('remarks')
                    ->searchable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListDonations::route('/'),
            'create' => Pages\CreateDonation::route('/create'),
            'edit' => Pages\EditDonation::route('/{record}/edit'),
        ];
    }
}
