<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Clusters\Settings;
use App\Filament\Resources\ContactResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Homeful\Contacts\Data\ContactData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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
        $contact_data = ContactData::fromModel($this->record);
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
           'co_borrowers' => collect($data['co_borrowers'])->map(function($item){
                return [
                    'first_name' => $item['first_name'] ?? '',
                    'middle_name' => $item['middle_name'] ?? '',
                    'last_name' => $item['last_name'] ?? '',
                    'name_suffix' => $item['name_suffix'] ?? '',
                    'civil_status' => $item['civil_status'] ?? '',
                    'sex' => $item['sex'] ?? '',
                    'address' => $item['address'] ?? '',
                    'nationality' => $item['nationality'] ?? '',
                    'date_of_birth' => $item['date_of_birth'] ?? '',
                    'email' => $item['email'] ?? '',
                    'mobile' => $item['mobile'] ?? '',
                    'other_mobile' => $item['other_mobile'] ?? '',
                    'help_number' => $item['help_number'] ?? '',
                    'landline' => $item['landline'] ?? '',
                    'mothers_maiden_name' => $item['mothers_maiden_name'] ?? '',
                    'age' => $item['age'] ?? '',
                    'relationship_to_buyer' => $item['relationship_to_buyer'] ?? '',
                    'passport' => $item['passport'] ?? '',
                    'date_issued' => $item['date_issued'] ?? '',
                    'place_issued' => $item['place_issued'] ?? '',
                    'spouse' => $item['spouse'] ?? '',
                    'spouse_tin' => $item['spouse_tin'] ?? '',
                ];
           }),
            'order'=> [
                'sku' => $data['order']['sku'] ?? '',
                'seller_commission_code' => $data['order']['seller_commission_code'] ?? '',
                'property_code' => $data['order']['property_code'] ?? '',
                'property_type' => $data['order']['property_type'] ?? '',
                'company_name' => $data['order']['company_name'] ?? '',
                'project_name' => $data['order']['project_name'] ?? '',
                'project_code' => $data['order']['project_code'] ?? '',
                'property_name' => $data['order']['property_name'] ?? '',
                'block' => $data['order']['block'] ?? '',
                'lot' => $data['order']['lot'] ?? '',
                'lot_area' => $data['order']['lot_area'] ?? '',
                'floor_area' => $data['order']['floor_area'] ?? '',
                'loan_term' => $data['order']['loan_term'] ?? '',
                'loan_interest_rate' => $data['order']['loan_interest_rate'] ?? '',
                'tct_no' => $data['order']['tct_no'] ?? '',
                'project_location' => $data['order']['project_location'] ?? '',
                'project_address' => $data['order']['project_address'] ?? '',
                'reservation_rate' => $data['order']['reservation_rate'] ?? '',
                'unit_type' => $data['order']['unit_type'] ?? '',
                'unit_type_interior' => $data['order']['unit_type_interior'] ?? '',
                'reservation_date' => $data['order']['reservation_date'] ?? '',
                'transaction_reference' => $data['order']['transaction_reference'] ?? '',
                'transaction_status' => $data['order']['transaction_status'] ?? '',
                'total_payments_made' => $data['order']['total_payments_made'] ?? '',
                'staging_status' => $data['order']['staging_status'] ?? '',
                'period_id' => $data['order']['period_id'] ?? '',
                'buyer_age' => $data['order']['buyer_age'] ?? '',
                'seller' => [
                    'name' => $data['order']['seller']['name'] ?? '',
                    'id' => $data['order']['seller']['id'] ?? '',
                    'superior' => $data['order']['seller']['superior'] ?? '',
                    'team_head' => $data['order']['seller']['team_head'] ?? '',
                    'chief_seller_officer' => $data['order']['seller']['chief_seller_officer'] ?? '',
                    'deputy_chief_seller_officer' => $data['order']['seller']['deputy_chief_seller_officer'] ?? '',
                    'unit' => $data['order']['seller']['unit'] ?? '',
                ],
                'seller' => [
                    'scheme' => $data['order']['payment_scheme']['scheme'] ?? '',
                    'method' => $data['order']['payment_scheme']['method'] ?? '',
                    'collectible_price' => $data['order']['payment_scheme']['collectible_price'] ?? '',
                    'commissionable_amount' => $data['order']['payment_scheme']['commissionable_amount'] ?? '',
                    'evat_percentage' => $data['order']['payment_scheme']['evat_percentage'] ?? '',
                    'evat_amount' => $data['order']['payment_scheme']['evat_amount'] ?? '',
                    'fees' => collect($data['order']['payment_scheme']['fees'])->map(function($item){
                        return [
                            'name' => $item['name'] ?? '',
                        'amount' => $item['amount'] ?? 0,
                        ];
                    }),
                ],
                'witness1' => $data['order']['witness1'] ?? '',
                'witness2' => $data['order']['witness2'] ?? '',
            ]
         ];
        $record->update($attribs);
    //    dd($data,$attribs,$record);

        return $record;
    }




}
