<?php

namespace App\Filament\Resources\ClientInformationsResource\Pages;

use App\Filament\Imports\ClientInformationsImporter;
use App\Filament\Resources\ClientInformationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\ImportAction;

class ListClientInformations extends ListRecords
{
    protected static string $resource = ClientInformationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportAction::make()
                ->importer(ClientInformationsImporter::class)
        ];
    }
}
