<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserSeeder::create_new_admin();
        UserSeeder::create_new_freelancer(300, "noumanhabib521@gmail.com");
        UserSeeder::create_new_employer("noumanhabib112233@gmail.com");

        $freelancer = UserSeeder::create_new_freelancer(350, 'freelancer@gmail.com');
        $employer = UserSeeder::create_new_employer("employer@gmail.com");
    }
}
