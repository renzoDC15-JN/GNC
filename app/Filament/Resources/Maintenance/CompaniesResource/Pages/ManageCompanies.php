<?php

namespace App\Filament\Resources\Maintenance\CompaniesResource\Pages;

use App\Filament\Resources\Maintenance\CompaniesResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCompanies extends ManageRecords
{
    protected static string $resource = CompaniesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
