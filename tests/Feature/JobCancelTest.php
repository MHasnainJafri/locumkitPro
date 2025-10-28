<?php

namespace Tests\Feature;

use App\Models\EmployerStoreList;
use App\Models\JobCancelation;
use App\Models\JobPost;
use App\Models\User;
use Tests\TestCase;

class JobCancelTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_job_cancel()
    {

        $this->artisan("migrate:fresh");
        $this->seed('Database\\Seeders\\TestingDatabaseSeeder');

        $employer = User::find(2);

        $job = [
            "job_store" => EmployerStoreList::where("employer_id", 2)->first()->id,
            "job_title" => fake()->jobTitle(),
            "job_date" => fake()->dateTimeBetween('+10 days', '+30 days')->format(get_default_date_format()),
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
            "checkinvite" => [$freelancers[0]->id],
        ]);

        $invitationResponse->assertSessionHas("success");

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

        $cancel_job_href = url("/cancel-job?job_id={$job->id}");

        $response = $this->actingAs($freelancer)->get($cancel_job_href);
        $response->assertOk();
        $response->assertViewIs("shared.cancel-job");

        //Send post form to cancel the job
        $form_post_action = $response->viewData("form_post_action");
        $response = $this->post($form_post_action, [
            "cancel-reason" => fake()->paragraph()
        ]);
        $response->assertSessionHas("success");

        $job = JobPost::find($job->id);

        $this->assertTrue($job->job_status == JobPost::JOB_STATUS_CANCELED);
        $this->assertTrue($job->getJobCancelByUserType() == JobCancelation::CANCEL_BY_LIVE_FREELANCER);
    }
}
