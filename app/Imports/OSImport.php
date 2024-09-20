<?php

namespace App\Imports;

use App\Models\User;
use Homeful\Contacts\Actions\PersistContactAction;
use Homeful\Contacts\Data\PaymentSchemeData;
use Homeful\Contacts\Models\Contact;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithGroupedHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Propaganistas\LaravelPhone\PhoneNumber;

HeadingRowFormatter::default('cornerstone-os-report-1');
class OSImport implements ToModel, WithHeadingRow, WithGroupedHeadingRow, WithChunkReading, ShouldQueue
{
    use Importable;



    public function __construct()
    {
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (!isset($row['project_code'])) {
            return null;
        }
//        if ($row['brn']=='7002257600038050'){
//            dd($row);
//        }
        $attribs = [
            'reference_code' => (string) ($row['brn'] ?? ''),
            'spouse' => [
                'first_name' => $row['buyer_spouse_first_name'] ?? '',
                'middle_name' => $row['buyer_spouse_middle_name'] ?? '',
                'last_name' => $row['buyer_spouse_last_name'] ?? '',
                'name_suffix' => $row['buyer_spouse_name_suffix'] ?? '',
                'mothers_maiden_name' => $row['buyer_spouse_mothers_maiden_name'] ?? '',
                'civil_status' => $row['buyer_spouse_civil_status'] ?? '',
                'sex' => $row['buyer_spouse_gender'] ?? '',
                'nationality' => $row['buyer_spouse_nationality'] ?? '',
                'date_of_birth' => $this->convertExcelDate($row['buyer_spouse_date_of_birth'] ?? ''),
                'email' => $row['buyer_spouse_email'] ?? '',
                'mobile' => $row['buyer_spouse_mobile'] ?? '',
                'other_mobile' => '',
                'help_number' => '',
                'client_id' => $row['client_id_spouse'] ?? '',
                'landline' => $row['buyer_spouse_landline'] ?? '',
                'age' => $row['spouse_age'] ?? '',
            ],
            'first_name' => Str::title($row['buyer_first_name'] ?? ''),
            'middle_name' => Str::title($row['buyer_middle_name'] ?? 'Missing'),
            'last_name' => Str::title($row['buyer_last_name'] ?? ''),
            'name_suffix' => Str::title($row['buyer_name_suffix'] ?? ''),
            'civil_status' => Str::title($row['buyer_civil_status'] ?? ''),
            'sex' => Str::title($row['buyer_gender'] ?? ''),
            'nationality' => Str::title($row['buyer_nationality'] ?? ''),
            'date_of_birth' => $this->convertExcelDate($row['date_of_birth'] ?? ''),
            'email' => strtolower($row['buyer_principal_email'] ?? ''),
            'mobile' => (string) ($row['buyer_primary_contact_number'] ?? ''),
            'help_number' => (string) ($row['buyer_help_number'] ?? ''),
            'landline' => (string) ($row['buyer_help_number'] ?? ''),
            'other_mobile' => (string) ($row['buyer_other_contact_number'] ?? ''),
            'mothers_maiden_name' => $row['buyer_mothers_maiden_name'] ?? '',
            'addresses' => [
                [
                    'type' => 'primary',
                    'ownership' => Str::title($row['buyer_ownership_type'] ?? ''),
                    'full_address' => $row['buyer_address'] ?? '',
                    'address1' => Str::title($row['buyer_place_of_residency_1_(city_of_residency)'] ?? ''),
                    'address2' => Str::title($row['buyer_place_of_residency_2_(province_of_residency)'] ?? ''),
                    'sublocality' => Str::title($row['buyer_barangay'] ?? ''),
                    'locality' => Str::title($row['buyer_city'] ?? ''),
                    'administrative_area' => Str::title($row['buyer_province'] ?? ''),
                    'postal_code' => Str::title($row['buyer_zip_code'] ?? ''),
                    'sorting_code' => '',
                    'country' => 'PH',
                    'block' => Str::title($row['buyer_block'] ?? ''),
                    'lot' => Str::title($row['buyer_lot'] ?? ''),
                    'unit' => Str::title($row['buyer_unit'] ?? ''),
                    'floor' => Str::title($row['buyer_floor'] ?? ''),
                    'street' => Str::title($row['buyer_street'] ?? ''),
                    'building' => Str::title($row['buyer_building'] ?? ''),
                    'length_of_stay' => $row['buyer_length_of_stay'] ?? '',
                ],
                // Repeat for other address types (co_borrower, spouse)...
            ],
            'employment' => [
                [
                    'type' => 'buyer',
                    'employment_status' => Str::title($row['buyer_employer_status'] ?? ''),
                    'monthly_gross_income' => $row['buyer_salary_gross_income'] ?? 0,
                    'current_position' => Str::title($row['buyer_position'] ?? ''),
                    'employment_type' => Str::title($row['buyer_employer_type'] ?? ''),
                    'years_in_service' => Str::title($row['buyer_years_in_service'] ?? ''),
                    'salary_range' => $row['buyer_salary_range'] ?? '',
                    'department_name' => $row['department_name'] ?? '',
                    'character_reference' => [
                        'name' => $row['buyer_character_reference_name'] ?? '',
                        'mobile' => $row['buyer_character_reference_mobile'] ?? '',
                    ],

                    'employer' => [
                        'name' => Str::title($row['buyer_employer_name'] ?? ''),
                        'industry' => Str::title($row['industry'] ?? ''),
                        'type' => Str::title($row['buyer_employer_type'] ?? ''),
                        'status' => Str::title($row['buyer_employer_status'] ?? ''),
                        'year_established' => Str::title($row['buyer_employer_year_established'] ?? ''),
                        'total_number_of_employees' => $row['buyer_employer_total_number_of_employees'] ?? 0,
                        'email' => $row['buyer_employer_email'] ?? '',
                        'nationality' => 'PH',
                        'contact_no' => (string) ($row['buyer_employer_contact_number'] ?? ''),
                        'fax' => $row['aif_fax'] ?? '',
                        'address' => [
                            'type' => 'work',
                            'ownership' => 'N/A',
                            'full_address' => Str::title($row['buyer_employer_address'] ?? ''),
                            'address1' => Str::title($row['buyer_place_of_work_1_(city_of_employer)'] ?? ''),
                            'address2' => Str::title($row['buyer_place_of_work_2_(province_of_employer)'] ?? ''),
                            'sublocality' => Str::title($row['buyer_employer_barangay'] ?? ''),
                            'locality' => Str::title($row['buyer_employer_city'] ?? ''),
                            'administrative_area' => Str::title($row['buyer_employer_province'] ?? ''),
                            'postal_code' => Str::title($row['buyer_employer_zip_code'] ?? ''),
                            'sorting_code' => '',
                            'country' => 'PH',
                            'block' => Str::title($row['buyer_employer_block'] ?? ''),
                            'lot' => Str::title($row['buyer_employer_lot'] ?? ''),
                            'unit' => Str::title($row['buyer_employer_unit'] ?? ''),
                            'floor' => Str::title($row['buyer_employer_floor'] ?? ''),
                            'street' => Str::title($row['buyer_employer_street'] ?? ''),
                            'building' => Str::title($row['buyer_employer_building'] ?? ''),
                            'length_of_stay' => $row['buyer_employer_length_of_stay'] ?? '',
                        ],
                    ],
                    'id' => [
                        'tin' => (string) ($row['buyer_tax_identification_number'] ?? ''),
                        'sss' => (string) ($row['buyer_sss_gsis_number'] ?? ''),
                        'pagibig' => (string) ($row['buyer_pag_ibig_number'] ?? ''),
                        'gsis' => (string) ($row['buyer_sss_gsis_number'] ?? ''),
                    ],
                ],
                // Repeat for spouse and co-borrower...
            ],
            'order' => [
                'sku' => Str::title($row['sku'] ?? ''),
                'seller_commission_code' => Str::title($row['seller_commission_code'] ?? ''),
                'property_code' => Str::title($row['property_code'] ?? ''),
                'property_type' => Str::title($row['property_type'] ?? ''),
                'company_name' => Str::title($row['company_name'] ?? ''),
                'project_name' => Str::title($row['project_name'] ?? ''),
                'project_code' => Str::title($row['project_code'] ?? ''),
                'property_name' => Str::title($row['property_name'] ?? $row['property_code'] ?? ''),
                'phase' => (string) ($row['phase'] ?? ''),
                'block' => (string) ($row['block'] ?? ''),
                'lot' => (string) ($row['lot'] ?? ''),
                'lot_area' => $row['lot_area'] ?? 0,
                'floor_area' => $row['floor_area'] ?? 0,
                'tcp' => $row['tcp'] ?? 0,
                'loan_term' => (string) ($row['bp2_terms'] ?? $row['bp_1_terms'] ?? ''),
                'loan_interest_rate' => $row['bp2_interest_rate'] ?? $row['bp1_interest_rate'] ?? 0,
                'tct_no' => $row['tct_no'] ?? '',
                'interest' => $row['interest'] ?? '',
                'project_location' => Str::title($row['project_location'] ?? ''),
                'project_address' => Str::title($row['project_address'] ?? ''),
                'mrif_fee' => $row['mrif_fee'] ?? 0,
                'reservation_rate' => $row['reservation_rate_processing_fee'] ?? 0,
                'class_field' => $row['class_field'] ?? '',
                'segment_field' => $row['segment_field'] ?? '',
                'rebooked_id_form' => $row['rebooked_id_form'] ?? '',
                'buyer_action_form_number' => $row['buyer_action_form_number'] ?? '',
                'buyer_action_form_date' => $this->convertExcelDate($row['buyer_action_form_date'] ?? ''),
                'cancellation_type' => $row['cancellation_type'] ?? '',
                'cancellation_reason' => $row['cancellation_reason'] ?? '',
                'cancellation_reason2' => $row['cancellation_reason2'] ?? '',
                'cancellation_remarks' => $row['cancellation_remarks'] ?? '',
                'unit_type' => $row['unit_type'] ?? '',
                'unit_type_interior' => $row['unit_type_interior'] ?? '',
                'house_color' => $row['house_color'] ?? '',
                'construction_status' => $row['construction_status'] ?? '',
                'transaction_reference' => $row['transaction_reference'] ?? '',
                'reservation_date' => $this->convertExcelDate($row['reservation_date'] ?? ''),
                'circular_number' => $row['circular_number'] ?? '',
                'term_1' => $row['term_1'] ?? '',
                'term_2' => $row['term_2'] ?? '',
                'term_3' => $row['term_3'] ?? '',
                'amort_mrisri1' => $row['amort_mrisri1'] ?? '',
                'amort_mrisri2' => $row['amort_mrisri2'] ?? '',
                'amort_mrisri3' => $row['amort_mrisri3'] ?? '',
                'amort_nonlife1' => $row['amort_nonlife1'] ?? '',
                'amort_nonlife2' => $row['amort_nonlife2'] ?? '',
                'amort_nonlife3' => $row['amort_nonlife3'] ?? '',
                'amort_princ_int1' => $row['amort_princ_int1'] ?? '',
                'amort_princ_int2' => $row['amort_princ_int2'] ?? '',
                'amort_princ_int3' => $row['amort_princ_int3'] ?? '',
                'monthly_amort1' => $row['monthly_amort1'] ?? 0,
                'monthly_amort2' => $row['monthly_amort2'] ?? 0,
                'monthly_amort3' => $row['monthly_amort3'] ?? 0,
                'equity_1_amount' => $row['equity_1_amount'] ?? 0,
                'equity_1_percentage_rate' => $row['equity_1_percentage_rate'] ?? 0,
                'equity_1_interest_rate' => $row['equity_1_interest_rate'] ?? 0,
                'equity_1_terms' => $row['equity_1_terms'] ?? 0,
                'equity_1_monthly_payment' => $row['equity_1_monthly_payment'] ?? 0,
                'equity_1_effective_date' => $this->convertExcelDate($row['equity_1_effective_date'] ?? ''),
                'equity_2_amount' => $row['equity_2_amount'] ?? 0,
                'equity_2_percentage_rate' => $row['equity_2_percentage_rate'] ?? 0,
                'equity_2_interest_rate' => $row['equity_2_interest_rate'] ?? 0,
                'equity_2_terms' => $row['equity_2_terms'] ?? 0,
                'cash_outlay_1_terms' => $row['cash_outlay_1_terms'] ?? 0,
                'cash_outlay_1_monthly_payment' => $row['cash_outlay_1_monthly_payment'] ?? 0,
                'cash_outlay_1_effective_date' => $this->convertExcelDate($row['cash_outlay_1_effective_date'] ?? ''),
                'cash_outlay_2_amount' => $row['cash_outlay_2_amount'] ?? 0,
                'cash_outlay_2_percentage_rate' => $row['cash_outlay_2_percentage_rate'] ?? 0,
                'cash_outlay_2_interest_rate' => $row['cash_outlay_2_interest_rate'] ?? 0,
                'cash_outlay_2_terms' => $row['cash_outlay_2_terms'] ?? 0,
                'cash_outlay_2_monthly_payment' => $row['cash_outlay_2_monthly_payment'] ?? 0,
                'cash_outlay_2_effective_date' => $this->convertExcelDate($row['cash_outlay_2_effective_date'] ?? ''),
                'cash_outlay_3_amount' => $row['cash_outlay_3_amount'] ?? 0,
                'cash_outlay_3_percentage_rate' => $row['cash_outlay_3_percentage_rate'] ?? 0,
                'cash_outlay_3_interest_rate' => $row['cash_outlay_3_interest_rate'] ?? 0,
                'cash_outlay_3_terms' => $row['cash_outlay_3_terms'] ?? 0,
                'cash_outlay_3_monthly_payment' => $row['cash_outlay_3_monthly_payment'] ?? 0,
                'cash_outlay_3_effective_date' => $this->convertExcelDate($row['cash_outlay_3_effective_date'] ?? ''),
                'page' => $row['page'] ?? '',
                'building' => $row['building'] ?? '',
                'floor' => $row['floor'] ?? '',
                'unit' => $row['unit'] ?? '',
                'cct' => $row['cct'] ?? '',
                'witness1' => $row['witness1'] ?? '',
                'witness2' => $row['witness2'] ?? '',
                'buyer_extension_name' => $row['buyer_extension_name'] ?? '',
                'company_acronym' => $row['company_acronym'] ?? '',
                'repricing_period_in_words' => $row['repricing_period_in_words'] ?? '',
                'repricing_period' => (string) ($row['repricing_period'] ?? ''),
                'company_address' => $row['company_address'] ?? '',
                'exec_position' => $row['exec_position'] ?? '',
                'board_resolution_date' => $this->convertExcelDate($row['board_resolution_date'] ?? ''),
                'registry_of_deeds_address' => $row['registry_of_deeds_address'] ?? '',
                'exec_tin' => $row['exec_tin'] ?? '',
                'loan_period_in_words' => $row['loan_period_in_words'] ?? '',
                'spouse_address' => $row['spouse_address'] ?? '',
                'total_miscellaneous_fee_in_words' => $row['total_miscellaneous_fee_in_words'] ?? '',
                'tmf' =>(string) $row['tmf'] ?? 0,
                'cash_outlay_1_amount' => $row['cash_outlay_1_amount'] ?? 0,
                'cash_outlay_1_percentage_rate' => $row['cash_outlay_1_percentage_rate'] ?? '',
                'cash_outlay_1_interest_rate' => $row['cash_outlay_1_interest_rate'] ?? '',
                'equity_2_monthly_payment' => $row['equity_2_monthly_payment'] ?? 0,
                'equity_2_effective_date' => $this->convertExcelDate($row['equity_2_effective_date'] ?? ''),
                'bp_1_amount' => $row['bp_1_amount'] ?? 0,
                'bp_1_percentage_rate' => $row['bp_1_percentage_rate'] ?? '',
                'bp_1_interest_rate' => $row['bp_1_interest_rate'] ?? 0,
                'bp_1_terms' => $row['bp_1_terms'] ?? '',
                'bp_1_monthly_payment' => $row['bp_1_monthly_payment'] ?? 0,
                'bp_1_effective_date' => $this->convertExcelDate($row['bp_1_effective_date'] ?? ''),
                'bp_2_amount' => $row['bp_2_amount'] ?? 0,
                'bp_2_percentage_rate' => $row['bp_2_percentage_rate'] ?? '',
                'bp_2_interest_rate' => $row['bp_2_interest_rate'] ?? '',
                'bp_2_terms' => $row['bp_2_terms'] ?? '',
                'bp_2_monthly_payment' => $row['bp_2_monthly_payment'] ?? 0,
                'bp_2_effective_date' => $this->convertExcelDate($row['bp_2_effective_date'] ?? ''),
                'circular_no_312_379' => $row['circular_no._(312/379)'] ?? '',
                'tcp_in_words' => $row['tcp_in_words'] ?? '',
                'interest_in_words' => $row['interest_in_words'] ?? '',
                'logo' => $row['logo'] ?? '',
                'loan_period_months' => $row['loan_period_months'] ?? '',
                'exec_signatories' => $row['exec_signatories'] ?? '',
                'exec_tin_no' => $row['exec_tin_no'] ?? '',
                'loan_terms_in_word' => $row['loan_terms_in_word'] ?? '',
                'loan_value_after_downpayment' => $row['loan_value'] ?? '',
                'date_created' => $this->convertExcelDate($row['date_created'] ?? ''),
                'ra_date' => $this->convertExcelDate($row['ra_date'] ?? ''),
                'date_approved' => $this->convertExcelDate($row['date_approved'] ?? ''),
                'date_expiration' => $this->convertExcelDate($row['date_expiration'] ?? ''),
                'os_month' => $row['os_month'] ?? '',
                'due_date' => $this->convertExcelDate($row['due_date'] ?? ''),
                'total_payments_made' => $row['total_payments_made'] ?? '',
                'transaction_status' => $row['transaction_status'] ?? '',
                'staging_status' => $row['staging_status'] ?? '',
                'period_id' => $row['period_id'] ?? '',
                'date_closed' => $this->convertExcelDate($row['date_closed'] ?? ''),
                'closed_reason' => $row['closed_reason'] ?? '',
                'date_cancellation' => $this->convertExcelDate($row['date_cancellation'] ?? ''),
                'baf_number' => $row['baf_number'] ?? '',
                'baf_date' => $this->convertExcelDate($row['baf_date'] ?? ''),
                'client_id_buyer' => $row['client_id_buyer'] ?? '',
                'buyer_age' => $row['buyer_age'] ?? '',
                'hucf_move_in_fee' => $row['hucf_move_in_fee'] ?? 0,
                'ltvr_slug' => $row['ltvr_slug'] ?? '',
                'repricing_period_slug' => $row['repricing_period_slug'] ?? '',
                'company_tin' => $row['company_tin'] ?? '',
                'yes_for_faq_solaris_project' => $row['y'] ?? '',
                'n_for_faq_solaris_project' => $row['n'] ?? '',
                'interest' => $row['interest'] ?? '',
                'total_selling_price' => $row['total_selling_price'] ?? '',
                'seller' => [
                    'unit' => $row['selling_unit'] ?? '',
                    'id' => $row['seller_id'] ?? '',
                    'name' => $row['seller_name'] ?? '',
                    'superior' => $row['seller_superior'] ?? '',
                    'team_head' => $row['sales_team_head'] ?? '',
                    'chief_seller_officer' => $row['chief_seller_officer'] ?? '',
                    'deputy_chief_seller_officer' => $row['chief_seller_officer'] ?? '',
                    'type' => $row['seller_type'] ?? '',
                    'reference_no' => $row['seller_reference_no'] ?? '',
                ],
                'payment_scheme' => [
                    'scheme' => $row['payment_scheme'] ?? '',
                    'method' => $row['payment_method_name'] ?? '',
                    'collectible_price' => $row['collectible_price'] ?? 0,
                    'commissionable_amount' => $row['commissionable_amount'] ?? 0,
                    'evat_percentage' => $row['evat_percentage'] ?? '',
                    'evat_amount' => $row['evat_amount'] ?? '',
                    'net_total_contract_price' => $row['net_total_contract_price'] ?? '',
                    'total_contract_price' => $row['tcp'] ?? 0,
                    'payments' => [
                        [
                            'type' => 'processing_fee',
                            'amount_paid' => $row['pf_amount_paid'] ?? 0,
                            'date' => $this->convertExcelDate($row['pf_payment_date'] ?? ''),
                            'reference_number' => $row['pf_payment_reference_number'] ?? '',
                        ],
                        [
                            'type' => 'home_utility_connection_fee',
                            'amount_paid' => $row['hucf_amount_paid'] ?? 0,
                            'date' => $this->convertExcelDate($row['hucf_payment_date'] ?? ''),
                            'reference_number' => $row['hucf_payment_reference_number'] ?? '',
                        ],
                        [
                            'type' => 'balance',
                            'amount_paid' => $row['balance_payment_amount_paid'] ?? 0,
                            'date' => $this->convertExcelDate($row['balance_payment_date'] ?? ''),
                            'reference_number' => $row['balance_payment_reference_number'] ?? '',
                        ],
                        [
                            'type' => 'equity',
                            'amount_paid' => $row['equity_payment_amount_paid'] ?? 0,
                            'date' => $this->convertExcelDate($row['equity_payment_date'] ?? ''),
                            'reference_number' => $row['equity_payment_reference_number'] ?? '',
                        ],
                    ],
                    'fees' => [
                        [
                            'name' => 'processing',
                            'amount' => $row['processing_fee'] ?? $row['reservation_rate_(processing_fee)'] ?? 0,
                        ],
                        [
                            'name' => 'home_utility_connection',
                            'amount' => $row['home_utility_connection_fee'] ?? 0,
                        ],
                        [
                            'name' => 'mrif',
                            'amount' => $row['mrif_fee'] ?? 0,
                        ],
                        [
                            'name' => 'rental',
                            'amount' => $row['rental_fee'] ?? 0,
                        ],
                        [
                            'name' => 'present_rental_fee',
                            'amount' => $row['present_rental_fee'] ?? 0,
                        ],
                    ],
                    'payment_remarks' => $row['payment_remarks'] ?? '',
                    'transaction_remarks' => $row['transaction_remarks'] ?? '',
                    'discount_rate' => $row['discount_rate'] ?? '',
                    'conditional_discount' => $row['conditional_discount'] ?? '',
                    'transaction_sub_status' => $row['transaction_sub_status'] ?? '',
                ],
            ],
            'co_borrowers' => [
                [
                    'name' => $row['aif_name'] ?? '',
                    'first_name' => $row['aif_first_name'] ?? '',
                    'middle_name' => $row['aif_middle_name'] ?? '',
                    'last_name' => $row['aif_last_name'] ?? '',
                    'name_suffix' => $row['aif_extension_name'] ?? '',
                    'date_of_birth' => $this->convertExcelDate($row['aif_birthday'] ?? ''),
                    'civil_status' => $row['co_borrower_civil_status'] ?? '', // TODO: Clarify *aif_civil_status
                    'sex' => $row['aif_gender'] ?? '',
                    'nationality' => $row['co_borrower_nationality'] ?? '', // TODO: Clarify *aif_nationality
                    'spouse' => $row['co_borrower_spouse'] ?? '',
                    'email' => '', // TODO: Look up in Mapping
                    'mobile' => '', // TODO: Look up in Mapping
                    'other_mobile' => '', // TODO: Look up in Mapping
                    'relationship_to_buyer' => $row['aif_relationship_to_buyer'] ?? '',
                    'help_number' => '', // TODO: Look up in Mapping
                    'mothers_maiden_name' => '', // TODO: Look up in Mapping
                    'passport' => $row['aif_passport'] ?? '',
                    'date_issued' => $this->convertExcelDate($row['aif_date_issued'] ?? ''),
                    'place_issued' => $row['aif_date_issued'] ?? '',
                    'tin' => $row['co_borrower_spouse_tin'] ?? '',
                ]
            ],
        ];


        $action = app(PersistContactAction::class);
        $validator = Validator::make($attribs, $action->rules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        $validated = $validator->validated();
        // dd($attribs, $validated);
        $contact = $action->run($validated);
        $contactArray = $contact->toArray();

//        $contactArray['mobile'] = $contact->mobile ? $contact->mobile->formatE164() : null;
//        $contactArray['other_mobile'] = $contact->other_mobile ? $contact->other_mobile->formatE164() : null;
//        $contactArray['help_number'] = $contact->help_number ? $contact->help_number->formatE164() : null;

        $contactArray['date_of_birth'] = $contact->date_of_birth
            ? Carbon::parse($contact->date_of_birth)->format('Y-m-d')
            : null;


        return Contact::updateOrCreate(
            ['reference_code' => $contactArray['reference_code']], // Unique identifier, adjust as needed
            $contactArray
        );
    }

    public function convertExcelDate($date)
    {
        if (is_null($date) || $date === '') {
            return null;
        }

        if (is_numeric($date)) {
            return Carbon::parse(Date::excelToDateTimeObject($date))->format('Y-m-d');
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            } catch (\Exception $e) {
                // If all formats fail, return null
                return null;
            }
        }
    }

    public function headingRow(): int
    {
        return 6;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}