<?php

namespace Database\Seeders;

use App\Models\PkgPrivilegeInfo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PkgPrivilegeInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        PkgPrivilegeInfo::truncate();
        Schema::enableForeignKeyConstraints();

        $pkg_privilege_infos = array(
            array('label' => 'Get job notification', 'bronze' => '1', 'silver' => '1', 'gold' => '1', 'created_at' => now(), 'updated_at' => now()),
            array('label' => 'Accept jobs', 'bronze' => '1', 'silver' => '1', 'gold' => '1', 'created_at' => now(), 'updated_at' => now()),
            array('label' => 'Add private jobs', 'bronze' => '1', 'silver' => '1', 'gold' => '1', 'created_at' => now(), 'updated_at' => now()),
            array('label' => 'Receive job reminders', 'bronze' => '1', 'silver' => '1', 'gold' => '1', 'created_at' => now(), 'updated_at' => now()),
            array('label' => 'Diary management ', 'bronze' => '1', 'silver' => '1', 'gold' => '1', 'created_at' => now(), 'updated_at' => now()),
            array('label' => 'Receive and leave feedback', 'bronze' => '1', 'silver' => '1', 'gold' => '1', 'created_at' => now(), 'updated_at' => now()),
            array('label' => 'Industry news update', 'bronze' => '1', 'silver' => '1', 'gold' => '1', 'created_at' => now(), 'updated_at' => now()),
            array('label' => 'Record income', 'bronze' => '0', 'silver' => '1', 'gold' => '1', 'created_at' => now(), 'updated_at' => now()),
            array('label' => 'Record expenses', 'bronze' => '0', 'silver' => '1', 'gold' => '1', 'created_at' => now(), 'updated_at' => now()),
            array('label' => 'View outstanding payments', 'bronze' => '0', 'silver' => '1', 'gold' => '1', 'created_at' => now(), 'updated_at' => now()),
            array('label' => 'Get financial reminders', 'bronze' => '0', 'silver' => '1', 'gold' => '1', 'created_at' => now(), 'updated_at' => now()),
            array('label' => 'Automatic invoicing', 'bronze' => '0', 'silver' => '1', 'gold' => '1', 'created_at' => now(), 'updated_at' => now()),
            array('label' => 'Live management accounting', 'bronze' => '0', 'silver' => '1', 'gold' => '1', 'created_at' => now(), 'updated_at' => now()),
            array('label' => 'Statutory accounts compliance (not offered in free period) ', 'bronze' => '0', 'silver' => '0', 'gold' => '1', 'created_at' => now(), 'updated_at' => now()),
            array('label' => 'Statutory tax return compliance (not offered in free period) ', 'bronze' => '0', 'silver' => '0', 'gold' => '1', 'created_at' => now(), 'updated_at' => now()),
            array('label' => 'Statutory annual return compliance (not offered in free period) ', 'bronze' => '0', 'silver' => '0', 'gold' => '1', 'created_at' => now(), 'updated_at' => now())
        );

        PkgPrivilegeInfo::insert($pkg_privilege_infos);
    }
}