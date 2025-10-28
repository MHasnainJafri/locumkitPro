<?php

namespace Tests\Feature;

use App\Models\EmployerStoreList;
use App\Models\FinanceExpense;
use App\Models\JobOnDay;
use App\Models\JobPost;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class JobDayExpenseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_on_job_day_expense()
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
        //Send onday_expense using CRON
        Carbon::setTestNow(now()->setTime(14, 0));
        Artisan::call("schedule:run");

        $encrypted_employer_id = encrypt($employer->id);
        $expenseFormUrl = url("/expense-cost-form?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&employer_id={$encrypted_employer_id}");
        $response = $this->actingAs($freelancer)->get($expenseFormUrl)->assertOk()->assertViewIs("freelancer.expense-cost-form");
        $expenseCats = $response->viewData("expense_types");

        $freelancer_expense_category_ids = [];
        $freelancer_costs = [];
        foreach (array_rand($expenseCats->toArray(), 3) as $index) {
            $freelancer_expense_category_ids[] = $expenseCats[$index]->id;
            $freelancer_costs[] = random_int(1, 5) * 10;
        }

        $response = $this->post($expenseFormUrl, [
            "cat" => $freelancer_expense_category_ids,
            "cost" => $freelancer_costs
        ])->assertRedirect(route("freelancer.dashboard"))->assertSessionHas("success");

        $count = FinanceExpense::where("freelancer_id", $freelancer->id)->where("job_id", $job->id)->where("job_type", 1)->count();
        $this->assertTrue($count === 3);
    }
}
