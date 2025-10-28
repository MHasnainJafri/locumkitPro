<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
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
            DemoDataSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
