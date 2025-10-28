<?php

namespace Tests\Feature;

use App\Models\FinanceExpense;
use App\Models\FinanceIncome;
use App\Models\FreelancerPrivateJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LocumPrivateJobTest extends TestCase
{
    /* This is to make sure all things for private job work from freelancer side only */
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_private_jobs_creation_and_compeletion()
    {

        $this->artisan("migrate:fresh");
        $this->seed('Database\\Seeders\\TestingDatabaseSeeder');

        $freelancer = User::find(1);
        FreelancerPrivateJob::factory()->freelancer($freelancer->id)->create();
        $private_job = FreelancerPrivateJob::where("freelancer_id", $freelancer->id)->latest()->first();
        $job_day_diff = now()->diffInDays($private_job->job_date);

        Carbon::setTestNow(now()->addDays($job_day_diff)->setTime(9, 0));
        Artisan::call("schedule:run");
        $private_job = FreelancerPrivateJob::find($private_job->id);
        $this->assertTrue($private_job->status == 1, "Must be change to reminded status");
        Carbon::setTestNow(now()->addDay()->setTime(11, 0));
        Artisan::call("schedule:run");
        $private_job = FreelancerPrivateJob::find($private_job->id);
        $this->assertTrue($private_job->status == 2, "Must be changes to on day sended");

        //Attend private job
        $job_id_encrypted = encrypt($private_job->id);
        $freelancer_id_encrypted = encrypt($freelancer->id);
        $action_encrypted = encrypt("yes");
        $job_type_encrypted = encrypt("private");
        $private_job_attend_link = url("/attendance?job_id={$job_id_encrypted}&user_id={$freelancer_id_encrypted}&action={$action_encrypted}&job_type={$job_type_encrypted}");

        $this->actingAs($freelancer)->get($private_job_attend_link)->assertOk()->assertViewIs("shared.attendance")->assertViewHas("check_job_status", 1);
        $private_job = FreelancerPrivateJob::find($private_job->id);
        $income_count = FinanceIncome::where("job_id", $private_job->id)->where("job_type", 2)->where("freelancer_id", $freelancer->id)->count();

        $this->assertTrue($income_count == 1, "Must be added income for private job");
        $this->assertTrue($private_job->status == FreelancerPrivateJob::STATUS_JOB_ATTENDED, "Must be added attened status to private job");

        Carbon::setTestNow(now()->setTime(14, 0));
        Artisan::call("schedule:run");
        $private_job = FreelancerPrivateJob::find($private_job->id);
        $this->assertTrue($private_job->status == FreelancerPrivateJob::STATUS_EXPENSE_NOTIFICATION_SEND, "Must be changes to on day expense sended");

        $encrypted_job_type = encrypt("private");
        $private_job_expense_form_url = url("/expense-cost-form?job_id={$job_id_encrypted}&freelancer_id={$freelancer_id_encrypted}&job_type={$encrypted_job_type}");

        $response = $this->actingAs($freelancer)->get($private_job_expense_form_url)->assertOk()->assertViewIs("freelancer.expense-cost-form");
        $expenseCats = $response->viewData("expense_types");

        $freelancer_expense_category_ids = [];
        $freelancer_costs = [];
        foreach (array_rand($expenseCats->toArray(), 3) as $index) {
            $freelancer_expense_category_ids[] = $expenseCats[$index]->id;
            $freelancer_costs[] = random_int(1, 5) * 10;
        }

        $response = $this->actingAs($freelancer)->post($private_job_expense_form_url, [
            "cat" => $freelancer_expense_category_ids,
            "cost" => $freelancer_costs
        ])->assertRedirect(route("freelancer.dashboard"))->assertSessionHas("success");

        $count = FinanceExpense::where("freelancer_id", $freelancer->id)->where("job_id", $private_job->id)->where("job_type", 2)->count();
        $this->assertTrue($count === 3);
    }
}
