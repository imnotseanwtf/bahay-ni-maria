<?php

namespace App\Filament\Resources\RecentAlertResource\Pages;

use App\Filament\Resources\RecentAlertResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecentAlert extends EditRecord
{
    protected static string $resource = RecentAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
