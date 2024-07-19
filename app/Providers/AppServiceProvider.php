<?php

namespace App\Providers;

use App\Models\Maintenance\Approvers;
use App\Policies\ContactPolicy;
use App\Policies\Maintenance\ApproversPolicy;
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

        HeadingRowFormatter::extend('cornerstone-os-report-1', function($value, $key) {
            $heading = Str::snake(Str::camel($value));
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
        });
    }
}
