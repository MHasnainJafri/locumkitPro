<?php

namespace Database\Seeders;

use App\Models\UserAclPackageResource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UserAclPackageResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        UserAclPackageResource::truncate();
        Schema::enableForeignKeyConstraints();

        $package_resources = [
            ['resource_key' => 'job_invitation', 'resource_value' => 'Get Job Invitation'],
            ['resource_key' => 'job_freeze', 'resource_value' => 'Freeze Jobs'],
            ['resource_key' => 'add_private_job', 'resource_value' => 'Add private job'],
            ['resource_key' => 'feedback', 'resource_value' => 'Leave Feedback'],
            ['resource_key' => 'job_reminders', 'resource_value' => 'Job Reminders'],
            ['resource_key' => 'finance', 'resource_value' => 'Finance '],
            ['resource_key' => 'finance_reminders', 'resource_value' => 'Finance Reminders'],
        ];

        UserAclPackageResource::insert($package_resources);
    }
}