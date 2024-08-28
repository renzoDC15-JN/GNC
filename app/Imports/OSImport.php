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
        $attribs  = [
            //
            'reference_code'=>(string) $row['brn'],
            'spouse' => [
                'first_name' => $row['buyer_spouse_first_name'] ?? '',
                'middle_name' => $row['buyer_spouse_middle_name'] ?? '',
                'last_name' =>  $row['buyer_spouse_last_name'] ?? '',
                'name_suffix' =>  $row['buyer_spouse_name_suffix'] ?? '',
                'mothers_maiden_name' =>  $row['buyer_spouse_mothers_maiden_name'] ?? '', // missing in MAP
                'civil_status' =>  $row['buyer_spouse_civil_status'] ?? '', // missing in MAP
                'sex' =>  $row['buyer_spouse_gender'] ?? '',
                'nationality' =>$row['buyer_spouse_nationality'] ?? '',
                'date_of_birth' =>(isset($row['buyer_spouse_date_of_birth']) && (is_int($row['buyer_spouse_date_of_birth']) || is_float($row['buyer_spouse_date_of_birth']))) ? $row['buyer_spouse_date_of_birth'] : '',
                'email' =>  $row['buyer_spouse_email'] ?? '',
                'mobile' => $row['buyer_spouse_mobile'] ?? '',
                'other_mobile' => null, // Missing in MAP
                'help_number' => null, // Missing in MAP
                'client_id' => $row['client_id_spouse'] ?? '',
                'landline' => $row['buyer_spouse_landline'] ?? '',
                'age' => $row['spouse_age'] ?? '',
            ],

            'first_name' => Str::title($row['buyer_first_name'] ?? '' ),
            'middle_name' => Str::title($row['buyer_middle_name']) ?: 'Missing',
            'last_name' => Str::title($row['buyer_last_name'] ?? ''),
            'name_suffix' => Str::title($row['buyer_name_suffix'] ?? ''),
            'civil_status' => Str::title($row['buyer_civil_status']),
            'sex' => Str::title($row['buyer_gender']),
            'nationality' => Str::title($row['buyer_nationality']),
            'date_of_birth' =>  $this->convertExcelDate($row['date_of_birth']),
            'email' => strtolower($row['buyer_principal_email']),
            'mobile' => (string) $row['buyer_primary_contact_number'], //TODO: update this
            'help_number' => (string) ($row['buyer_help_number'] ?? ''),
            'landline' => (string) $row['buyer_help_number'] ?? '',
            'other_mobile' => (string) $row['buyer_other_contact_number'],
            'mothers_maiden_name' =>  $row['buyer_mothers_maiden_name'] ?? '',
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
                    'sorting_code' => null,
                    'country' => 'PH',
                    'block' =>  Str::title($row['buyer_block'] ?? ''),
                    'lot' =>  Str::title($row['buyer_lot'] ?? ''),
                    'unit' =>  Str::title($row['buyer_unit'] ?? ''),
                    'floor' =>  Str::title($row['buyer_floor'] ?? ''),
                    'street' =>  Str::title($row['buyer_street'] ?? ''),
                    'building' =>  Str::title($row['buyer_building'] ?? ''),
                    'length_of_stay' => $row['buyer_length_of_stay'] ?? '',
                ],
                [
                    'type' => 'co_borrower',
                    'ownership' => Str::title($row['buyer_ownership_type'] ?? ''),
                    'full_address' => Str::title($row['co_borrower_address'] ?? ''),
                    'address1' => Str::title($row['buyer_place_of_residency_1_(city_of_residency)'] ?? ''),
                    'address2' => Str::title($row['buyer_place_of_residency_2_(province_of_residency)'] ?? ''),
                    'sublocality' => Str::title($row['buyer_barangay'] ?? ''),
                    'locality' => Str::title($row['buyer_city'] ?? ''),
                    'administrative_area' => Str::title($row['buyer_province'] ?? ''),
                    'postal_code' => Str::title($row['buyer_zip_code'] ?? ''),
                    'sorting_code' => null,
                    'country' => 'PH',
                    'block' =>  Str::title($row['buyer_block'] ?? ''),
                    'lot' =>  Str::title($row['buyer_lot'] ?? ''),
                    'unit' =>  Str::title($row['buyer_unit'] ?? ''),
                    'floor' =>  Str::title($row['buyer_floor'] ?? ''),
                    'street' =>  Str::title($row['buyer_street'] ?? ''),
                    'building' =>  Str::title($row['buyer_building'] ?? ''),
                    'length_of_stay' => $row['buyer_length_of_stay'] ?? '',
                ],
                [
                    'type' => 'spouse',
                    'ownership' => Str::title($row['buyer_ownership_type'] ?? ''),
                    'full_address' => null,
                    'address1' => Str::title($row['buyer_place_of_residency_1_(city_of_residency)'] ?? ''),
                    'address2' => Str::title($row['buyer_place_of_residency_2_(province_of_residency)'] ?? ''),
                    'sublocality' => Str::title($row['buyer_barangay'] ?? ''),
                    'locality' => Str::title($row['buyer_city'] ?? ''),
                    'administrative_area' => Str::title($row['buyer_province'] ?? ''),
                    'postal_code' => Str::title($row['buyer_zip_code'] ?? ''),
                    'sorting_code' => null,
                    'country' => 'PH',

                    'block' =>  Str::title($row['buyer_block'] ?? ''),
                    'lot' =>  Str::title($row['buyer_lot'] ?? ''),
                    'unit' =>  Str::title($row['buyer_unit'] ?? ''),
                    'floor' =>  Str::title($row['buyer_floor'] ?? ''),
                    'street' =>  Str::title($row['buyer_street'] ?? ''),
                    'building' =>  Str::title($row['buyer_building'] ?? ''),
                    'length_of_stay' => $row['buyer_length_of_stay'] ?? '',
                ],


            ],
            'employment' =>[
                [
                    'type'=>'buyer',
                    'employment_status' => Str::title($row['buyer_employer_status'] ?? '' ),
                    'monthly_gross_income' => (string) $row['buyer_salary_gross_income'] ?? '',
                    'current_position' => Str::title($row['buyer_position'] ?? ''),
                    'employment_type' => Str::title($row['buyer_employer_type'] ?? ''),
                    'years_in_service' => Str::title($row['buyer_years_in_service'] ?? ''),
                    'salary_range' => $row['buyer_salary_range'] ?? '',
                    'department_name' => $row['department_name'] ?? '',
                    'employer' => [
                        'name' => Str::title($row['buyer_employer_name'] ?? ''),
                        'industry' => Str::title($row['industry'] ?? ''),
                        'type' => Str::title($row['buyer_employer_type'] ?? ''),
                        'status' => Str::title($row['buyer_employer_status'] ?? ''),
                        'year_established' => Str::title($row['buyer_employer_year_established'] ?? ''),
                        'total_number_of_employees' => $row['buyer_employer_total_number_of_employees'] ?? '',
                        'email' => $row['buyer_employer_email'] ?? '',
                        'nationality' => 'PH',
                        'contact_no' => (string) $row['buyer_employer_contact_number'] ?? '',
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
                            'sorting_code' => null,
                            'country' => 'PH',

                            'block' =>  Str::title($row['buyer_employer_block'] ?? ''),
                            'lot' =>  Str::title($row['buyer_employer_lot'] ?? ''),
                            'unit' =>  Str::title($row['buyer_employer_unit'] ?? ''),
                            'floor' =>  Str::title($row['buyer_employer_floor'] ?? ''),
                            'street' =>  Str::title($row['buyer_employer_street'] ?? ''),
                            'building' =>  Str::title($row['buyer_employer_building'] ?? ''),
                            'length_of_stay' => $row['buyer_employer_length_of_stay'] ?? '',
                        ],
                    ],
                    'id' => [
                        'tin' => (string) $row['buyer_tax_identification_number'] ?? '',
                        'sss' => (string) $row['buyer_sss_gsis_number'] ?? '', //TODO: process sss or gsis
                        'pagibig' => (string) $row['buyer_pag_ibig_number'] ?? '',
                        'gsis' => (string) ($row['buyer_sss_gsis_number'] ?? ''),
                    ],
                ],
                [
                    'type'=>'spouse',
                    'employment_status' => Str::title($row['spouse_employer_status'] ?? ''),
                    'monthly_gross_income' => (string) ($row['spouse_salary_gross_income'] ?? ''),
                    'current_position' => Str::title($row['spouse_position'] ?? ''),
                    'employment_type' => Str::title($row['spouse_employer_type'] ?? ''),
                    'years_in_service' => Str::title($row['spouse_years_in_service'] ?? ''),
                    'salary_range' => $row['spouse_salary_range'] ?? '',
                    'department_name' => $row['spouse_department_name'] ?? '',
                    'employer' => [
                        'name' => Str::title($row['spouse_employer_name'] ?? ''),
                        'industry' => Str::title($row['spouse_industry'] ?? ''),
                        'type' => Str::title($row['spouse_employer_type'] ?? ''),
                        'status' => Str::title($row['spouse_employer_status'] ?? ''),
                        'year_established' => Str::title($row['spouse_employer_year_stablished'] ?? ''),
                        'total_number_of_employees' => $row['spouse_employer_total_number_of_employees'] ?? '',
                        'email' => $row['spouse_employer_email'] ?? '',
                        'nationality' => 'PH',
                        'contact_no' => (string) ($row['spouse_employer_contact_number'] ?? ''),
                        'fax' => $row['aif_fax'] ?? '',
                        'address' => [
                            'type' => 'work',
                            'ownership' => 'N/A',
                            'full_address' => null,
                            'address1' => Str::title($row['spouse_place_of_work_1_(city_of_residency)'] ?? ''),
                            'address2' => Str::title($row['spouse_place_of_work_2_(province_of_residency)'] ?? ''),
                            'sublocality' => Str::title($row['spouse_employer_barangay'] ?? ''),
                            'locality' => Str::title($row['spouse_employer_city'] ?? ''),
                            'administrative_area' => Str::title($row['spouse_employer_province'] ?? ''),
                            'postal_code' => Str::title($row['spouse_employer_zip_code'] ?? ''),
                            'sorting_code' => null,
                            'country' => 'PH',
                            'block' =>  Str::title($row['spouse_employer_block'] ?? ''),
                            'lot' =>  Str::title($row['spouse_employer_lot'] ?? ''),
                            'unit' =>  Str::title($row['spouse_employer_unit'] ?? ''),
                            'floor' =>  Str::title($row['spouse_employer_floor'] ?? ''),
                            'street' =>  Str::title($row['spouse_employer_street'] ?? ''),
                            'building' =>  Str::title($row['spouse_employer_building'] ?? ''),
                            'length_of_stay' => $row['spouse_employer_length_of_stay'] ?? '',
                        ],
                    ],
                    'id' => [
                        'tin' => (string) ($row['spouse_tin'] ?? ''),
                        'sss' => (string) ($row['spouse_sss_gsis_number'] ?? ''), //TODO: process sss or gsis
                        'pagibig' => (string) ($row['spouse_pag_ibig_number'] ?? ''),
                        'gsis' => (string) ($row['spouse_sss_gsis_number'] ?? ''),
                    ],
                ],
                [
                    'type'=>'co_borrower',
                    'employment_status' => Str::title($row['co_borrower_employer_status'] ?? ''),
                    'monthly_gross_income' => (string) ($row['co_borrower_salary_gross_income'] ?? ''),
                    'current_position' => Str::title($row['co_borrower_position'] ?? ''),
                    'employment_type' => Str::title($row['co_borrower_employer_type'] ?? ''),
                    'years_in_service' => Str::title($row['co_borrower_years_in_service'] ?? ''),
                    'salary_range' => $row['co_borrower_salary_range'] ?? '',
                    'department_name' => $row['co_borrower_department_name'] ?? '',
                    'employer' => [
                        'name' => Str::title($row['co_borrower_employer_name'] ?? ''),
                        'industry' => Str::title($row['co_borrower_industry'] ?? ''),
                        'type' => Str::title($row['co_borrower_employer_type'] ?? ''),
                        'status' => Str::title($row['co_borrower_employer_status'] ?? ''),
                        'year_established' => Str::title($row['co_borrower_employer_year_stablished'] ?? ''),
                        'total_number_of_employees' => $row['co_borrower_employer_total_number_of_employees'] ?? '',
                        'email' => $row['co_borrower_employer_email'] ?? '',
                        'nationality' => 'PH',
                        'contact_no' => (string) ($row['co_borrower_employer_contact_number'] ?? ''),
                        'fax' =>  $row['aif_fax'] ?? '',
                        'address' => [
                            'type' => 'work',
                            'ownership' => 'N/A',
                            'full_address' => Str::title($row['co_borrower_address'] ?? ''),
                            'address1' => Str::title($row['co_borrower_place_of_work_1_(city_of_residency)'] ?? ''),
                            'address2' => Str::title($row['co_borrower_place_of_work_2_(province_of_residency)'] ?? ''),
                            'sublocality' => Str::title($row['co_borrower_employer_barangay'] ?? ''),
                            'locality' => Str::title($row['co_borrower_employer_city'] ?? ''),
                            'administrative_area' => Str::title($row['co_borrower_employer_province'] ?? ''),
                            'postal_code' => Str::title($row['co_borrower_employer_zip_code'] ?? ''),
                            'sorting_code' => null,
                            'country' => 'PH',
                            'block' =>  Str::title($row['co_borrower_employer_block'] ?? ''),
                            'lot' =>  Str::title($row['co_borrower_employer_lot'] ?? ''),
                            'unit' =>  Str::title($row['co_borrower_employer_unit'] ?? ''),
                            'floor' =>  Str::title($row['co_borrower_employer_floor'] ?? ''),
                            'street' =>  Str::title($row['co_borrower_employer_street'] ?? ''),
                            'building' =>  Str::title($row['co_borrower_employer_building'] ?? ''),
                            'length_of_stay' => $row['co_borrower_employer_length_of_stay'] ?? '',
                        ],
                    ],
                    'id' => [
                        'tin' => (string) ($row['co_borrower_tin'] ?? ''),
                        'sss' => (string) ($row['co_borrower_sss_gsis_number'] ?? ''), //TODO: process sss or gsis
                        'pagibig' => (string) ($row['co_borrower_pag_ibig_number'] ?? ''),
                        'gsis' => (string) ($row['co_borrower_sss_gsis_number'] ?? ''),
                    ],
                ],
            ],
            'order'=>[
                //additional for gnc 7-15-2024
                'sku' => Str::title($row['sku'] ?? ''),
                'seller_commission_code' => Str::title($row['seller_commission_code'] ?? ''),
                'property_code' => Str::title($row['property_code'] ?? ''),
                'property_type' => Str::title($row['property_type'] ?? ''),
                'company_name' => Str::title($row['company_name'] ?? ''),
                'project_name' => Str::title($row['project_name'] ?? ''),
                'project_code' => Str::title($row['project_code'] ?? ''),
                'property_name' => Str::title($row['property_name']??$row['property_code'] ?? ''),
                'phase' =>(string) $row['phase'] ?? '',
                'block' =>(string) $row['block'] ?? '',
                'lot' => (string) $row['lot'] ?? '',
                'lot_area' => $row['lot_area'] ?? '',
                'floor_area' => $row['floor_area'] ?? '',
                'tcp' => $row['tcp'] ?? '',
                'loan_term' => $row['bp2_terms']??$row['bp1_terms'],
                'loan_interest_rate' =>$row['bp2_interest_rate']??$row['bp1_interest_rate'] ?? '',
                'tct_no' => $row['tct_no'] ?? '',
                'interest' => $row['interest'] ?? '',
                'project_location' => Str::title($row['project_location'] ?? ''),
                'project_address' => Str::title($row['project_address'] ?? ''),
                'mrif_fee' => $row['mrif_fee'] ?? '',
                'reservation_rate' => $row['reservation_rate_processing_fee'] ?? '',
                'class_field' => $row['class_field'] ?? '',
                'segment_field' => $row['segment_field'] ?? '',
                'rebooked_id_form' => $row['rebooked_id_form'] ?? '',
                'buyer_action_form_number' => $row['buyer_action_form_number'] ?? '',
                'buyer_action_form_date' => (isset($row['buyer_action_form_date']) && (is_int($row['buyer_action_form_date']) || is_float($row['buyer_action_form_date']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['buyer_action_form_date'])) : '',
                'cancellation_type' => $row['cancellation_type'] ?? '',
                'cancellation_reason' => $row['cancellation_reason'] ?? '',
                'cancellation_reason2' => $row['cancellation_reason2'] ?? '',
                'cancellation_remarks' => $row['cancellation_remarks'] ?? '',

                'unit_type' => $row['unit_type'] ?? '',
                'unit_type_interior' => $row['unit_type_interior'] ?? '',
                'house_color' => $row['house_color'] ?? '',
                'construction_status' => $row['construction_status'] ?? '',
                'transaction_reference' => $row['transaction_reference'] ?? '',
                'reservation_date' => (isset($row['reservation_date']) && (is_int($row['reservation_date']) || is_float($row['reservation_date']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['reservation_date'])) : '',
                'circular_number' => $row['circular_number'] ?? '',

                // For checking
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
                'monthly_amort1' => $row['monthly_amort1'] ?? '',
                'monthly_amort2' => $row['monthly_amort2'] ?? '',
                'monthly_amort3' => $row['monthly_amort3'] ?? '',
                'equity_1_amount' => $row['equity_1_amount'] ?? '',
                'equity_1_percentage_rate' => $row['equity_1_percentage_rate'] ?? '',
                'equity_1_interest_rate' => $row['equity_1_interest_rate'] ?? '',
                'equity_1_terms' => $row['equity_1_terms'] ?? '',
                'equity_1_monthly_payment' => $row['equity_1_monthly_payment'] ?? '',
                'equity_1_effective_date' => (isset($row['equity_1_effective_date']) && (is_int($row['equity_1_effective_date']) || is_float($row['equity_1_effective_date']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['equity_1_effective_date'])) : '',
                'equity_2_amount' => $row['equity_2_amount'] ?? '',
                'equity_2_percentage_rate' => $row['equity_2_percentage_rate'] ?? '',
                'equity_2_interest_rate' => $row['equity_2_interest_rate'] ?? '',
                'equity_2_terms' => $row['equity_2_terms'] ?? '',

                // For checking
                'cash_outlay_1_terms' => $row['cash_outlay_1_terms'] ?? '',
                'cash_outlay_1_monthly_payment' => $row['cash_outlay_1_monthly_payment'] ?? '',
                'cash_outlay_1_effective_date' => (isset($row['cash_outlay_1_effective_date']) && (is_int($row['cash_outlay_1_effective_date']) || is_float($row['cash_outlay_1_effective_date']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['cash_outlay_1_effective_date'])) : '',
                'cash_outlay_2_amount' => (isset($row['cash_outlay_2_amount']) && (is_int($row['cash_outlay_2_amount']) || is_float($row['cash_outlay_2_amount']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['cash_outlay_2_amount'])) : '',
                'cash_outlay_2_percentage_rate' => $row['cash_outlay_2_percentage_rate'] ?? '',
                'cash_outlay_2_interest_rate' => $row['cash_outlay_2_interest_rate'] ?? '',
                'cash_outlay_2_terms' => $row['cash_outlay_2_terms'] ?? '',
                'cash_outlay_2_monthly_payment' => $row['cash_outlay_2_monthly_payment'] ?? '',
                'cash_outlay_2_effective_date' => (isset($row['cash_outlay_2_effective_date']) && (is_int($row['cash_outlay_2_effective_date']) || is_float($row['cash_outlay_2_effective_date']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['cash_outlay_2_effective_date'])) : '',
                'cash_outlay_3_amount' => $row['cash_outlay_3_amount'] ?? '',
                'cash_outlay_3_percentage_rate' => $row['cash_outlay_3_percentage_rate'] ?? '',
                'cash_outlay_3_interest_rate' => $row['cash_outlay_3_interest_rate'] ?? '',
                'cash_outlay_3_terms' => $row['cash_outlay_3_terms'] ?? '',
                'cash_outlay_3_monthly_payment' => $row['cash_outlay_3_monthly_payment'] ?? '',
                'cash_outlay_3_effective_date' => (isset($row['cash_outlay_3_effective_date']) && (is_int($row['cash_outlay_3_effective_date']) || is_float($row['cash_outlay_3_effective_date']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['cash_outlay_3_effective_date'])) : '',
                'page' => $row['page'] ?? '',

                // For checking
                'building' => $row['building'] ?? '',
                'floor' => $row['floor'] ?? '',
                'unit' => $row['unit'] ?? '',
                'cct' => $row['cct'] ?? '',
                'witness1' => $row['witness1'] ?? '',
                'witness2' => $row['witness2'] ?? '',
                'buyer_extension_name' => $row['buyer_extension_name'] ?? '',
                'company_acronym' => $row['company_acronym'] ?? '',
                'repricing_period_in_words' => $row['repricing_period_in_words'] ?? '',
                'repricing_period' => $row['repricing_period'] ?? '',
                'company_address' => $row['company_address'] ?? '',
                'exec_position' => $row['exec_position'] ?? '',
                'board_resolution_date' => (isset($row['board_resolution_date']) && (is_int($row['board_resolution_date']) || is_float($row['board_resolution_date']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['board_resolution_date'])) : '',
                'registry_of_deeds_address' => $row['registry_of_deeds_address'] ?? '',
                'exec_tin' => $row['exec_tin'] ?? '',
                'loan_period_in_words' => $row['loan_period_in_words'] ?? '',
                'spouse_address' => $row['spouse_address'] ?? '',
                'total_miscellaneous_fee_in_words' => $row['total_miscellaneous_fee_in_words'] ?? '',
                'tmf' => $row['tmf'] ?? '',

                // For Checking
                'cash_outlay_1_amount' => $row['cash_outlay_1_amount'] ?? '',
                'cash_outlay_1_percentage_rate' => $row['cash_outlay_1_percentage_rate'] ?? '',
                'cash_outlay_1_interest_rate' => $row['cash_outlay_1_interest_rate'] ?? '',
                'equity_2_monthly_payment' => $row['equity_2_monthly_payment'] ?? '',
                'equity_2_effective_date' => (isset($row['equity_2_effective_date']) && (is_int($row['equity_2_effective_date']) || is_float($row['equity_2_effective_date']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['equity_2_effective_date'])) : '',
                'bp_1_amount' => $row['bp_1_amount'] ?? '',
                'bp_1_percentage_rate' => $row['bp_1_percentage_rate'] ?? '',
                'bp_1_interest_rate' => $row['bp_1_interest_rate'] ?? '',
                'bp_1_terms' => $row['bp_1_terms'] ?? '',
                'bp_1_monthly_payment' => $row['bp_1_monthly_payment'] ?? '',
                'bp_1_effective_date' => (isset($row['bp_1_effective_date']) && (is_int($row['bp_1_effective_date']) || is_float($row['bp_1_effective_date']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['bp_1_effective_date'])) : '',
                'bp_2_amount' => $row['bp_2_amount'] ?? '',
                'bp_2_percentage_rate' => $row['bp_2_percentage_rate'] ?? '',
                'bp_2_interest_rate' => $row['bp_2_interest_rate'] ?? '',
                'bp_2_terms' => $row['bp_2_terms'] ?? '',
                'bp_2_monthly_payment' => $row['bp_2_monthly_payment'] ?? '',
                'bp_2_effective_date' => (isset($row['bp_2_effective_date']) && (is_int($row['bp_2_effective_date']) || is_float($row['bp_2_effective_date']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['bp_2_effective_date'])) : '',
                'circular_no_312_379' => $row['circular_no._(312/379)'] ?? '',
                'tcp_in_words' => $row['tcp_in_words'] ?? '',
                'interest_in_words' => $row['interest_in_words'] ?? '',
                'logo' => $row['logo'] ?? '',
                'loan_period_months' => $row['loan_period_months'] ?? '',
                'exec_signatories' => $row['exec_signatories'] ?? '',
                'exec_tin_no' => $row['exec_tin_no'] ?? '',
                'loan_terms_in_word' => $row['loan_terms_in_word'] ?? '',
                'loan_value_after_downpayment' => $row['loan_value'] ?? '',

                'date_created'=> (isset($row['date_created']) && (is_int($row['date_created']) || is_float($row['date_created']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['date_created'])) : '',
                'ra_date'=> (isset($row['ra_date']) && (is_int($row['ra_date']) || is_float($row['ra_date']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['ra_date'])) : '',
                'date_approved'=> (isset($row['date_approved']) && (is_int($row['date_approved']) || is_float($row['date_approved']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['date_approved'])) : '',
                'date_expiration'=> (isset($row['date_expiration']) && (is_int($row['date_expiration']) || is_float($row['date_expiration']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['date_expiration'])) : '',
                'os_month'=>$row['os_month'],
                'due_date'=> (isset($row['due_date']) && (is_int($row['due_date']) || is_float($row['due_date']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['due_date'])) : '',
                'total_payments_made'=>$row['total_payments_made'],
                'transaction_status'=>$row['transaction_status'],
                'staging_status'=>$row['staging_status'],
                'period_id'=>$row['period_id'],
                'date_closed'=> (isset($row['date_closed']) && (is_int($row['date_closed']) || is_float($row['date_closed']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['date_closed'])) : '',
                'closed_reason'=>$row['closed_reason'],
                'date_cancellation'=> (isset($row['date_cancellation']) && (is_int($row['date_cancellation']) || is_float($row['date_cancellation']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['date_cancellation'])) : '',

                'baf_number' => $row['baf_number'],
                'baf_date' => (isset($row['baf_date']) && (is_int($row['baf_date']) || is_float($row['baf_date']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['baf_date'])) : '',
                'client_id_buyer' => $row['client_id_buyer'],
                'buyer_age' => $row['buyer_age'],
                'hucf_move_in_fee' =>$row['hucf/move-in_fee'] ?? '',
                'ltvr_slug' =>$row['ltvr_slug'] ?? '',
                'repricing_period_slug' =>$row['repricing_period_slug'] ?? '',
                'company_tin' =>$row['company_tin'] ?? '',
                'yes_for_faq_solaris_project' =>$row['y'] ?? '',
                'n_for_faq_solaris_project' =>$row['n'] ?? '',

                'seller'=>[
                    'unit'=>$row['selling_unit'] ?? '',
                    'id'=>$row['seller_id'] ?? '',
                    'name'=>$row['seller_name'] ?? '',
                    'superior'=>$row['seller_superior'] ?? '',
                    'team_head'=>$row['sales_team_head'] ?? '',
                    'chief_seller_officer'=>$row['chief_seller_officer'] ?? '',
                    'deputy_chief_seller_officer'=>$row['chief_seller_officer'] ?? '',
                    'type'=>$row['seller_type'] ?? '',
                    'reference_no'=>$row['seller_reference_no'] ?? '',
                ],
                'payment_scheme'=> [
                    'scheme'=>$row['payment_scheme'] ?? '',
                    'method'=>$row['payment_method_name'] ?? '',
                    'collectible_price'=>$row['collectible_price'] ?? '',
                    'commissionable_amount'=>$row['commissionable_amount'] ?? '',
                    'evat_percentage'=>$row['evat_percentage'] ?? '',
                    'evat_amount'=>$row['evat_amount'] ?? '',
                    'net_total_contract_price'=>$row['net_total_contract_price'] ?? '',
                    'total_contract_price'=>$row['tcp'] ?? '',
                    'payments'=>[
                        [
                            'type'=>'processing_fee',
                            'amount_paid'=>$row['pf_amount_paid'],
                            'date'=> (isset($row['pf_payment_date']) && (is_int($row['pf_payment_date']) || is_float($row['pf_payment_date']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['pf_payment_date'])) : null,
                            'reference_number'=>$row['pf_payment_reference_number'],
                        ],
                        [
                            'type'=>'home_utility_connection_fee',
                            'amount_paid'=>$row['hucf_amount_paid'],
                            'date'=>(isset($row['hucf_payment_date']) && (is_int($row['hucf_payment_date']) || is_float($row['hucf_payment_date']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['hucf_payment_date'])) : null,
                            'reference_number'=>$row['hucf_payment_reference_number'],
                        ],
                        [
                            'type'=>'balance',
                            'amount_paid'=>$row['balance_payment_amount_paid'],
                            'date'=> (isset($row['balance_payment_date']) && (is_int($row['balance_payment_date']) || is_float($row['balance_payment_date']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['balance_payment_date'])) : null,
                            'reference_number'=>$row['balance_payment_reference_number'],
                        ],
                        [
                            'type'=>'equity',
                            'amount_paid'=>$row['equity_payment_amount_paid'],
                            'date'=> (isset($row['equity_payment_date']) && (is_int($row['equity_payment_date']) || is_float($row['equity_payment_date']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['equity_payment_date'])) : null,
                            'reference_number'=>$row['equity_payment_reference_number'],
                        ],
                    ],
                    'fees'=>[
                        [
                            'name'=>'processing',
                            'amount'=>$row['processing_fee']?? $row['reservation_rate_(processing_fee)'] ?? '',
                        ],
                        [
                            'name'=>'home_utility_connection',
                            'amount'=>$row['home_utility_connection_fee'] ?? '',
                        ],
                        [
                            'name'=>'mrif',
                            'amount'=>$row['mrif_fee'] ?? '',
                        ],
                        [
                            'name'=>'rental',
                            'amount'=>$row['rental_fee'] ?? '',
                        ],
                    ],
                    'payment_remarks'=>$row['payment_remarks'] ?? '',
                    'transaction_remarks'=>$row['transaction_remarks'] ?? '',
                    'discount_rate'=>$row['discount_rate'] ?? '',
                    'conditional_discount'=>$row['conditional_discount'] ?? '',
                    'transaction_sub_status'=>$row['transaction_sub_status'] ?? '',
                    'total_selling_price'=>$row['total_selling_price'] ?? '',

                ]

            ],
            'co_borrowers' => [
                [
                    'name' => $row['aif_name'] ?? '',
                    'first_name' => $row['aif_first_name'] ?? '',
                    'middle_name' => $row['aif_middle_name'] ?? '',
                    'last_name' => $row['aif_last_name'] ?? '',
                    'name_suffix' => $row['aif_extension_name'] ?? '',
                    'date_of_birth' => $row['aif_birthday'] ?? '',
                    'civil_status' => $row['co_borrower_civil_status'] ?? '', // TODO: Clarify *aif_civil_status
                    'sex' => $row['aif_gender'] ?? '',
                    'nationality' => $row['co_borrower_nationality'] ?? '', // TODO: Clarify *aif_nationality
                    'email' => '', // TODO: Look up in Mapping
                    'mobile' => '', // TODO: Look up in Mapping
                    'other_mobile' => '', // TODO: Look up in Mapping
                    'relationship_to_buyer' => $row['aif_relationship_to_buyer'] ?? '',
                    'help_number' => '', // TODO: Look up in Mapping
                    'mothers_maiden_name' => '', // TODO: Look up in Mapping
                    'passport' => $row['aif_passport'] ?? '',
                    'date_issued' => (isset($row['aif_date_issued']) && (is_int($row['aif_date_issued']) || is_float($row['aif_date_issued']))) ? Carbon::createFromDate(Date::excelToDateTimeObject($row['aif_date_issued'])) : '',
                    'place_issued' => $row['aif_date_issued'] ?? '',
                    // 'age' => $row['aif_age'] ?? '',
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



    /**
     * @param array $headerRow
     * @return array
     */
    public function formatHeaderRow(array $headerRow): array
    {
        // dd($headerRow);
        // Custom header row formatting logic here
        return array_map(function ($header) {
            $heading = Str::snake(Str::camel($header));
            return match ($heading) {
                'property_name' => 'property_code', //old
                'b_r_n' => 'brn',
                'o_s_status' => 'os_status',
                'rebooked_i_d_from' => 'rebooked_id_from',
                'b_a_f_number' => 'baf_number',
                'b_a_f_date' => 'baf_date',
                'construction_status%' => 'construction_status',
                'client_i_d(_buyer)' => 'client_id_buyer',
                'h_e_l_p_number' => 'help_number',
                'buyer_unit/_lot' => 'buyer_unit_lot',
                'buyer_place_of_residency1(_city_of_residency)' => 'buyer_place_of_residency_city',
                'buyer_place_of_residency2(_province_of_residency)' => 'buyer_place_of_residency_province',
                'buyer_tax_identifaction_number' => 'buyer_tax_identification_number',
                'buyer_s_s_s/_g_s_i_s_number' => 'buyer_sss_gsis_number',
                'client_i_d(_spouse)' => 'client_id_spouse',
                'client_i_d(_a_i_f)' => 'client_id_aif',
                'a_i_f_name' => 'aif_name',
                'a_i_f_address' => 'aif_address',
                'client_i_d(_co_borrower)' => 'client_id__co_borrower',
                'buyer_place_of_work1(_city_of_employer)' => 'buyer_place_of_employer_city',
                'buyer_place_of_work2(_province_of_employer)' => 'buyer_place_of_employer_province',
                'buyer_salary/_gross_income' => 'buyer_salary_gross_income',
                'seller_i_d' => 'seller_id',
                'c_s_o/_d_c_s_o' => 'cso_dcso',
                'r_a_date' => 'ra_date',
                'o_s_month' => 'os_month',
                'period_i_d(_r_e,_d_p,_b_p,_m_f,_fully_paid)' => 'period_id',
                'e_v_a_t_percentage' => 'evat_percentage',
                'e_v_a_t_amount' => 'evat_amount',
                'm_r_i_f_fee' => 'mrif_fee',
                'h_u_c_f/_move_in_fee' => 'hucf_move_in_fee',
                'reservation_rate(_processing_fee)' => 'reservation_rate_processing_fee',
                'b_p1_amount' => 'bp1_amount',
                'b_p1_percentage_rate' => 'bp1_percentage_rate',
                'b_p1_interest_rate' => 'bp1_interest_rate',
                'b_p1_terms' => 'bp1_terms',
                'b_p1_monthly_payment' => 'bp1_monthly_payment',
                'b_p1_effective_date' => 'bp1_effective_date',
                'b_p2_amount' => 'bp2_amount',
                'b_p2_percentage_rate' => 'bp2_percentage_rate',
                'b_p2_interest_rate' => 'bp2_interest_rate',
                'b_p2_terms' => 'bp2_terms',
                'b_p2_monthly_payment' => 'bp2_monthly_payment',
                'b_p2_effective_date' => 'bp2_effective_date',
                'circular_no.(312/379)' => 'circular_312_379',
                'l_t_v_r_slug' => 'ltvr_slug',
                'p_f_amount_paid' => 'pf_amount_paid',
                'p_f_payment_reference_number' => 'pf_payment_reference_number',
                'p_f_payment_date' => 'pf_payment_date',
                'h_u_c_f_amount_paid' => 'hucf_amount_paid',
                'h_u_c_f_payment_reference_number' => 'hucf_payment_reference_number',
                'h_u_c_f_payment_date' => 'hucf_payment_date',
                'total_contract_price'=>'tcp',
                'transfer_certificate_of_title'=>'tct_no',

                'buyer_primary_contact_number' => 'buyer_primary_contact_number',
                'buyer_help_number' => 'buyer_help_number',
                'buyer_other_contact_number' => 'buyer_other_contact_number',
                'buyer_mothers_maiden_name' => 'buyer_mothers_maiden_name',
                'buyer_ownership_type' => 'buyer_ownership_type',
                'buyer_barangay' => 'buyer_barangay',
                'buyer_city' => 'buyer_city',
                'buyer_province' => 'buyer_province',
                'buyer_zip_code' => 'buyer_zip_code',
                'buyer_block' => 'buyer_block',
                'buyer_lot' => 'buyer_lot',
                'buyer_unit' => 'buyer_unit',
                'buyer_floor' => 'buyer_floor',
                'buyer_street' => 'buyer_street',
                'buyer_building' => 'buyer_building',
                'buyer_length_of_stay' => 'buyer_length_of_stay',
                'buyer_employer_status' => 'buyer_employer_status',
                'buyer_salary_gross_income' => 'buyer_salary_gross_income',
                'buyer_position' => 'buyer_position',
                'buyer_employer_type' => 'buyer_employer_type',
                'buyer_years_in_service' => 'buyer_years_in_service',
                'buyer_salary_range' => 'buyer_salary_range',
                'department_name' => 'department_name',
                'buyer_employer_name' => 'buyer_employer_name',
                'buyer_industry' => 'buyer_industry',
                'buyer_employer_year_stablished' => 'buyer_employer_year_stablished',
                'buyer_employer_total_number_of_employees' => 'buyer_employer_total_number_of_employees',
                'buyer_employer_email' => 'buyer_employer_email',
                'buyer_employer_contact_number' => 'buyer_employer_contact_number',
                'buyer_employer_barangay' => 'buyer_employer_barangay',
                'buyer_employer_city' => 'buyer_employer_city',
                'buyer_employer_province' => 'buyer_employer_province',
                'buyer_employer_zip_code' => 'buyer_employer_zip_code',
                'buyer_employer_block' => 'buyer_employer_block',
                'buyer_employer_lot' => 'buyer_employer_lot',
                'buyer_employer_unit' => 'buyer_employer_unit',
                'buyer_employer_floor' => 'buyer_employer_floor',
                'buyer_employer_street' => 'buyer_employer_street',
                'buyer_employer_building' => 'buyer_employer_building',
                'buyer_employer_length_of_stay' => 'buyer_employer_length_of_stay',
                'buyer_tax_identification_number' => 'buyer_tax_identification_number',
                'buyer_pag_ibig_number' => 'buyer_pag_ibig_number',
                'spouse_employer_status' => 'spouse_employer_status',
                'spouse_salary_gross_income' => 'spouse_salary_gross_income',
                'spouse_position' => 'spouse_position',
                'spouse_employer_type' => 'spouse_employer_type',
                'spouse_years_in_service' => 'spouse_years_in_service',
                'spouse_salary_range' => 'spouse_salary_range',
                'spouse_department_name' => 'spouse_department_name',
                'spouse_employer_name' => 'spouse_employer_name',
                'spouse_industry' => 'spouse_industry',
                'spouse_employer_year_stablished' => 'spouse_employer_year_stablished',
                'spouse_employer_total_number_of_employees' => 'spouse_employer_total_number_of_employees',
                'spouse_employer_email' => 'spouse_employer_email',
                'spouse_employer_contact_number' => 'spouse_employer_contact_number',
                'spouse_employer_barangay' => 'spouse_employer_barangay',
                'spouse_employer_city' => 'spouse_employer_city',
                'spouse_employer_province' => 'spouse_employer_province',
                'spouse_employer_zip_code' => 'spouse_employer_zip_code',
                'spouse_employer_block' => 'spouse_employer_block',
                'spouse_employer_lot' => 'spouse_employer_lot',
                'spouse_employer_unit' => 'spouse_employer_unit',
                'spouse_employer_floor' => 'spouse_employer_floor',
                'spouse_employer_street' => 'spouse_employer_street',
                'spouse_employer_building' => 'spouse_employer_building',
                'spouse_employer_length_of_stay' => 'spouse_employer_length_of_stay',
                'spouse_tax_identification_number' => 'spouse_tax_identification_number',
                'spouse_sss_gsis_number' => 'spouse_sss_gsis_number',
                'spouse_pag_ibig_number' => 'spouse_pag_ibig_number',
                'sku' => 'sku',
                'seller_commission_code' => 'seller_commission_code',
                'reservation_payment_reference_number' => 'reservation_payment_reference_number',
                'reservation_payment_date' => 'reservation_payment_date',
                'reservation_amount_paid' => 'reservation_amount_paid',
                'downpayment_payment_reference_number' => 'downpayment_payment_reference_number',
                'downpayment_payment_date' => 'downpayment_payment_date',
                'downpayment_amount_paid' => 'downpayment_amount_paid',
                'additional_payment_reference_number' => 'additional_payment_reference_number',
                'additional_payment_date' => 'additional_payment_date',
                'additional_amount_paid' => 'additional_amount_paid',
                'vat' => 'vat',
                'e_v_a_t' => 'evat',
                'vat_percentage' => 'vat_percentage',
                'vat_amount' => 'vat_amount',
                'tax' => 'tax',
                'tax_percentage' => 'tax_percentage',
                'tax_amount' => 'tax_amount',
                'penalty' => 'penalty',
                'penalty_amount' => 'penalty_amount',
                'total_amount_paid' => 'total_amount_paid',
                'total_balance' => 'total_balance',
                'total_interest' => 'total_interest',
                'total_principal' => 'total_principal',
                'payment_type' => 'payment_type',
                'document_status' => 'document_status',
                'property_status' => 'property_status',
                'buyer_status' => 'buyer_status',
                'spouse_status' => 'spouse_status',
                'co_borrower_status' => 'co_borrower_status',
                'contract_status' => 'contract_status',
                'aif_status' => 'aif_status',
                'help_status' => 'help_status',
                'rebook_status' => 'rebook_status',
                'rebook_reason' => 'rebook_reason',
                'remarks' => 'remarks',
                'source' => 'source',
                'aif_first_name' => 'aif_first_name',
                'aif_middle_name' => 'aif_middle_name',
                'aif_last_name' => 'aif_last_name',
                'aif_unit/lot' => 'aif_unit_lot',
                'aif_street' => 'aif_street',
                'aif_subdivision' => 'aif_subdivision',
                'aif_barangay' => 'aif_barangay',
                'aif_city' => 'aif_city',
                'aif_province' => 'aif_province',
                'aif_zip_code' => 'aif_zip_code',
                'aif_length_of_stay' => 'aif_length_of_stay',
                'aif_ownership_type' => 'aif_ownership_type',
                'aif_birthday' => 'aif_birthday',
                'aif_age' => 'aif_age',
                'aif_gender' => 'aif_gender',
                'aif_civil_status' => 'aif_civil_status',
                'aif_position' => 'aif_position',
                'aif_industry' => 'aif_industry',
                'aif_salary_gross_income' => 'aif_salary_gross_income',
                'aif_company_phone_number' => 'aif_company_phone_number',
                'aif_fax' => 'aif_fax',
                'aif_company_email' => 'aif_company_email',
                'term1' => 'term_1',
                'term2' => 'term_2',
                'term3' => 'term_3',
                'amort_mrisri1' => 'amort_mrisri1',
                'amort_mrisri2' => 'amort_mrisri2',
                'amort_mrisri3' => 'amort_mrisri3',
                'amort_nonlife1' => 'amort_nonlife1',
                'amort_nonlife2' => 'amort_nonlife2',
                'amort_nonlife3' => 'amort_nonlife3',
                'amort_princ_int1' => 'amort_princ_int1',
                'amort_princ_int2' => 'amort_princ_int2',
                'amort_princ_int3' => 'amort_princ_int3',
                'monthly_amort1' => 'monthly_amort1',
                'monthly_amort2' => 'monthly_amort2',
                'monthly_amort3' => 'monthly_amort3',
                'equity_1_-_amount' => 'equity_1_amount',
                'equity_1_-_percentage_rate' => 'equity_1_percentage_rate',
                'equity_1_-_interest_rate' => 'equity_1_interest_rate',
                'equity_1_-_terms' => 'equity_1_terms',
                'equity_1_-_monthly_payment' => 'equity_1_monthly_payment',
                'equity_1_-_effective_date' => 'equity_1_effective_date',
                'equity_2_-_amount' => 'equity_2_amount',
                'equity_2_-_percentage_rate' => 'equity_2_percentage_rate',
                'equity_2_-_interest_rate' => 'equity_2_interest_rate',
                'equity_2_-_terms' => 'equity_2_terms',
                'cash_outlay_1_terms' => 'cash_outlay_1_terms',
                'cash_outlay_1_monthly_payment' => 'cash_outlay_1_monthly_payment',
                'cash_outlay_1_-_effective_date' => 'cash_outlay_1_effective_date',
                'cash_outlay_2_-_amount' => 'cash_outlay_2_amount',
                'cash_outlay_2_-_percentage_rate' => 'cash_outlay_2_percentage_rate',
                'cash_outlay_2_-_interest_rate' => 'cash_outlay_2_interest_rate',
                'cash_outlay_2_-_terms' => 'cash_outlay_2_terms',
                'cash_outlay_2_-_monthly_payment' => 'cash_outlay_2_monthly_payment',
                'cash_outlay_2_-_effective_date' => 'cash_outlay_2_effective_date',
                'cash_outlay_3_-_amount' => 'cash_outlay_3_amount',
                'cash_outlay_3_-_percentage_rate' => 'cash_outlay_3_percentage_rate',
                'cash_outlay_3_-_interest_rate' => 'cash_outlay_3_interest_rate',
                'cash_outlay_3_-_terms' => 'cash_outlay_3_terms',
                'cash_outlay_3_-_monthly_payment' => 'cash_outlay_3_monthly_payment',
                'cash_outlay_3_-_effective_date' => 'cash_outlay_3_effective_date',
                'building' => 'building',
                'floor' => 'floor',
                'unit' => 'unit',
                'cct' => 'cct',
                'wtiness1' => 'wtiness1',
                'wtiness2' => 'wtiness2',
                'buyer_extension_name' => 'buyer_extension_name',
                'company_acronym' => 'company_acronym',
                'repricing_period_in_words' => 'repricing_period_in_words',
                'repricing_period' => 'repricing_period',
                // For Checking
                'project' => 'company_name',
                'project_address' => 'company_address',
                'name_of_executive' => 'exec_possition',
                'board_resolution_date' => 'board_resolution_date',
                'municipality_where_deed_of_sales_is_located' => 'registry_of_deeds_address',
                'exec_tin_number' => 'exec_tin',
                'loan_period_in_words' => 'loan_period_in_words',
                'spouse_address' => 'spouse_address',
                'total_miscellaneous_fee_in_words' => 'total_miscellaneous_fee_in_words',
                'total_miscellaneous_fee' => 'tmf',
                'cash_outlay_1_-_amount' => 'cash_outlay_1_amount',
                'cash_outlay_1_-_percentage_rate' => 'cash_outlay_1_percentage_rate',
                'cash_outlay_1_-_interest_rate' => 'cash_outlay_1_interest_rate',
                'equity_2_-_monthly_payment' => 'equity_2_monthly_payment',
                'equity_2_-_effective_date' => 'equity_2_effective_date',
                'bp_1_-_amount' => 'bp_1_amount',
                'bp_1_-_percentage_rate' => 'bp_1_percentage_rate',
                'bp_1_-_interest_rate' => 'bp_1_interest_rate',
                'bp_1_-_terms' => 'bp_1_terms',
                'bp_1_-_monthly_payment' => 'bp_1_monthly_payment',
                'bp_1_-_effective_date' => 'bp_1_effective_date',
                'bp_2_-_amount' => 'bp_2_amount',
                'bp_2_-_percentage_rate' => 'bp_2_percentage_rate',
                'bp_2_-_interest_rate' => 'bp_2_interest_rate',
                'bp_2_-_terms' => 'bp_2_terms',
                'bp_2_-_monthly_payment' => 'bp_2_monthly_payment',
                'bp_2_-_effective_date' => 'bp_2_effective_date',
                'buyer_age' => 'buyer_age',

                default  => $heading,
            };
        }, $headerRow);
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
