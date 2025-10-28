<?php

namespace Tests\Feature;

use App\Helpers\DistanceCalculateHelper;
use App\Models\EmployerStoreList;
use App\Models\JobPost;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class DistanceCalculateTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_distance_helper_working()
    {
        //For the test to run please make sure database is already migrated.
        $this->seed('Database\\Seeders\\SiteTownSeeder');

        $this->postJson("/ajax/get-town-list", [
            "zip" => 10017,
            "full_addr" => "New York, NY 10017, USA",
            "max_dis" => 5
        ])->assertSeeText("No record found. Please check the post code Or try with higher range.");

        /* $this->postJson("/ajax/get-town-list", [
            "zip" => "GU7 2",
            "full_addr" => "Aarons Hill",
            "max_dis" => 5
        ])->assertSeeText('40 record(s) found.'); */
    }
}
