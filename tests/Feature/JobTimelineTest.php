<?php

namespace Tests\Feature;

use App\Models\EmployerStoreList;
use App\Models\JobPost;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JobTimelineTest extends TestCase
{
    /**
     * @testdox Job timeline test by creating job with one timeline and testing it's behaviour with time.
     * 1. Create new job.
     * 2. Invite few freelancers for job
     * 3. Increase date to job_timeline_date and running CRON job schedule at job_timeline_hour.
     * 4. Checking that job_rate increased to job_timeline_rate.
     * 5. Test done
     */
    public function test_job_with_timeline()
    {

        $this->artisan("migrate:fresh");
        $this->seed('Database\\Seeders\\TestingDatabaseSeeder');

        $employer = User::find(2);
        $job_date = Carbon::parse(fake()->dateTimeBetween('+1 days', '+9 days'));
        $job_date_2 = $job_date->copy()->subDays(5);
        $job_rate = fake()->numberBetween(200, 250);
        $job_rate_2 = $job_rate * 2;

        $job = [
            "job_store" => EmployerStoreList::where("employer_id", 2)->first()->id,
            "job_title" => fake()->jobTitle(),
            "job_date" => $job_date->format(get_default_date_format()),
            "job_rate" => $job_rate,
            "set_timeline" => 1,
            "job_date_new" => [$job_date_2->format(get_default_date_format())],
            "job_rate_new" => [$job_rate_2],
            "job_timeline_hrs" => [8],
        ];

        $response = $this->actingAs($employer)->post('/employer/managejob', $job);
        $response->assertSessionHas("success");
        $response->assertStatus(302);

        $job = JobPost::find(1);
        $response = $this->actingAs($employer)->get("/employer/job-search/{$job->id}");
        $freelancers = $response->viewData("freelancers");
        if (sizeof($freelancers) == 0) {
            $this->assertTrue(false, "There must be one freelancer");
        }

        $freelancer_ids = [];
        foreach ($freelancers as $freelancer) {
            $freelancer_ids[] = $freelancer->id;
        }

        $invitationResponse = $this->actingAs($employer)->post("/employer/invite-for-job/{$job->id}", [
            "checkinvite" => $freelancer_ids,
        ]);

        $invitationResponse->assertSessionHas("success");

        //Now go to timeline date and clear mail logs befor that
        Storage::delete("logs/mail.log");
        Carbon::setTestNow($job_date_2->setTime(8, 0));
        Artisan::call("schedule:run");

        $job = JobPost::find($job->id);
        $this->assertTrue($job->job_rate == $job_rate_2);
    }

    /**
     * @testdox Job timeline test by creating job with two timelines and testing it's behaviour with time.
     * 1. Same as single timeline test.
     * 2. We increase time one by one for each timeline.
     * 3. Checking if job_rate increases in database accordingly.
     */
    public function test_job_with_two_timeline()
    {

        $this->artisan("migrate:fresh");
        $this->seed('Database\\Seeders\\TestingDatabaseSeeder');

        $employer = User::find(2);
        $job_date = Carbon::parse(fake()->dateTimeBetween('+10 days', '+30 days'));
        $job_date_2 = $job_date->copy()->subDays(5);
        $job_date_3 = $job_date->copy()->subDays(2);
        $job_rate = fake()->numberBetween(200, 250);
        $job_rate_2 = $job_rate * 2;
        $job_rate_3 = $job_rate * 2.5;
        $job_timeline2_hour = 8;
        $job_timeline3_hour = 9;

        $job = [
            "job_store" => EmployerStoreList::where("employer_id", 2)->first()->id,
            "job_title" => fake()->jobTitle(),
            "job_date" => $job_date->format(get_default_date_format()),
            "job_rate" => $job_rate,
            "set_timeline" => 1,
            "job_date_new" => [$job_date_2->format(get_default_date_format()), $job_date_3->format(get_default_date_format())],
            "job_rate_new" => [$job_rate_2, $job_rate_3],
            "job_timeline_hrs" => [$job_timeline2_hour, $job_timeline3_hour],
        ];

        $response = $this->actingAs($employer)->post('/employer/managejob', $job);
        $response->assertSessionHas("success");
        $response->assertStatus(302);

        $job = JobPost::find(1);

        $response = $this->actingAs($employer)->get("/employer/job-search/{$job->id}");
        $freelancers = $response->viewData("freelancers");
        if (sizeof($freelancers) == 0) {
            $this->assertTrue(false, "There must be one freelancer");
        }
        $freelancer_ids = [];
        foreach ($freelancers as $freelancer) {
            $freelancer_ids[] = $freelancer->id;
        }
        $invitationResponse = $this->actingAs($employer)->post("/employer/invite-for-job/{$job->id}", [
            "checkinvite" => $freelancer_ids,
        ]);
        $invitationResponse->assertSessionHas("success");

        Carbon::setTestNow($job_date_2->setTime($job_timeline2_hour, 0));
        Artisan::call("schedule:run");

        $job = JobPost::find($job->id);
        $this->assertTrue($job->job_rate == $job_rate_2);

        Carbon::setTestNow($job_date_3->setTime($job_timeline3_hour, 0));
        Artisan::call("schedule:run");

        $job = JobPost::find($job->id);
        $this->assertTrue($job->job_rate == $job_rate_3);
    }
}
