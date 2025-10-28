<?php

namespace Database\Seeders;

use App\Models\FeedbackQuestion;
use App\Models\JobFeedback;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class JobFeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $questions = FeedbackQuestion::where("question_status", 1)->whereNotNull("question_employer")->where("question_employer", "!=", "")->where("question_cat_id", 3)->get();
        if (sizeof($questions) > 0) {
            Schema::disableForeignKeyConstraints();
            JobFeedback::truncate();
            Schema::enableForeignKeyConstraints();


            $feedback_json = array();
            $rate = 0;
            for ($i = 0; $i < sizeof($questions); $i++) {
                $question_rate = random_int(1, 5);
                $feedback_json[] = [
                    "qusId" => $questions[$i]->id,
                    "qus" => $questions[$i]->question_employer,
                    "qusRate" => $question_rate,
                ];
                $rate += $question_rate;
            }
            $rate = $rate / sizeof($questions);
            $feedback = [
                "employer_id" => 2,
                "freelancer_id" => 1,
                "job_id" => null,
                "rating" => $rate,
                "feedback" => json_encode($feedback_json),
                "comments" => fake()->sentence(),
                "user_type" => "employer",
                "cat_id" => 3,
                "status" => 1,
                "created_at" => fake()->dateTimeThisMonth()
            ];

            JobFeedback::insert($feedback);
        }
    }
}