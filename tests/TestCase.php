<?php

namespace Tests;

use App\Models\EmployerStoreList;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function give_feedback(string $url, Authenticatable $user, int $employer_id, int $freelancer_id, int $job_id, Collection $questions, string $user_type): TestResponse
    {
        $question_request_data_ratevalue = [];
        $question_request_data_fdqus = [];
        $question_request_data_fdqusid = [];
        $total_rating = 0;
        foreach ($questions as $question) {
            $rate = random_int(2, 5);
            $question_request_data_ratevalue[] = $rate;
            $question_request_data_fdqus[] = $question['question_' . $user_type];
            $question_request_data_fdqusid[] = $question->id;
            $total_rating += $rate;
        }
        $total_rating = $total_rating / sizeof($questions);

        $data = [
            "employer_id" => $employer_id,
            "freelancer_id" => $freelancer_id,
            "job_id" => $job_id,
            "total-rating" => $total_rating,
            "ratevalue" => $question_request_data_ratevalue,
            "fdqus" => $question_request_data_fdqus,
            "fdqusid" => $question_request_data_fdqusid,
            "user_type" => $user_type,
            "cat_id" => 3,
            "comment" => fake()->paragraph(),
        ];

        return $this->actingAs($user)->post($url, $data);
    }

    /**
     * @param User $employer Who post the job
     * @param string $date Job date in m/d/Y format.
     * @param float $rate Job rate
     * @param User|null $freelancer If given will be used as locum who accept the job.
     * @return int Freelancer id who accepted the job.
     */
    protected function inviate_and_accept_by_posting_new_job(User $employer, string $date, float $rate, User $freelancer = null): int
    {
        $job = [
            "job_store" => EmployerStoreList::where("employer_id", $employer->id)->first()->id,
            "job_title" => "Need 3-4 freelancers Job",
            "job_date" => $date,
            "job_rate" => $rate,
        ];

        $response = $this->actingAs($employer)->post('/employer/managejob', $job);
        $response->assertSessionHas("success");
        $response->assertStatus(302);

        $job = JobPost::orderBy("id", "DESC")->first();
        $response = $this->actingAs($employer)->get("/employer/job-search/{$job->id}");
        $freelancers = $response->viewData("freelancers");
        if (sizeof($freelancers) == 0) {
            $this->assertTrue(false, 'Need atleast 1 freelancer');
        }
        fwrite(STDOUT, print_r("Freelancer found for job#{$job->id} are: " . sizeof($freelancers) . "{$freelancers->pluck('id')}" . "\n\r", TRUE));

        $invitationResponse = $this->actingAs($employer)->post("/employer/invite-for-job/{$job->id}", [
            "checkinvite" => array_unique($freelancers->pluck('id')->toArray() + ($freelancer ? [$freelancer->id] : [])),
        ]);
        $invitationResponse->assertSessionHas("success");
        if (is_null($freelancer)) {
            $freelancer = $freelancers->random();
        }
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

        fwrite(STDOUT, print_r("Freelancer {$freelancer->id} accepted the job job# {$job->id} " . "\n\r", TRUE));

        return $freelancer->id;
    }

    public function log(string $message)
    {
        fwrite(STDOUT, print_r($message . "\n\r", TRUE));
    }
}
