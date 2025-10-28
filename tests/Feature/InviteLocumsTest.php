<?php

namespace Tests\Feature;

use App\Models\EmployerStoreList;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InviteLocumsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_invite_locum_for_job_and_accept_the_job()
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
    }
}
