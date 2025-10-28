<?php

namespace Tests\Feature;

use App\Models\EmployerStoreList;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TwoFreelancerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_employer_need_two_freelancer_on_same_date()
    {

        $this->artisan("migrate:fresh");
        $this->seed('Database\\Seeders\\TestingDatabaseSeeder');

        $employer = User::find(2);

        $this->inviate_and_accept_by_posting_new_job($employer, "20/02/2023", 430.50);
        $this->inviate_and_accept_by_posting_new_job($employer, "20/02/2023", 430.50);
        $this->inviate_and_accept_by_posting_new_job($employer, "20/02/2023", 430.50);
        $this->inviate_and_accept_by_posting_new_job($employer, "20/02/2023", 430.50);
        $this->inviate_and_accept_by_posting_new_job($employer, "20/02/2023", 430.50);
    }
}
