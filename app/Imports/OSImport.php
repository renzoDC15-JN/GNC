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

        $attribs  = [
            //
            'reference_code'=> Str::title($row['buyer_last_name']).'-'.$row['buyer_birthday'],
            'spouse' => [
                'first_name' => $this->faker->firstName(),
                'middle_name' => $this->faker->lastName(),
                'last_name' => $this->faker->lastName(),
                'civil_status' => $this->faker->randomElement(['Single', 'Married', 'Annuled/Divorced', 'Legally Seperated', 'Widow/er']),
                'sex' => $this->faker->randomElement(['Male', 'Female']),
                'nationality' => 'Filipino',
                'date_of_birth' => $this->faker->date(),
                'email' => $this->faker->email(),
                'mobile' => $this->faker->phoneNumber(),
            ],
            //
            'first_name' => Str::title($row['buyer_first_name']),
            'middle_name' => Str::title($row['buyer_middle_name']) ?: 'Missing',
            'last_name' => Str::title($row['buyer_last_name']),
            'civil_status' => Str::title($row['buyer_civil_status']),
            'sex' => Str::title($row['buyer_gender']),
            'nationality' => Str::title($row['buyer_nationality']),
            'date_of_birth' => Carbon::createFromDate(Date::excelToDateTimeObject($row['buyer_birthday'])), //TODO: update this
            'email' => strtolower($row['buyer_principal_email']),
            'mobile' => (string) $row['buyer_primary_contact_number'], //TODO: update this
            'addresses' => [
                [
                    'type' => 'primary',
                    'ownership' => Str::title($row['buyer_ownership_type']),
                    'full_address' => null,
                    'address1' => Str::title($row['buyer_street']),
                    'address2' => null,
                    'sublocality' => Str::title($row['buyer_barangay']),
                    'locality' => Str::title($row['buyer_city']),
                    'administrative_area' => Str::title($row['buyer_province']),
                    'postal_code' => null,
                    'sorting_code' => null,
                    'country' => 'PH',
                ],
            ],
            'employment' => [
                'employment_status' => Str::title($row['buyer_employer_status']),
                'monthly_gross_income' => (string) $row['buyer_salary_gross_income'],
                'current_position' => Str::title($row['buyer_position']),
                'employment_type' => Str::title($row['buyer_employer_type']),
                'employer' => [
                    'name' => Str::title($row['buyer_employer_name']),
                    'industry' => Str::title($row['industry']),
                    'nationality' => 'PH',
                    'contact_no' => (string) $row['buyer_employer_contact_number'],
                    'address' => [
                        'type' => 'work',
                        'ownership' => 'N/A',
                        'full_address' => Str::title($row['buyer_employer_address']),
                        'address1' => Str::title($row['buyer_street']),
                        'address2' => null,
                        'sublocality' => null,
                        'locality' => Str::title($row['buyer_employer_city']),
                        'administrative_area' => Str::title($row['buyer_employer_province']),
                        'postal_code' => '1111', //TODO: update this
                        'sorting_code' => null,
                        'country' => 'PH',
                    ],
                ],
                'id' => [
                    'tin' => (string) $row['buyer_tax_identification_number'],
                    'sss' => (string) $row['buyer_sss_gsis_number'], //TODO: process sss or gsis
                    'pagibig' => (string) $row['buyer_pag_ibig_number'],
                ],
            ],
            //additional for gnc 7-15-2024
            'company_name' => Str::title($row['company_name']),
            'project_name' => Str::title($row['project_name']),
            'project_code' => Str::title($row['project_code']),
            'property_name' => Str::title($row['property_name']),
            'phase' => Str::title($row['phase']),
            'block' => Str::title($row['block']),
            'lot' => Str::title($row['lot']),
            'lot_area' => Str::title($row['lot_area']),
            'floor_area' => Str::title($row['floor_area']),
            'tcp' => Str::title($row['tcp']),
            'loan_term' => Str::title($row['loan_term']),
            'loan_interest_rate' => Str::title($row['loan_interest_rate']),
            'tct_no' => Str::title($row['tct_no']),

        ];

//        dd($attribs);

        $contact = app(PersistContactAction::class)->run($attribs);
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
                'property_name' => 'property_code',
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

                default  => $heading,
            };
        }, $headerRow);
    }


    public function headingRow(): int
    {
        return 6;
    }
}
