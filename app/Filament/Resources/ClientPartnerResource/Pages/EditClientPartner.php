<?php

namespace App\Filament\Resources\ClientPartnerResource\Pages;

use App\Filament\Resources\ClientPartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClientPartner extends EditRecord
{
    protected static string $resource = ClientPartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
