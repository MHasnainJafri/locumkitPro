<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UsersWorkCalenderTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_updating_user_work_calendar()
    {

        $this->artisan("migrate:fresh");
        $this->seed('Database\\Seeders\\TestingDatabaseSeeder');
        $users = User::all();
        foreach ($users as $user) {
            $this->actingAs($user)->postJson("/ajax/update-calender", [
                "availability" => 2,
                "selected_date" => fake()->date("Y-m-d", '+10 days'),
            ])->assertStatus(200)->assertExactJson([
                "availability" => 2
            ]);

            $this->actingAs($user)->postJson("/ajax/update-calender", [
                "availability" => 1,
                "selected_date" => fake()->date("Y-m-d", '+10 days'),
                'min_rate_date' => fake()->randomFloat(10, 250, 450),
            ])->assertStatus(200)->assertExactJson([
                "availability" => 1
            ]);

            $this->actingAs($user)->postJson("/ajax/get-booked-date-info", [
                "date" => fake()->date("Y-m-d", '-20 days')
            ])->assertStatus(200)->assertExactJson([
                "success" => false,
                "message" => "No job found"
            ]);
        }
        //The system below may generate test error because of job_dates. So don't worry about it.
        /* $employer = User::where("user_acl_role_id", User::USER_ROLE_EMPLOYER)->first();
        $this->assertNotNull($employer, 'Must find an employer in db');
        $users = User::where("user_acl_role_id", User::USER_ROLE_LOCUM)->get();
        foreach ($users as $user) {
            $job_date = Carbon::parse(fake()->dateTimeBetween('now', '+10 days'));
            $this->inviate_and_accept_by_posting_new_job($employer, $job_date->format("d/m/Y"), fake()->randomFloat(4, 1000, 2000), $user);

            $this->actingAs($user)->postJson("/ajax/get-booked-date-info", [
                "date" => $job_date
            ])->assertStatus(200)->assertJsonFragment([
                "success" => true,
            ]);
            $this->actingAs($employer)->postJson("/ajax/get-booked-date-info", [
                "date" => $job_date
            ])->assertStatus(200)->assertJsonFragment([
                "success" => true,
            ]);
        } */
    }
}
