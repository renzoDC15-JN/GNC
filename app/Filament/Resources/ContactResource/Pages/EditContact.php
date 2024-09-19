<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Clusters\Settings;
use App\Filament\Resources\ContactResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContact extends EditRecord
{
    protected static string $resource = ContactResource::class;
    protected static ?string $cluster = Settings::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $buyer_address_present = collect($data['addresses'])->firstWhere(function ($address) {
            return in_array($address['type'], ['present', 'Present', 'Primary','primary']);
        })??[];
        $buyer_address_permanent= collect($data['addresses'])->firstWhere(function ($address) {
            return in_array($address['type'], ['permanent', 'Permanent', 'Secondary','secondary']);
        })??[];

        $buyer_employment= collect($data['employment'])->firstWhere(function ($address) {
            return in_array($address['type'], ['buyer', 'Buyer']);
        })??[];


        $data= array_merge($data,['buyer_address_present'=>$buyer_address_present]);
        $data= array_merge($data,['buyer_address_permanent'=>$buyer_address_permanent]);
        $data= array_merge($data,['buyer_employment'=>$buyer_employment]);


        return $data;
    }
}
