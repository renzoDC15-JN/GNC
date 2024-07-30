<?php

namespace App\Imports;

use App\Models\User;
use Homeful\Contacts\Actions\PersistContactAction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithGroupedHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Faker\Factory as FakerFactory;

HeadingRowFormatter::default('cornerstone-os-report-1');
class OSImport implements ToModel, WithHeadingRow, WithGroupedHeadingRow
{
    use Importable;


    protected $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create();
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
//        dd($row);
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
                'date_of_birth' => $row['buyer_spouse_date_of_birth'] ?? '',
                'email' =>  $row['buyer_spouse_email'] ?? '',
                'mobile' => $row['buyer_spouse_mobile'] ?? '',
                'landline' => $row['buyer_spouse_landline'] ?? '',
            ],
            //
            'first_name' => Str::title($row['buyer_first_name']),
            'middle_name' => Str::title($row['buyer_middle_name']) ?: 'Missing',
            'last_name' => Str::title($row['buyer_last_name']),
            'name_suffix' => Str::title($row['buyer_name_suffix'] ?? ''),
            'civil_status' => Str::title($row['buyer_civil_status']),
            'sex' => Str::title($row['buyer_gender']),
            'nationality' => Str::title($row['buyer_nationality']),
            'date_of_birth' => Carbon::createFromDate(Date::excelToDateTimeObject($row['buyer_birthday'])), //TODO: update this
            'email' => strtolower($row['buyer_principal_email']),
            'mobile' => (string) $row['buyer_primary_contact_number'], //TODO: update this
            'help_number' => (string) ($row['buyer_help_number'] ?? ''),
            'landline' =>  $row['buyer_help_number'] ?? '',
            'other_mobile' =>  $row['buyer_other_contact_number'],
            'mothers_maiden_name' =>  $row['buyer_mothers_maiden_name'] ?? '',
            'addresses' => [
                [
                    'type' => 'primary',
                    'ownership' => Str::title($row['buyer_ownership_type']),
                    'full_address' => null,
                    'address1' => Str::title($row['buyer_place_of_residency_1_(city_of_residency)'] ?? ''),
                    'address2' => Str::title($row['buyer_place_of_residency_2_(province_of_residency)'] ?? ''),
                    'sublocality' => Str::title($row['buyer_barangay']),
                    'locality' => Str::title($row['buyer_city']),
                    'administrative_area' => Str::title($row['buyer_province']),
                    'postal_code' => Str::title($row['buyer_zip_code'] ?? ''),
                    'sorting_code' => null,
                    'country' => 'PH',

                    'block' =>  Str::title($row['buyer_block']),
                    'lot' =>  Str::title($row['buyer_lot'] ?? ''),
                    'unit' =>  Str::title($row['buyer_unit'] ?? ''),
                    'floor' =>  Str::title($row['buyer_floor'] ?? ''),
                    'street' =>  Str::title($row['buyer_street']),
                    'building' =>  Str::title($row['buyer_building'] ?? ''),
                    'length_of_stay' => $row['buyer_length_of_stay'] ?? '',
                ],
            ],
            'employment' =>[
                [
                    'type'=>'buyer',
                    'employment_status' => Str::title($row['buyer_employer_status']),
                    'monthly_gross_income' => (string) $row['buyer_salary_gross_income'],
                    'current_position' => Str::title($row['buyer_position']),
                    'employment_type' => Str::title($row['buyer_employer_type']),
                    'years_in_service' => Str::title($row['buyer_years_in_service']),
                    'salary_range' => $row['buyer_salary_range'],
                    'department_name' => $row['department_name'],
                    'employer' => [
                        'name' => Str::title($row['buyer_employer_name']),
                        'industry' => Str::title($row['buyer_industry'] ?? ''),
                        'type' => Str::title($row['buyer_employer_type']),
                        'status' => Str::title($row['buyer_employer_status'] ?? ''),
                        'year_established' => Str::title($row['buyer_employer_year_established']),
                        'total_number_of_employees' => $row['buyer_employer_total_number_of_employees'],
                        'email' => $row['buyer_employer_email'] ?? '',
                        'nationality' => 'PH',
                        'contact_no' => (string) $row['buyer_employer_contact_number'],
                        'address' => [
                            'type' => 'work',
                            'ownership' => 'N/A',
                            'full_address' => null,
                            'address1' => Str::title($row['buyer_place_of_work_1_(city_of_employer)'] ?? ''),
                            'address2' => Str::title($row['buyer_place_of_work_2_(province_of_employer)'] ?? ''),
                            'sublocality' => Str::title($row['buyer_employer_barangay'] ?? ''),
                            'locality' => Str::title($row['buyer_employer_city']),
                            'administrative_area' => Str::title($row['buyer_employer_province']),
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
                        'tin' => (string) $row['buyer_tax_identification_number'],
                        'sss' => (string) $row['buyer_sss_gsis_number'], //TODO: process sss or gsis
                        'pagibig' => (string) $row['buyer_pag_ibig_number'],
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
                        'tin' => (string) ($row['spouse_tax_identification_number'] ?? ''),
                        'sss' => (string) ($row['spouse_sss_gsis_number'] ?? ''), //TODO: process sss or gsis
                        'pagibig' => (string) ($row['spouse_pag_ibig_number'] ?? ''),
                    ],
                ],
            ],
            'order'=>[
                //additional for gnc 7-15-2024

                'sku' => Str::title($row['sku'] ?? ''),
                'seller_commission_code' => Str::title($row['seller_commission_code'] ?? ''),
                'property_code' => Str::title($row['property_code']),

                'company_name' => Str::title($row['company_name']),
                'project_name' => Str::title($row['project_name']),
                'project_code' => Str::title($row['project_code']),
                'property_name' => Str::title($row['property_name']??$row['property_code']),
                'phase' => $row['phase'],
                'block' => $row['block'],
                'lot' => $row['lot'],
                'lot_area' => $row['lot_area'],
                'floor_area' => $row['floor_area'],
                'tcp' => $row['total_contract_price']??$row['tcp'],
                'loan_term' => $row['bp2_terms']??$row['bp1_terms'],
                'loan_interest_rate' =>$row['bp2_interest_rate']??$row['bp1_interest_rate'],
                'tct_no' => $row['transfer_certificate_of_title'] ?? '',

                'project_location' => Str::title($row['project_location'] ?? ''),
                'project_address' => Str::title($row['project_address'] ?? ''),
                'mrif_fee' => $row['mrif_fee'] ?? '',
                'reservation_rate' => $row['reservation_rate_processing_fee'] ?? '',

                'class_field' => $row['class_field'],
                'segment_field' => $row['segment_field'],
                'rebooked_id_form' => $row['rebooked_id_form'] ?? '',
                'buyer_action_form_number' => $row['buyer_action_form_number'] ?? '',
                'buyer_action_form_date' => $row['buyer_action_form_date'] ?? '',
                'cancellation_type' => $row['cancellation_type'],
                'cancellation_reason' => $row['cancellation_reason'],
                'cancellation_remarks' => $row['cancellation_remarks'],

                'unit_type' => $row['unit_type'],
                'unit_type_interior' => $row['unit_type_interior'],
                'house_color' => $row['house_color'],
                'construction_status' => $row['construction_status'],
                'transaction_reference' => $row['transaction_reference'],
                'reservation_date' => $row['reservation_date'],
                'circular_number' => $row['circular_number'] ?? '',

                'date_created'=> $row['date_created'],
                'ra_date'=> $row['ra_date'],
                'date_approved'=>$row['date_approved'],
                'date_expiration'=>$row['date_expiration'],
                'os_month'=>$row['os_month'],
                'due_date'=>$row['due_date'],
                'total_payments_made'=>$row['total_payments_made'],
                'transaction_status'=>$row['transaction_status'],
                'staging_status'=>$row['staging_status'],
                'period_id'=>$row['period_id'],
                'date_closed'=>$row['date_closed'],
                'closed_reason'=>$row['closed_reason'],
                'date_cancellation'=>$row['date_cancellation'],

                'seller'=>[
                    'unit'=>$row['selling_unit'],
                    'id'=>$row['seller_id'],
                    'name'=>$row['seller_name'],
                    'superior'=>$row['seller_superior'],
                    'team_head'=>$row['seller_superior'],
                    'chief_seller_officer'=>$row['chief_seller_officer'] ?? '',
                    'deputy_chief_seller_officer'=>$row['deputy_chief_seller_officer'] ?? '',
                    'type'=>$row['seller_type'],
                    'reference_no'=>$row['seller_reference_no'] ?? '',
                ],
                'payment_scheme'=>[
                    'scheme'=>$row['payment_scheme'],
                    'method'=>$row['payment_method_name'],
                    'collectible_price'=>$row['collectible_price'],
                    'commissionable_amount'=>$row['commissionable_amount'],
                    'evat_percentage'=>$row['evat_percentage'],
                    'evat_amount'=>$row['evat_amount'],
                    'net_total_contract_price'=>$row['net_total_contract_price'],
                    'total_contract_price'=>$row['total_contract_price'],
                    'payments'=>[
                        [
                            'type'=>'processing_fee',
                            'amount_paid'=>$row['pf_amount_paid'],
                            'date'=>$row['pf_payment_date'],
                            'reference_number'=>$row['pf_payment_reference_number'],
                        ],
                        [
                            'type'=>'home_utility_connection_fee',
                            'amount_paid'=>$row['hucf_amount_paid'],
                            'date'=>$row['hucf_payment_date'],
                            'reference_number'=>$row['hucf_payment_reference_number'],
                        ],
                        [
                            'type'=>'balance',
                            'amount_paid'=>$row['balance_payment_amount_paid'],
                            'date'=>$row['balance_payment_date'],
                            'reference_number'=>$row['balance_payment_reference_number'],
                        ],
                        [
                            'type'=>'equity',
                            'amount_paid'=>$row['equity_payment_amount_paid'],
                            'date'=>$row['equity_payment_date'],
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
                            'amount'=>$row['mrif_fee'],
                        ],
                        [
                            'name'=>'rental',
                            'amount'=>$row['rental_fee'],
                        ],
                    ],
                    'payment_remarks'=>$row['payment_remarks'],
                    'transaction_remarks'=>$row['transaction_remarks'],
                    'discount_rate'=>$row['discount_rate'],
                    'conditional_discount'=>$row['conditional_discount'],
                    'transaction_sub_status'=>$row['transaction_sub_status'],

                ]


            ],






        ];

//        dd($attribs);

        $contact = app(PersistContactAction::class)->run($attribs);

//        dd($contact);

        return $contact;
    }

    /**
     * @param array $headerRow
     * @return array
     */
    public function formatHeaderRow(array $headerRow): array
    {
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
                'buyer_sss_gsis_number' => 'buyer_sss_gsis_number',
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
                default  => $heading,
            };
        }, $headerRow);
    }


    public function headingRow(): int
    {
        return 6;
    }
}
