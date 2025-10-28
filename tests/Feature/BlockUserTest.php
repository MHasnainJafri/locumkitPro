<?php

namespace Tests\Feature;

use App\Models\BlockUser;
use App\Models\EmployerStoreList;
use App\Models\JobPost;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BlockUserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_employer_blocking_of_freelancer()
    {
        $this->artisan("migrate:fresh");
        $this->seed('Database\\Seeders\\TestingDatabaseSeeder');
        $employer = User::find(2);

        $job = [
            "job_store" => EmployerStoreList::where("employer_id", 2)->first()->id,
            "job_title" => fake()->jobTitle(),
            "job_date" => fake()->dateTimeBetween('+10 days', '+20 days')->format(get_default_date_format()),
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
            return;
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

        //Send job reminder
        $job = JobPost::find(1);
        $job_day_diff = now()->diffInDays($job->job_date);

        Carbon::setTestNow(now()->addDays($job_day_diff)->setTime(9, 0));
        Artisan::call("schedule:run");

        //Send On Day Reminder
        Carbon::setTestNow(now()->addDay()->setTime(11, 0));
        Artisan::call("schedule:run");

        //Mark the attadnce
        $encrypted_yes = encrypt("yes");
        $job_type_encrypted = encrypt("website");
        $yes_link = url("/attendance?job_id={$encrypted_job_id}&user_id={$encrypted_freelancer_id}&action={$encrypted_yes}&job_type={$job_type_encrypted}");
        $this->actingAs($freelancer)->get($yes_link)->assertSeeText("Attendance confirmed");

        //Send feedback
        Carbon::setTestNow(now()->addDay()->setTime(9, 0));
        Artisan::call("schedule:run");
        $job = JobPost::find(1);
        $this->assertTrue($job->job_status == JobPost::JOB_STATUS_DONE_COMPLETED);

        $encrypted_employer_id = encrypt($job->employer_id);
        //Block the user
        $block_url_locum = url("/block-user?employer_id={$encrypted_employer_id}&freelancer_id={$encrypted_freelancer_id}");
        $this->actingAs($employer)->get($block_url_locum)->assertOk()->assertViewIs("employer.block-user");
        $this->actingAs($employer)->post("/employer/block-user/{$freelancer->id}")->assertSessionHas("success");
        $this->actingAs($employer)->post("/employer/block-user/{$freelancer->id}")->assertSessionHas("error");

        $count = BlockUser::where("freelancer_id", $freelancer->id)->where("employer_id", $employer->id)->count();
        $this->assertTrue($count == 1);

        $job = [
            "job_store" => EmployerStoreList::where("employer_id", 2)->first()->id,
            "job_title" => fake()->jobTitle(),
            "job_date" => fake()->dateTimeBetween('+21 days', '+30 days')->format(get_default_date_format()),
            "job_rate" => fake()->numberBetween(200, 1000),
        ];

        $response = $this->actingAs($employer)->post('/employer/managejob', $job);
        $response->assertSessionHas("success");
        $response->assertStatus(302);

        $job = JobPost::find(1);
        $response = $this->actingAs($employer)->get("/employer/job-search/{$job->id}");

        $freelancers = $response->viewData("freelancers");
        $this->assertFalse(collect($freelancers)->contains('id', '=', $freelancer->id));
    }
}
