<?php

namespace Tests\Feature;

use App\Models\EmployerStoreList;
use App\Models\JobAction;
use App\Models\JobInvitedUser;
use App\Models\JobPost;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FreezeJobTest extends TestCase
{
    /**
     * @testdox Job freeze test by creating new job and inviting freelancers. Then one freelancer freeze the job. Meenwhile second freelancer try to accept job and fails. Then after 15minutes job will be unfreeze. Then first freelancer try to freeze the job but unsuccessfull because he already freezed it. Then second freelancer accept the job.
     */
    public function test_freezing_of_job()
    {

        $this->artisan("migrate:fresh");
        $this->seed('Database\\Seeders\\TestingDatabaseSeeder');

        $employer = User::find(2);
        $job = [
            "job_store" => EmployerStoreList::where("employer_id", $employer->id)->first()->id,
            "job_title" => fake()->jobTitle(),
            "job_date" => fake()->dateTimeBetween('+2 days', '+5 days')->format(get_default_date_format()),
            "job_rate" => fake()->numberBetween(200, 800),
        ];

        $response = $this->actingAs($employer)->post('/employer/managejob', $job);
        $response->assertSessionHas("success");
        $response->assertStatus(302);

        $job = JobPost::find(1);
        $response = $this->actingAs($employer)->get("/employer/job-search/{$job->id}");
        $freelancers = $response->viewData("freelancers");
        if (sizeof($freelancers) == 0) {
            $this->assertTrue(false, "Must be one freelancer searched");
        }
        if (sizeof($freelancers) == 1 && $job->job_rate >= 250) {
            $this->assertTrue(false, "Must be two freelancer searched");
        }
        if (sizeof($freelancers) == 2 && $job->job_rate >= 700) {
            $this->assertTrue(false, "Must be three freelancer searched");
        }

        $first_freelancer = $freelancers[0];
        $freelancer_ids = [];
        foreach ($freelancers as $freelancer) {
            $freelancer_ids[] = $freelancer->id;
        }

        $invitationResponse = $this->actingAs($employer)->post("/employer/invite-for-job/{$job->id}", [
            "checkinvite" => $freelancer_ids,
        ]);

        $invitationResponse->assertSessionHas("success");

        //Job freeze
        $encrypted_job_id = encrypt($job->id);
        $encrypted_freelancer_id = encrypt($first_freelancer->id);
        $encrypted_freelancer_type = encrypt("live");
        $freeze_href_link = url("/freeze-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");

        $response = $this->get($freeze_href_link);
        $response->assertStatus(302);

        $response = $this->actingAs($first_freelancer)->get($freeze_href_link);
        $response->assertStatus(200);
        $response->assertSeeText("Job will be frozen for 15 minutes only");

        $response = $this->actingAs($first_freelancer)->get($freeze_href_link);
        $response->assertStatus(200);
        $response->assertSeeText("You already freezed this job.");


        if (sizeof($freelancers) >= 2) {
            //check if freeze again
            $second_freelancer = $freelancers[1];
            $encrypted_freelancer_id = encrypt($second_freelancer->id);
            $freeze_href_link = url("/freeze-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");

            $response = $this->actingAs($second_freelancer)->get($freeze_href_link);
            $response->assertStatus(200);
            $response->assertSeeText("Thank you for your interest however this job is curently held by another locum - If it goes live again we shall notify you.");
        }

        //Call CronResetFreeze cron job
        Carbon::setTestNow(now()->addHour()->setMinute(0));
        Artisan::call("schedule:run");

        $job = JobPost::find(1);
        $this->assertTrue($job->job_status == JobPost::JOB_STATUS_OPEN_WAITING);

        //accept job after freeze ends
        //Job acceptance
        $encrypted_job_id = encrypt($job->id);
        $encrypted_freelancer_id = encrypt($first_freelancer->id);
        $encrypted_freelancer_type = encrypt("live");
        $accept_href_link = url("/accept-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");

        $response = $this->get($accept_href_link);
        $response->assertStatus(302)->assertRedirect('/login');

        $response = $this->actingAs($first_freelancer)->get($accept_href_link);
        $response->assertStatus(200);

        $response->assertSeeText("Job accepted successfully.");
    }
}
