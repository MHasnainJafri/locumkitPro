<?php

namespace Tests\Feature;

use App\Models\EmployerStoreList;
use App\Models\JobPost;
use App\Models\PrivateUser;
use App\Models\PrivateUserJobAction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PrivateUserJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_employer_private_users_flow()
    {

        $this->artisan("migrate:fresh");
        $this->seed('Database\\Seeders\\TestingDatabaseSeeder');
        $employer = User::find(2);

        PrivateUser::factory()->employer($employer->id)->create();

        $job = [
            "job_store" => EmployerStoreList::where("employer_id", 2)->first()->id,
            "job_title" => fake()->jobTitle(),
            "job_date" => fake()->dateTimeBetween('+10 days', '+30 days')->format(get_default_date_format()),
            "job_rate" => fake()->numberBetween(200, 1000),
        ];

        $response = $this->actingAs($employer)->post('/employer/managejob', $job);
        $response->assertSessionHas("success");
        $response->assertStatus(302);

        $job = JobPost::latest()->first();
        $response = $this->actingAs($employer)->get("/employer/job-search/{$job->id}");

        $private_freelancers = $response->viewData("private_freelancers");
        if (sizeof($private_freelancers) == 0) {
            $this->assertTrue(false, "Test just created a prive freelancer and system must be shown that!");
        }
        $private_freelancer = $private_freelancers[0];

        $invitationResponse = $this->actingAs($employer)->post("/employer/invite-for-job/{$job->id}", [
            "checkinvitep" => [$private_freelancer->id],
        ]);

        $invitationResponse->assertSessionHas("success");

        //Job acceptance
        $encrypted_job_id = encrypt($job->id);
        $encrypted_freelancer_id = encrypt($private_freelancer->id);
        $encrypted_freelancer_type = encrypt("private");
        $accept_href_link = url("/accept-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");

        $response = $this->get($accept_href_link);
        $response->assertStatus(200);

        $response->assertSeeText("Job accepted successfully.");

        $response = $this->get($accept_href_link);
        $response->assertStatus(200);

        $response->assertSeeText("You have already accepted this job.");

        //Send job reminder
        $job = JobPost::find($job->id);
        $job_day_diff = now()->diffInDays($job->job_date);

        Carbon::setTestNow(now()->addDays($job_day_diff)->setTime(9, 0));
        Artisan::call("schedule:run");
        $count = PrivateUserJobAction::where("employer_id", $employer->id)->where("private_user_id", $private_freelancer->id)->where("status", PrivateUserJobAction::ACTION_ACCEPT)->where("notify", 1)->count();
        $this->assertTrue($count >= 1);

        //Send On Day Reminder
        Carbon::setTestNow(now()->addDay()->setTime(11, 0));
        Artisan::call("schedule:run");
        $count = PrivateUserJobAction::where("employer_id", $employer->id)->where("private_user_id", $private_freelancer->id)->where("status", PrivateUserJobAction::ACTION_ACCEPT)->where("notify", 2)->count();
        $this->assertTrue($count >= 1);

        //Complete the job
        Carbon::setTestNow(now()->addDay()->setTime(9, 0));
        Artisan::call("schedule:run");
        $count = PrivateUserJobAction::where("employer_id", $employer->id)->where("private_user_id", $private_freelancer->id)->where("status", PrivateUserJobAction::ACTION_DONE)->where("notify", 2)->count();
        $this->assertTrue($count >= 1);

        $job = JobPost::find($job->id);
        $this->assertTrue($job->job_status == JobPost::JOB_STATUS_DONE_COMPLETED);
    }
}
