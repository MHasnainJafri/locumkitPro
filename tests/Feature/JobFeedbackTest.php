<?php

namespace Tests\Feature;

use App\Models\EmployerStoreList;
use App\Models\JobAction;
use App\Models\JobPost;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class JobFeedbackTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_feedback_creation()
    {

        $this->artisan("migrate:fresh");
        $this->seed('Database\\Seeders\\TestingDatabaseSeeder');

        $employer = User::find(2);

        $job = [
            "job_store" => EmployerStoreList::where("employer_id", 2)->first()->id,
            "job_title" => fake()->jobTitle(),
            "job_date" => fake()->dateTimeBetween('+10 days', '+30 days')->format(get_default_date_format()),
            "job_rate" => fake()->numberBetween(350, 1000),
        ];

        $response = $this->actingAs($employer)->post('/employer/managejob', $job);
        $response->assertSessionHas("success");
        $response->assertStatus(302);

        $job = JobPost::where("employer_id", $employer->id)->latest()->first();
        $response = $this->actingAs($employer)->get("/employer/job-search/{$job->id}");

        $freelancers = $response->viewData("freelancers");
        if (sizeof($freelancers) == 0) {
            $this->assertTrue(false, "Must to get atleast one freelancer");
            return;
        }
        $freelancer = $freelancers[0];

        $invitationResponse = $this->actingAs($employer)->post("/employer/invite-for-job/{$job->id}", [
            "checkinvite" => [$freelancer->id],
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
        $job = JobPost::find($job->id);
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
        $job = JobPost::find($job->id);
        $this->assertTrue($job->job_status == JobPost::JOB_STATUS_DONE_COMPLETED);

        $encrypted_user_type_freelancer = encrypt("freelancer");
        $encrypted_user_type_employer = encrypt("employer");
        $encrypted_employer_id = encrypt($job->employer_id);
        //Add feedback from freelancer side
        $feedback_url_freelancer = url("/feedback?job_id={$encrypted_job_id}&user_id={$encrypted_freelancer_id}&user_type={$encrypted_user_type_freelancer}");
        $response = $this->actingAs($freelancer)->get($feedback_url_freelancer)->assertOk()->assertViewIs("shared.feedback");
        $question_array = $response->viewData("allFeedbackQusArray");
        $user_type = $response->viewData("user_type");
        $this->give_feedback("/post-feedback", $freelancer, $job->employer_id, $freelancer->id, $job->id, $question_array, $user_type);
        //Add feedback from employer side
        $feedback_url_employer = url("/feedback?job_id={$encrypted_job_id}&user_id={$encrypted_employer_id}&user_type={$encrypted_user_type_employer}");
        $response = $this->actingAs($job->employer)->get($feedback_url_employer)->assertOk()->assertViewIs("shared.feedback");
        $this->actingAs($job->employer)->get($feedback_url_freelancer)->assertSessionHas("error");
        $question_array = $response->viewData("allFeedbackQusArray");
        $user_type = $response->viewData("user_type");
        $this->give_feedback("/post-feedback", $job->employer, $job->employer_id, $freelancer->id, $job->id, $question_array, $user_type);

        //Approve feedback using CRON

        Carbon::setTestNow(now()->addDays(2)->setTime(24, 0));
        Artisan::call("schedule:run");

        //Check if feedback given
        $response = $this->actingAs($freelancer)->get("/freelancer/feedback-detail")->assertOk()->assertViewHas("feedbacks");
        $feedbacks_for_freelancer = $response->viewData("feedbacks");
        $this->assertTrue(count($feedbacks_for_freelancer) >= 1);

        $response = $this->actingAs($job->employer)->get("/employer/feedback-detail")->assertOk()->assertViewHas("feedbacks");
        $feedbacks_for_employer = $response->viewData("feedbacks");
        $this->assertTrue(count($feedbacks_for_employer) >= 1);
    }
}
