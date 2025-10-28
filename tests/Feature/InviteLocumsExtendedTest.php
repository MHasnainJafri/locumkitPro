<?php

namespace Tests\Feature;

use App\Models\EmployerStoreList;
use App\Models\JobPost;
use App\Models\MobileNotification;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserExtraInfo;
use App\Models\UserPackageDetail;
use App\Models\UserPaymentInfo;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InviteLocumsExtendedTest extends TestCase
{
    /**
     * @testdox Trying to create different employers & freelancer and then creating and sending jobs in parallel to test system accuracy.
     */
    public function test_example()
    {

        $this->artisan("migrate:fresh");
        $this->seed('Database\\Seeders\\TestingDatabaseSeeder');
        //DB Setup
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
        UserSeeder::create_new_freelancer(250, "noumanhabib521@gmail.com");
        UserSeeder::create_new_employer();
        UserSeeder::create_new_employer();
        for ($i = 1; $i < 10; $i++) {
            UserSeeder::create_new_freelancer(random_int(200, 500));
        }
        //End --DB Setup

        $employer1 = User::find(2);
        $employer2 = User::find(3);
        for ($i = 1; $i <= 10; $i++) {
            $this->create_job_and_send_invitation($employer1, $i);
            $this->create_job_and_send_invitation($employer2, $i);
        }
        for ($i = 1; $i <= 5; $i++) {
            $this->create_job_and_send_invitation($employer2, $i + 10);
        }
        for ($i = 1; $i <= 5; $i++) {
            $this->create_job_and_send_invitation($employer1, $i + 10);
        }
    }

    private function create_job_and_send_invitation(User $employer, int $i)
    {
        $job = [
            "job_store" => EmployerStoreList::where("employer_id", $employer->id)->first()->id,
            "job_title" => fake()->jobTitle(),
            "job_date" => today()->addDays($i)->format(get_default_date_format()),
            "job_rate" => fake()->numberBetween(300, 1000),
        ];

        $response = $this->actingAs($employer)->post('/employer/managejob', $job);
        $response->assertSessionHas("success");
        $response->assertStatus(302);

        $job = JobPost::where("employer_id", $employer->id)->orderBy("id", "DESC")->first();
        $response = $this->actingAs($employer)->get("/employer/job-search/{$job->id}");

        $freelancers = $response->viewData("freelancers");
        if (sizeof($freelancers) == 0) {
            $this->assertTrue(false);
        }
        $freelancer_ids = [];
        foreach ($freelancers as $freelancer) {
            $freelancer_ids[] = $freelancer->id;
        }
        $invitationResponse = $this->actingAs($employer)->post("/employer/invite-for-job/{$job->id}", [
            "checkinvite" => $freelancer_ids,
        ]);
        Log::debug("Session: " . $invitationResponse->baseResponse->content() . "\n");

        $invitationResponse->assertSessionHas("success");
    }
}
