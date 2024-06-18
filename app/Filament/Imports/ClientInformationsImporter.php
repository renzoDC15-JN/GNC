<?php

namespace App\Filament\Imports;

use App\Models\ClientInformations;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ClientInformationsImporter extends Importer
{
    protected static ?string $model = ClientInformations::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('project')
                ->requiredMapping()
                ->ignoreBlankState()
                ->rules(['nullable','max:255']),
            ImportColumn::make('location')
                ->requiredMapping()
                ->ignoreBlankState()
                ->rules(['nullable','max:255']),
            ImportColumn::make('property_name')
                ->requiredMapping()
                ->ignoreBlankState()
                ->rules(['nullable','max:255']),
            ImportColumn::make('phase')
                ->requiredMapping()
                ->ignoreBlankState()
                ->rules(['nullable','max:255']),
            ImportColumn::make('block')
                ->requiredMapping()
                ->ignoreBlankState()
                ->rules(['nullable','max:255']),
            ImportColumn::make('lot')
                ->requiredMapping()
                ->ignoreBlankState()
                ->rules(['nullable','max:255']),
            ImportColumn::make('buyer_name')
                ->requiredMapping()
                ->ignoreBlankState()
                ->rules(['nullable','max:255']),
            ImportColumn::make('buyer_civil_status')
                ->requiredMapping()
                ->ignoreBlankState()
                ->rules(['nullable','max:255']),
            ImportColumn::make('buyer_nationality')
                ->requiredMapping()
                ->ignoreBlankState()
                ->rules(['nullable','max:255']),
            ImportColumn::make('buyer_address')
                ->requiredMapping()
                ->ignoreBlankState()
                ->rules(['nullable','max:255']),
            ImportColumn::make('buyer_tin')
                ->requiredMapping()
                ->ignoreBlankState()
                ->guess(['Buyer Tax Identifaction Number'])
                ->rules(['nullable','max:255']),
            ImportColumn::make('buyer_spouse_name')
                ->requiredMapping()
                ->ignoreBlankState()
                ->rules(['nullable','max:255']),
            ImportColumn::make('mrif_fee')
                ->requiredMapping()
                ->ignoreBlankState()
                ->rules(['nullable','max:255']),
            ImportColumn::make('reservation_rate')
                ->requiredMapping()
                ->ignoreBlankState()
                ->guess(['Reservation Rate (Processing Fee)'])
                ->rules(['nullable','max:255']),
            // ImportColumn::make('created_by')
            //     ->requiredMapping()
            //     ->rules(['max:255']),
        ];
    }

    public function resolveRecord(): ?ClientInformations
    {
        return ClientInformations::firstOrNew([
            // Update existing records, matching them by `$this->data['column_name']`
            'property_name' => $this->data['property_name'],
        ],$this->data);

        // return new ClientInformations();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your client informations import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
