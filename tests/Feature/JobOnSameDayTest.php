<?php

namespace Tests\Feature;

use App\Models\EmployerStoreList;
use App\Models\JobPost;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class JobOnSameDayTest extends TestCase
{
    /**
     * @testdox Job on same day test by posting and accepting job on same day.
     */
    public function test_same_day_job()
    {

        $this->artisan("migrate:fresh");
        $this->seed('Database\\Seeders\\TestingDatabaseSeeder');
        Carbon::setTestNow(today()->setTime(1, 0));
        $employer = User::find(2);

        $job = [
            "job_store" => EmployerStoreList::where("employer_id", 2)->first()->id,
            "job_title" => fake()->jobTitle(),
            "job_date" => today()->format(get_default_date_format()),
            "job_rate" => fake()->numberBetween(200, 1000),
        ];

        $response = $this->actingAs($employer)->post('/employer/managejob', $job);
        $response->assertSessionHas("success");
        $response->assertStatus(302);

        $job = JobPost::find(1);
        $response = $this->actingAs($employer)->get("/employer/job-search/{$job->id}");

        $freelancers = $response->viewData("freelancers");
        if (sizeof($freelancers) == 0) {
            $this->assertTrue(false);
        }
        $freelancer = $freelancers[0];

        $invitationResponse = $this->actingAs($employer)->post("/employer/invite-for-job/{$job->id}", [
            "checkinvite" => [$freelancer->id],
        ]);

        $invitationResponse->assertSessionHas("success");

        Carbon::setTestNow(today()->setTime(10, 00));

        //Check mobile API response
        $responseData = $this->postJson("/api/user-job-action", [
            "user_id" => $freelancer->id,
            "user_role" => User::USER_ROLE_LOCUM,
            "page_id" => "interested-job-list",
        ])->assertJsonFragment(["success" => true])->json("data");
        $this->assertTrue(sizeof($responseData) == 1);
        $this->assertTrue($responseData[0]["job_id"] == $job->id);

        //Job acceptance
        $encrypted_job_id = encrypt($job->id);
        $encrypted_freelancer_id = encrypt($freelancer->id);
        $encrypted_freelancer_type = encrypt("live");
        $accept_href_link = url("/accept-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");

        $response = $this->get($accept_href_link);
        $response->assertStatus(302)->assertRedirect('/login');

        $response = $this->actingAs($freelancer)->get($accept_href_link);
        $response->assertStatus(200);

        $response->assertSeeText("Job accepted successfully.");

        Carbon::setTestNow(today()->setTime(10, 50));
        Artisan::call("schedule:run");
    }
}
