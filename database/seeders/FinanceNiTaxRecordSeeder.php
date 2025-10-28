<?php

namespace Database\Seeders;

use App\Models\FinanceNiTaxRecord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class FinanceNiTaxRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        FinanceNiTaxRecord::truncate();
        Schema::enableForeignKeyConstraints();
        $finance_ni_tax_records = array(
            array('finance_year' => '2016-2017', 'c4_min_ammount_1' => '8000', 'c4_min_ammount_tax_1' => '0', 'c4_min_ammount_2' => '45000', 'c4_min_ammount_tax_2' => '9', 'c4_min_ammount_3' => '45001', 'c4_min_ammount_tax_3' => '2', 'c2_min_amount' => '6025', 'c2_tax' => '148.2', 'created_at' => '2018-01-05 14:43:12'),
            array('finance_year' => '2017-2018', 'c4_min_ammount_1' => '8500', 'c4_min_ammount_tax_1' => '0', 'c4_min_ammount_2' => '40000', 'c4_min_ammount_tax_2' => '9', 'c4_min_ammount_3' => '40001', 'c4_min_ammount_tax_3' => '2', 'c2_min_amount' => '6025', 'c2_tax' => '148.2', 'created_at' => '2018-01-05 14:43:54'),
            array('finance_year' => '2018-2019', 'c4_min_ammount_1' => '8424', 'c4_min_ammount_tax_1' => '0', 'c4_min_ammount_2' => '46350', 'c4_min_ammount_tax_2' => '12', 'c4_min_ammount_3' => '46351', 'c4_min_ammount_tax_3' => '2', 'c2_min_amount' => '6205', 'c2_tax' => '153.4', 'created_at' => '2018-12-12 17:47:04'),
            array('finance_year' => '2019-2020', 'c4_min_ammount_1' => '8632', 'c4_min_ammount_tax_1' => '0', 'c4_min_ammount_2' => '50000', 'c4_min_ammount_tax_2' => '12', 'c4_min_ammount_3' => '50001', 'c4_min_ammount_tax_3' => '2', 'c2_min_amount' => '6365', 'c2_tax' => '156', 'created_at' => '2020-02-13 23:48:11'),
            array('finance_year' => '2020-2021', 'c4_min_ammount_1' => '9500', 'c4_min_ammount_tax_1' => '0', 'c4_min_ammount_2' => '50000', 'c4_min_ammount_tax_2' => '12', 'c4_min_ammount_3' => '50001', 'c4_min_ammount_tax_3' => '2', 'c2_min_amount' => '6475', 'c2_tax' => '159', 'created_at' => '2020-04-10 23:47:47'),
            array('finance_year' => '2021-2022', 'c4_min_ammount_1' => '9658', 'c4_min_ammount_tax_1' => '0', 'c4_min_ammount_2' => '50270', 'c4_min_ammount_tax_2' => '9', 'c4_min_ammount_3' => '50271', 'c4_min_ammount_tax_3' => '2', 'c2_min_amount' => '6515', 'c2_tax' => '3.05', 'created_at' => '2022-08-13 14:26:35'),
            array('finance_year' => '2022-2023', 'c4_min_ammount_1' => '11908', 'c4_min_ammount_tax_1' => '0', 'c4_min_ammount_2' => '50270', 'c4_min_ammount_tax_2' => '9', 'c4_min_ammount_3' => '50271', 'c4_min_ammount_tax_3' => '2', 'c2_min_amount' => '6725', 'c2_tax' => '3.15', 'created_at' => '2022-08-13 14:27:15')
        );

        FinanceNiTaxRecord::insert($finance_ni_tax_records);
    }
}