<?php

namespace App\Providers;

use App\Models\Maintenance\Approvers;
use App\Policies\ContactPolicy;
use App\Policies\Maintenance\ApproversPolicy;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Homeful\Contacts\Models\Contact;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Approvers::class, ApproversPolicy::class);
        Gate::policy(Contact::class, ContactPolicy::class);

        FilamentAsset::register([
            Js::make('openlinknewtab', __DIR__ . '/../../resources/js/openlinknewtab.js'),
        ]);

        HeadingRowFormatter::extend('cornerstone-os-report-1', function($value, $key) {
            $heading = Str::snake(Str::camel($value));

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
                'buyer_tax_identifaction_number' => 'buyer_tax_identification_number', // TODO: Wrong spelling on excel
                'buyer_s_s_s/_g_s_i_s_number' => 'buyer_sss_gsis_number',
                'client_i_d(_spouse)' => 'client_id_spouse',
                'client_i_d(_a_i_f)' => 'client_id_aif',
                'a_i_f_name' => 'aif_name',
                'a_i_f_address' => 'aif_address',
                'client_i_d(_co_borrower)' => 'client_id_co_borrower',
                'buyer_place_of_work1(_city_of_employer)' => 'buyer_place_of_employer_city',
                'buyer_place_of_work2(_province_of_employer)' => 'buyer_place_of_employer_province',
                'buyer_salary/_gross_income' => 'buyer_salary_gross_income',
                'seller_i_d' => 'seller_id',
                'c_s_o/_d_c_s_o' => 'chief_seller_officer',
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
                'circular_no.(312/379)' => 'circular_no_312_379',
                'l_t_v_r_slug' => 'ltvr_slug',
                'p_f_amount_paid' => 'pf_amount_paid',
                'p_f_payment_reference_number' => 'pf_payment_reference_number',
                'p_f_payment_date' => 'pf_payment_date',
                'h_u_c_f_amount_paid' => 'hucf_amount_paid',
                'h_u_c_f_payment_reference_number' => 'hucf_payment_reference_number',
                'h_u_c_f_payment_date' => 'hucf_payment_date',
                'total_contract_price'=>'tcp',
                'transfer_certificate_of_title'=>'tct_no',
                't_c_t_no'=>'tct_no',
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
                'industry' => 'industry',
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
                'spouse_age' => 'age',
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
                'property_type' => 'property_type',
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
                'aif_fax' => 'fax',
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
                'company_name' => 'company_name',
                'project_address' => 'project_address',
                'executive_possition' => 'exec_position', // TODO: wrong spelling in excel
                // 'executive_position' => 'exec_position',
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
                'sales_team_head' => 'sales_team_head',
                'employment_status' => 'employment_status',
                'monthly_gross_income' => 'monthly_gross_income',
                'buyer_employer_address' => 'buyer_employer_address',
                'cancellation_reason2' => 'cancellation_reason2',
                'total_selling_price' => 'total_selling_price',
                'repricing_period_slug' => 'repricing_period_slug',
                'tcp_in_words' => 'tcp_in_words',
                'interest_in_words' => 'interest_in_words',
                'interest' => 'interest',
                'logo' => 'logo',
                'loan_period_months' => 'loan_period_months',
                'aif_relationship_to_buyer' => 'relationship_to_buyer',
                'aif_passport' => 'passport',
                'aif_date_issued' => 'date_issued',
                'aif_place_issued' => 'place_issued',
                'page' => 'page',
                'lot_area_in_words' => 'lot_area_in_words',
                'chief_sales_officer_representative' => 'exec_signatories',
                'exec_tin_no' => 'exec_tin_no',
                'loan_terms_in_word' => 'loan_terms_in_word',
                'company_tin' => 'company_tin',
                'y' => 'yes_for_faq_solaris_project',
                'n' => 'n_for_faq_solaris_project',
                'loan_value' => 'loan_value_after_downpayment',
                'company_address' => 'company_address',
                'buyer_birthday' => 'date_of_birth',
                'due_date' => 'due_date',
                'date_closed' => 'date_closed',
                'executie_tin_no' => 'exec_tin_no',
                'deed_of_registry_location' => 'registry_of_deeds_address',
                'co-borrower_address' => 'co_borrower_address',
                'co-borrower_civil_status' => 'co_borrower_civil_status',
                'co-borrower_nationality' => 'co_borrower_nationality',
                'co-borrower_tin' => 'co_borrower_tin',

                default  => $heading,
            };
        });
    }
}
