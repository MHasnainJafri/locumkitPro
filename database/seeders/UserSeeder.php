<?php

namespace Database\Seeders;

use App\Models\EmployerStoreList;
use App\Models\MobileNotification;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserExtraInfo;
use App\Models\UserPackageDetail;
use App\Models\UserPaymentInfo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        UserExtraInfo::truncate();
        User::truncate();
        UserPackageDetail::truncate();
        UserPaymentInfo::truncate();
        EmployerStoreList::truncate();
        UserAnswer::truncate();
        MobileNotification::truncate();
        Schema::enableForeignKeyConstraints();
        //create a new freelancer
        $this->create_new_freelancer(150, "noumanhabib521@gmail.com");
        $this->create_new_employer("noumanhabib332211@gmail.com");
        $this->create_new_freelancer(250);
        $this->create_new_freelancer(700);
        $this->create_new_freelancer(450);
        $this->create_new_freelancer(300);
        $this->create_new_freelancer(350);
        $this->create_new_freelancer(380);
        $this->create_new_freelancer(420.50);
        //Don't create new users other test may fails like `PrivateUserTest`
        //$this->create_new_admin();
    }

    public static function create_new_freelancer(float $rate = 250, string $email = null)
    {
        $minimum_rate = json_encode([
            'Monday'     => $rate,
            'Tuesday'     => $rate,
            'Wednesday' => $rate,
            'Thursday'     => $rate,
            'Friday'     => $rate,
            'Saturday'     => $rate,
            'Sunday'     => $rate,
        ]);

        $user = User::create([
            'firstname' => fake()->firstName("male"),
            'lastname' => fake()->lastName("male"),
            'email' => $email ? $email : fake()->safeEmail(),
            'login' => fake()->userName(),
            'password' => Hash::make("password"),
            'active' => User::USER_STATUS_ACTIVE,
            'user_acl_role_id' => User::USER_ROLE_LOCUM,
            'user_acl_profession_id' => User::USER_PROFESSION_DEFAULT,
            'user_acl_package_id' => 4,
            "email_verified_at" => now()
        ]);

        UserExtraInfo::create([
            "user_id" => $user->id,
            "aoc_id" => "OPL 11-11111/A",
            "gender" => "male",
            "dob" => null,
            "mobile" => "0123456789",
            "address" => "London, UK, Street#123",
            "city" => "London Apprentice Cornwall",
            "zip" => "PL26 7A",
            "telephone" => "0123456789",
            "company" => "My test company",
            "profile_image" => null,
            "max_distance" => 10,
            "minimum_rate" => $minimum_rate,
            "site_town_ids" => '["25848","24350","25045","33703","42682","29520","39555","42735","20044","26501","39195","26539","39415","42761","13138","42928","32956","29184","32357","8308","42667","36509","20825","19804","42842","21156","4610","3391","7502","23872","7609","36799","39366","20236","22490","42687","33724"]',
            "cet" => "123",
            "goc" => "D-123456",
            "aop" => "12345",
            "inshurance_company" => null,
            "inshurance_no" => null,
            "inshurance_renewal_date" => null,
            "store_type_name" => "Boots"
        ]);

        $pkg_active_date = now()->format("Y-m-d");
        $pkg_expire_date = now()->addDays(90)->format("Y-m-d");

        UserPackageDetail::create([
            "user_id" => $user->id,
            "user_acl_package_id" => 4,
            "package_active_date" => $pkg_active_date,
            "package_expire_date" => $pkg_expire_date,
        ]);

        UserPaymentInfo::create([
            "user_id" => $user->id,
            "user_acl_package_id" => 4,
            "payment_type" => "FREE",
            "price" => 0,
            "payment_status" => 1,
        ]);


        $user_answers_data = [
            [
                "user_id" => $user->id,
                "user_question_id" => 2,
                "type_value" => "2-5",
            ],
            [
                "user_id" => $user->id,
                "user_question_id" => 3,
                "type_value" => "16-20",
            ],
            [
                "user_id" => $user->id,
                "user_question_id" => 4,
                "type_value" => "16-20",
            ],
            [
                "user_id" => $user->id,
                "user_question_id" => 5,
                "type_value" => "Yes",
            ],
            [
                "user_id" => $user->id,
                "user_question_id" => 23,
                "type_value" => '["English","Urdu"]',
            ],
            [
                "user_id" => $user->id,
                "user_question_id" => 24,
                "type_value" => '["Basic Eye Test"]',
            ],
            [
                "user_id" => $user->id,
                "user_question_id" => 28,
                "type_value" => '["Autorefractor","OCT"]',
            ],
            [
                "user_id" => $user->id,
                "user_question_id" => 31,
                "type_value" => '["Basic IT usage"]',
            ]
        ];

        UserAnswer::insert($user_answers_data);

        return $user;
    }

    public static function create_new_admin(string|null $email = null)
    {
        $user = User::create([
            'firstname' => 'Site',
            'lastname' => 'Admin',
            'email' => $email ? $email : 'admin@locumkit.com',
            'login' => 'admin',
            'password' => Hash::make("password"),
            'active' => User::USER_STATUS_ACTIVE,
            'user_acl_role_id' => User::USER_ROLE_ADMIN,
            'user_acl_profession_id' => User::USER_PROFESSION_DEFAULT,
            'user_acl_package_id' => 4,
            "email_verified_at" => now()
        ]);
    }

    public static function create_new_employer($email = null)
    {
        $user = User::create([
            'firstname' => fake()->firstName("male"),
            'lastname' => fake()->lastName("male"),
            'email' => $email ? $email : fake()->safeEmail(),
            'login' => fake()->userName(),
            'password' => Hash::make("password"),
            'active' => User::USER_STATUS_ACTIVE,
            'user_acl_role_id' => User::USER_ROLE_EMPLOYER,
            'user_acl_profession_id' => User::USER_PROFESSION_DEFAULT,
            'user_acl_package_id' => 4,
            "email_verified_at" => now()
        ]);

        $emp_store_result = [[
            'employer_id' => $user->id,
            'store_name'    => fake()->company(),
            'store_address' => "Carpalla, Saint Austell, Cornwall, PL26 7TY, UK",
            'store_region'  => "Carpalla Cornwall",
            'store_zip'     => "PL26 7TY",
            'store_start_time' => '{"Monday":"09:00","Tuesday":"09:00","Wednesday":"09:00","Thursday":"09:00","Friday":"09:00","Saturday":"09:00","Sunday":"09:00"}',
            'store_end_time'   => '{"Monday":"17:30","Tuesday":"17:30","Wednesday":"17:30","Thursday":"17:30","Friday":"17:30","Saturday":"17:30","Sunday":"17:30"}',
            'store_lunch_time' => '{"Monday":"20","Tuesday":"20","Wednesday":"30","Thursday":"20","Friday":"00","Saturday":"00","Sunday":"00"}',
            'created_at' => now(),
            'updated_at' => now()
        ]];

        UserExtraInfo::create([
            "user_id" => $user->id,
            "gender" => "male",
            "dob" => null,
            "mobile" => "0123456789",
            "address" => "Beasly Ait Lane, Fordbridge Rd, Sunbury-on-Thames TW16 6AS, United Kingdom",
            "city" => "Sunbury Surrey",
            "zip" => "TW16 6AS",
            "telephone" => "0123456789",
            "company" => fake()->company(),
            "profile_image" => null,
        ]);
        if (sizeof($emp_store_result) > 0) {
            EmployerStoreList::insert($emp_store_result);
        }


        $employer_answers_data = [
            [
                "user_id" => $user->id,
                "user_question_id" => 2,
                "type_value" => '0-1',
            ],
            [
                "user_id" => $user->id,
                "user_question_id" => 3,
                "type_value" => '10-15',
            ],
            [
                "user_id" => $user->id,
                "user_question_id" => 4,
                "type_value" => '10-15',
            ],
            [
                "user_id" => $user->id,
                "user_question_id" => 5,
                "type_value" => 'Yes',
            ],
            [
                "user_id" => $user->id,
                "user_question_id" => 23,
                "type_value" => '["English","Urdu", "Chinese"]',
            ],
            [
                "user_id" => $user->id,
                "user_question_id" => 24,
                "type_value" => '["Basic Eye Test", "OCT"]',
            ],
            [
                "user_id" => $user->id,
                "user_question_id" => 28,
                "type_value" => '["Autorefractor","OCT", "Slit lamp"]',
            ],
            [
                "user_id" => $user->id,
                "user_question_id" => 31,
                "type_value" => '["Socrates"]',
            ],
        ];

        UserAnswer::insert($employer_answers_data);

        return $user;
    }
}
