<?php

namespace Database\Seeders;

use App\Models\UserAclProfession;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UserAclProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        UserAclProfession::truncate();
        Schema::enableForeignKeyConstraints();
        $professions = [
            [
                "id" => 1,
                "name" => "Dentistry",
                "description" => "Specialist in medical Dentist",
                "is_active" => false,
            ],
            [
                "id" => 3,
                "name" => "Optometry",
                "description" => "Specialist in Opticians",
                "is_active" => true,
            ],
            [
                "id" => 4,
                "name" => "Pharmacy",
                "description" => "Special in Pharmacist",
                "is_active" => false,
            ],
            [
                "id" => 8,
                "name" => "Domiciliary Opticians",
                "description" => "Outrace Opticians",
                "is_active" => false,
            ],
            [
                "id" => 9,
                "name" => "Audiologists",
                "description" => "Hearcare specialists",
                "is_active" => false,
            ],
            [
                "id" => 10,
                "name" => "Dispensing Optician / Contact lens Optician",
                "description" => "Dispensing Optician / Contact lens Optician",
                "is_active" => true,
            ]
        ];

        UserAclProfession::insert($professions);
    }
}