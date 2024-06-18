<?php

namespace App\Filament\Resources\ClientInformationsResource\Pages;

use App\Filament\Resources\ClientInformationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientInformations extends ListRecords
{
    protected static string $resource = ClientInformationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
