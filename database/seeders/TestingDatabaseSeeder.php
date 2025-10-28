<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\BlockUser;
use App\Models\BlogCategory;
use App\Models\ExpenseType;
use App\Models\FreelancerPrivateJob;
use App\Models\JobAction;
use App\Models\JobPost;
use App\Models\PrivateUser;
use App\Models\PrivateUserJobAction;
use App\Models\SiteTown;
use App\Models\UserAclPackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestingDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //\App\Models\FreelancerPrivateJob::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            CoreConfigDataSeeder::class,
            PropertyValueScriptDataSeeder::class, 
            UserAclDataSeeder::class,
            UserAclRoleSeeder::class,
            UserAclProfessionSeeder::class,
            UserQuestionSeeder::class,
            PkgPrivilegeInfoSeeder::class,
            SiteTownSeeder::class,
            UserAclPackageResourceSeeder::class,
            UserAclPackageSeeder::class,
            BlogCategorySeeder::class,
            ExpenseTypeSeeder::class,
            FinanceTaxRecordSeeder::class,
            FinanceNiTaxRecordSeeder::class,
            FeedbackQuestionSeeder::class,
            IndustryNewsSeeder::class,
            BlogSeeder::class,
            UserSeeder::class,
        ]);
    }
}
