<?php

namespace App\Filament\Resources\MarketSegmentResource\Pages;

use App\Filament\Resources\MarketSegmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMarketSegments extends ManageRecords
{
    protected static string $resource = MarketSegmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
