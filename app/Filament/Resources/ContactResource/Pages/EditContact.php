<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Clusters\Settings;
use App\Filament\Resources\ContactResource;
use App\Models\Documents;
use Exception;
use Filament\Actions;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\MaxWidth;
use Homeful\Contacts\Data\ContactData;
use Homeful\Contacts\Models\Contact;
use Howdu\FilamentRecordSwitcher\Filament\Concerns\HasRecordSwitcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use PhpParser\Node\Stmt\TryCatch;

class EditContact extends EditRecord
{
    use HasRecordSwitcher;
    protected static string $resource = ContactResource::class;
    protected static ?string $cluster = Settings::class;
    protected function getHeaderActions(): array
    {
        return [
//            Actions\DeleteAction::make(),
            $this->getSaveFormAction()
                ->formId('form'),
            Actions\Action::make('document')
                ->button()
                ->form([
                    Select::make('document')
                        ->label('Select Document')
                        ->native(false)
                        ->options(
                            Documents::all()->mapWithKeys(function ($document) {
                                return [$document->id => $document->name];
                            })->toArray()
                        )
                        ->multiple()
                        ->searchable()
                        ->required(),
                    ToggleButtons::make('action')
                        ->options([
                            'view' => 'View',
                            'download' => 'Download',
                        ])
                        ->icons([
                            'view' => 'heroicon-o-eye',
                            'download' => 'heroicon-o-arrow-down-tray',
                        ])
                        ->inline()
                        ->columns(2)
                        ->default('view')
                        ->required(),
                ])
                ->modalCancelAction(fn (StaticAction $action) => $action->label('Close'))
                ->action(function (array $data, Contact $record, Component $livewire) {

                    foreach ($data['document'] as $d){
                        $livewire->dispatch('open-link-new-tab-event',route('contacts_docx_to_pdf', [$record,$d,$data['action']=='view'?1:0,$record->last_name]));
                    }
                })
                ->modalWidth(MaxWidth::Small),
            Actions\Action::make('Get Technical Description from MFiles')
                ->label('Get Technical Description from MFiles')
                ->action(function (Model $record) {
                    try {
                        $mfilesLink = config('gnc.mfiles_link');
                        $credentials = config('gnc.mfiles_credentials');

                        // Prepare the data to send in the POST request
                        $payload = [
                            "Credentials" => [
                                "Username" => $credentials['username'],  // Fetching from config
                                "Password" => $credentials['password'],  // Fetching from config
                            ],
                            "objectID" => 119,
                            "propertyID" => 1105,
                            "name" => "PVT3_DEV-01-001-001",
                            "property_ids"=>[1105,1050,1109,1203,1204,1202,1285],
                        ];
//                    dd($payload,$this->data['order']['property_name']);
//                        dd($mfilesLink. '/api/mfiles/document/search/properties',$payload);
                        $response = Http::post($mfilesLink . '/api/mfiles/document/search/properties', $payload);

                        if ($response->successful()) {
                            $this->data['order']['technical_description'] = $response->json()['Technical Description'];
                            Notification::make()
                                ->title('MFILES Tech Decription '.$response->status())
                                ->body($response->json()['Technical Description'])
                                ->success()
                                ->persistent()
                                ->sendToDatabase(auth()->user())
                                ->send();
                        }
                    }catch (Exception $e){
                        Notification::make()
                            ->title('MFILES Tech Decription '.$response->status())
                            ->body($response->body())
                            ->danger()
                            ->persistent()
                            ->sendToDatabase(auth()->user())
                            ->send();
                    }
            }),
        ];
    }
    protected function getFormActions(): array
    {
        return [];
    }


    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                // Recursively process arrays
                foreach ($value as $subKey => &$subValue) {
                    if (is_null($subValue)) {
                        $subValue = ''; // Set null values to empty strings in nested arrays
                    }
                }
            } elseif (is_null($value)) {
                $value = ''; // Set null values to empty strings
            }
        }
        $contact_data = ContactData::fromModel(new Contact($data));
        $new_data = [];

        // Extracting data from contact_data for form
        $buyer_address_present = collect($contact_data->addresses)->firstWhere('type', 'primary') ?? $contact_data->addresses[0];
        $buyer_employment = collect($contact_data->employment)->firstWhere('type', 'buyer') ?? [];

        $new_data['buyer_address_present'] = $buyer_address_present;
        $new_data['buyer_employment'] = $buyer_employment;

        // Spouse details if available
        $new_data['spouse'] = $contact_data->spouse->toArray() ?? [];

        // Profile data
        $new_data['profile'] = $contact_data->profile->toArray();
        $new_data['profile']['mobile']=$data['mobile'];

        // Order and seller details
        $new_data['order'] = $contact_data->order->toArray();
//        $new_data['seller'] = $contact_data->order->toArray()['seller'] ?? [];
        $new_data['reference_code'] = $contact_data->reference_code;
        $new_data['co_borrowers'] = $contact_data->co_borrowers->toArray();
        $new_data['uploads']=$contact_data->uploads->toArray();
//        dd($new_data);
        return $new_data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
//        $data['last_edited_by_id'] = auth()->id();
//            collect($data['addresses'])->firstWhere(function ($address) {
//                return in_array($address['type'], ['present', 'Present', 'Primary','primary']);
//            })??[];
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $attribs =[
            'first_name' => $data['profile']['first_name'],
            'middle_name' => $data['profile']['middle_name'],
            'last_name' => $data['profile']['last_name'],
            'name_suffix' => $data['profile']['name_suffix'],
            'civil_status' => $data['profile']['civil_status'],
            'sex' => $data['profile']['sex'],
            'nationality' => $data['profile']['nationality'],
            'date_of_birth' => $data['profile']['date_of_birth'],
            'email' => $data['profile']['email'],
            'mobile' => $data['profile']['mobile'],
            'other_mobile' => $data['profile']['other_mobile'],
            'landline' => $data['profile']['landline'],
            // Create spouse if data is provided
            'spouse' => [
                'first_name' => $data['spouse']['first_name'] ?? null,
                'middle_name' => $data['spouse']['middle_name'] ?? null,
                'last_name' => $data['spouse']['last_name'] ?? null,
                'name_suffix' => $data['spouse']['name_suffix'] ?? null,
                'civil_status' => $data['spouse']['civil_status'] ?? null,
                'sex' => $data['spouse']['sex'] ?? null,
                'nationality' => $data['spouse']['nationality'] ?? null,
                'date_of_birth' => $data['spouse']['date_of_birth'] ?? null,
                'email' => $data['spouse']['email'] ?? null,
                'mobile' => $data['spouse']['mobile'] ?? null,
                'landline' => $data['spouse']['landline'] ?? null,
                'mothers_maiden_name' => $data['spouse']['mothers_maiden_name'] ?? null,
            ],

            // Add addresses
            'addresses' => [
                [
                    'type' => 'present',
                    'full_address' => $data['buyer_address_present']['full_address'] ?? null,
                    'sublocality' => $data['buyer_address_present']['sublocality'] ?? null,
                    'locality' => $data['buyer_address_present']['locality'] ?? null,
                    'administrative_area' => $data['buyer_address_present']['administrative_area'] ?? null,
                    'postal_code' => $data['buyer_address_present']['postal_code'] ?? null,
                    'block' => $data['buyer_address_present']['block'] ?? null,
                    'street' => $data['buyer_address_present']['street'] ?? null,
                    'ownership' => $data['buyer_address_present']['ownership'] ?? null,
                    'country' => $data['buyer_address_present']['country'] ?? '',
                ]
            ],

            // Add employment
            'employment' => [
                [
                    'type'=>'buyer',
                    'employment_status' => $data['buyer_employment']['employment_status'] ?? null,
                    'monthly_gross_income' => $data['buyer_employment']['monthly_gross_income'] ?? null,
                    'current_position' => $data['buyer_employment']['current_position'] ?? null,
                    'employment_type' => $data['buyer_employment']['employment_type'] ?? null,
                    'years_in_service' => $data['buyer_employment']['years_in_service'] ?? null,
                    'employer' => [
                        'name' => $data['buyer_employment']['employer']['name'] ?? null,
                        'industry' => $data['buyer_employment']['employer']['industry'] ?? null,
                        'nationality' => $data['buyer_employment']['employer']['nationality'] ?? null,
                        'contact_no' => $data['buyer_employment']['employer']['contact_no'] ?? null,
                        'year_established' => $data['buyer_employment']['employer']['year_established'] ?? null,
                        'total_number_of_employees' => $data['buyer_employment']['employer']['total_number_of_employees'] ?? null,
                        'email' => $data['buyer_employment']['employer']['email'] ?? null,
                        'fax' => $data['buyer_employment']['employer']['fax'] ?? null,

                        // Expanding the employer address structure
                        'address' => [
                            'full_address' => $data['buyer_employment']['employer']['address']['full_address'] ?? null,
                            'locality' => $data['buyer_employment']['employer']['address']['locality'] ?? null,
                            'administrative_area' => $data['buyer_employment']['employer']['address']['administrative_area'] ?? null,
                            'country' => $data['buyer_employment']['employer']['address']['country'] ?? null,
                            'ownership' =>'company',
                            'type'=>'company',
                        ]
                    ],
                    'id' => [
                        'tin' => $data['buyer_employment']['id']['tin'] ?? null,
                        'pagibig' => $data['buyer_employment']['id']['pagibig'] ?? null,
                        'sss' => $data['buyer_employment']['id']['sss'] ?? null,
                        'gsis' => $data['buyer_employment']['id']['gsis'] ?? null,
                    ],
                    'character_reference'=> $data['buyer_employment']['character_reference'],
                ]
            ],
//            'employment'=>[
//                $data['buyer_employment']
//            ],
            'order'=>$data['order']
        ];
//        dd($attribs['order']);
        $record->update($attribs);
//        dd($data['order'],$attribs['order'],$record->order);

        return $record;
    }




}
