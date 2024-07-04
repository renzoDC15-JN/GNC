<?php

namespace App\Filament\Resources\ClientInformationsResource\Pages;

use App\Filament\Resources\ClientInformationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClientInformations extends EditRecord
{
    protected static string $resource = ClientInformationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
