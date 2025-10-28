<?php

namespace Database\Seeders;

use App\Models\UserAclRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UserAclRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        UserAclRole::truncate();
        Schema::enableForeignKeyConstraints();

        $roles = [
            [
                "id" => 1,
                "name" => "Administrator",
                "description" => null,
                "is_public" => false
            ],
            [
                "id" => 2,
                "name" => "Locum",
                "description" => "Locum description",
                "is_public" => true
            ],
            [
                "id" => 3,
                "name" => "Employer",
                "description" => "Employer description",
                "is_public" => true
            ],
            [
                "id" => 4,
                "name" => "Config manager",
                "description" => "To manage config section of the website",
                "is_public" => false
            ],
            [
                "id" => 5,
                "name" => "User System Manager",
                "description" => "To manage all about user sysrtem",
                "is_public" => false
            ],

            [
                "id" => 6,
                "name" => "Feedback Manager",
                "description" => "To manage user feedback",
                "is_public" => false
            ],

            [
                "id" => 8,
                "name" => "Question Manager",
                "description" => "To manage all dynamic question",
                "is_public" => false
            ],

            [
                "id" => 9,
                "name" => "Report Manager",
                "description" => "To manage report",
                "is_public" => false
            ],

            [
                "id" => 10,
                "name" => "Social Media Manager",
                "description" => "To manage all social advertisement ",
                "is_public" => false
            ],

            [
                "id" => 11,
                "name" => "Finance Balance Sheet Manager",
                "description" => "To manage Balance sheet of user ",
                "is_public" => false
            ],

            [
                "id" => 12,
                "name" => "Finance Profit Loss Manager",
                "description" => "To manage profit and loss stetment",
                "is_public" => false
            ],

            [
                "id" => 13,
                "name" => "Finance All Transaction Manager",
                "description" => "To manage all transaction of individual user",
                "is_public" => false
            ],

            [
                "id" => 14,
                "name" => "Writer",
                "description" => "Writer",
                "is_public" => false
            ],

            [
                "id" => 15,
                "name" => "Sub Admin",
                "description" => "Admin but not allow to create new user",
                "is_public" => false
            ],
        ];

        UserAclRole::insert($roles);
    }
}