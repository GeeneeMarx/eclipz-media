<?php

namespace App\Filament\Resources\MediaLibraryItemResource\Pages;

use App\Filament\Resources\MediaLibraryItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMediaLibraryItem extends EditRecord
{
    protected static string $resource = MediaLibraryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}