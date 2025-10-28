<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PrivateUserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_private_user_emails()
    {

        $this->artisan("migrate:fresh");
        $this->seed('Database\\Seeders\\TestingDatabaseSeeder');
        $userSeeder = new UserSeeder();
        $userSeeder->create_new_employer();
        $email = 'nouman123@gmail.com';
        $employer1 = User::find(2);
        $employer2 = User::orderBy("id", "DESC")->where("user_acl_role_id", User::USER_ROLE_EMPLOYER)->first();

        $response = $this->actingAs($employer1)->post('/employer/store-private-users', [
            'private_user_name' => [fake()->name('male')],
            'private_user_email' => [$email],
            'private_user_mobile' => [fake()->phoneNumber()],
        ]);
        $response->assertRedirect()->assertSessionHas("success");

        $response = $this->actingAs($employer2)->post('/employer/store-private-users', [
            'private_user_name' => [fake()->name('male'), fake()->name('male')],
            'private_user_email' => [fake()->safeEmail(), $email],
            'private_user_mobile' => [fake()->phoneNumber(), fake()->phoneNumber()],
        ]);
        $response->assertRedirect()->assertSessionHas("success");

        $response = $this->actingAs($employer1)->post('/employer/store-private-users', [
            'private_user_name' => [fake()->name('male'), fake()->name('male')],
            'private_user_email' => [fake()->safeEmail(), $email],
            'private_user_mobile' => [fake()->phoneNumber(), fake()->phoneNumber()],
        ]);

        $response->assertRedirect()->assertSessionHas("error", "Emails {$email}, are already present.");
    }
}
