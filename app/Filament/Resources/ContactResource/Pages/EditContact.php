<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Clusters\Settings;
use App\Filament\Resources\ContactResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditContact extends EditRecord
{
    protected static string $resource = ContactResource::class;
    protected static ?string $cluster = Settings::class;
    protected function getHeaderActions(): array
    {
        return [
//            Actions\DeleteAction::make(),
            $this->getSaveFormAction()
                ->formId('form'),
        ];
    }
    protected function getFormActions(): array
    {
        return [];
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

//    dd($data);
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
//        $data['last_edited_by_id'] = auth()->id();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        dd($data);
        $record->update($data);

        return $record;
    }




}
