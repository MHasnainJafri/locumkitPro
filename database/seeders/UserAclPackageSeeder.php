<?php

namespace Database\Seeders;

use App\Models\UserAclPackage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UserAclPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        UserAclPackage::truncate();
        Schema::enableForeignKeyConstraints();

        $packages = [
            ['name' => 'Gold', 'price' => '550', 'description' => 'Gold Package', 'user_acl_package_resources_ids_list' => json_encode([1, 2, 3, 4, 5, 6, 7])],
            ['name' => 'Silver', 'price' => '450', 'description' => 'Silver Package', 'user_acl_package_resources_ids_list' => json_encode([1, 2, 3, 4, 5, 6, 7])],
            ['name' => 'Bronze', 'price' => '96', 'description' => 'Bronze Package', 'user_acl_package_resources_ids_list' => json_encode([1, 2, 3, 4, 5])],
            ['name' => 'Free Subscription', 'price' => '0', 'description' => '3 months trial membership', 'user_acl_package_resources_ids_list' => json_encode([1, 2, 3, 4, 5, 6, 7])],
        ];

        UserAclPackage::insert($packages);
    }
}