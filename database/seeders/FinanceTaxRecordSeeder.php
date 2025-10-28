<?php

namespace Database\Seeders;

use App\Models\FinanceTaxRecord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class FinanceTaxRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        FinanceTaxRecord::truncate();
        Schema::enableForeignKeyConstraints();

        $finance_tax_records = array(
            array('finance_year' => '2018-2019', 'personal_allowance_rate' => '11850', 'personal_allowance_rate_tax' => '0', 'basic_rate' => '46350', 'basic_rate_tax' => '20', 'higher_rate' => '150000', 'higher_rate_tax' => '40', 'additional_rate' => '150001', 'additional_rate_tax' => '45', 'company_limited_tax' => '19'),
            array('finance_year' => '2016-2017', 'personal_allowance_rate' => '11500', 'personal_allowance_rate_tax' => '0', 'basic_rate' => '45000', 'basic_rate_tax' => '20', 'higher_rate' => '150000', 'higher_rate_tax' => '40', 'additional_rate' => '150001', 'additional_rate_tax' => '45', 'company_limited_tax' => '20'),
            array('finance_year' => '2017-2018', 'personal_allowance_rate' => '11500', 'personal_allowance_rate_tax' => '0', 'basic_rate' => '45000', 'basic_rate_tax' => '20', 'higher_rate' => '150000', 'higher_rate_tax' => '40', 'additional_rate' => '150001', 'additional_rate_tax' => '45', 'company_limited_tax' => '20'),
            array('finance_year' => '2015-2016', 'personal_allowance_rate' => '11500', 'personal_allowance_rate_tax' => '0', 'basic_rate' => '45000', 'basic_rate_tax' => '20', 'higher_rate' => '150000', 'higher_rate_tax' => '40', 'additional_rate' => '150001', 'additional_rate_tax' => '45', 'company_limited_tax' => '20'),
            array('finance_year' => '2019-2020', 'personal_allowance_rate' => '12500', 'personal_allowance_rate_tax' => '0', 'basic_rate' => '50000', 'basic_rate_tax' => '20', 'higher_rate' => '150000', 'higher_rate_tax' => '40', 'additional_rate' => '150001', 'additional_rate_tax' => '45', 'company_limited_tax' => '19'),
            array('finance_year' => '2020-2021', 'personal_allowance_rate' => '12500', 'personal_allowance_rate_tax' => '0', 'basic_rate' => '50000', 'basic_rate_tax' => '20', 'higher_rate' => '150000', 'higher_rate_tax' => '40', 'additional_rate' => '150001', 'additional_rate_tax' => '45', 'company_limited_tax' => '19'),
            array('finance_year' => '2021-2022', 'personal_allowance_rate' => '12570', 'personal_allowance_rate_tax' => '0', 'basic_rate' => '50270', 'basic_rate_tax' => '20', 'higher_rate' => '150000', 'higher_rate_tax' => '40', 'additional_rate' => '150001', 'additional_rate_tax' => '45', 'company_limited_tax' => '19'),
            array('finance_year' => '2022-2023', 'personal_allowance_rate' => '12570', 'personal_allowance_rate_tax' => '0', 'basic_rate' => '50270', 'basic_rate_tax' => '20', 'higher_rate' => '150000', 'higher_rate_tax' => '40', 'additional_rate' => '150001', 'additional_rate_tax' => '45', 'company_limited_tax' => '19')
        );
        FinanceTaxRecord::insert($finance_tax_records);
    }
}