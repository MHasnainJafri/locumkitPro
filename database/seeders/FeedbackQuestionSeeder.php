<?php

namespace Database\Seeders;

use App\Models\FeedbackQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class FeedbackQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Schema::disableForeignKeyConstraints();
        FeedbackQuestion::truncate();
        Schema::enableForeignKeyConstraints();

        $feedback_qustions = array(
            array('question_freelancer' => 'Test feedback qus 2 fro freelancer', 'question_employer' => 'Test feedback qus 2 fro Employer', 'question_cat_id' => '1', 'question_sort_order' => '2', 'question_status' => '2', 'created_at' => '2017-02-11 14:54:02'),
            array('question_freelancer' => 'Test feedback qus 3 fro freelancer', 'question_employer' => 'Test feedback qus 3 fro Employer', 'question_cat_id' => '1', 'question_sort_order' => '3', 'question_status' => '2', 'created_at' => '2017-02-11 14:56:20'),
            array('question_freelancer' => 'Test feedback qus 3 fro freelancer', 'question_employer' => 'Test feedback qus 3 fro Employer', 'question_cat_id' => '1', 'question_sort_order' => '3', 'question_status' => '2', 'created_at' => '2017-02-11 14:58:23'),
            array('question_freelancer' => 'Test feedback qus 3 fro freelancer', 'question_employer' => 'Test feedback qus 3 fro Employer Edited Question', 'question_cat_id' => '5', 'question_sort_order' => '3', 'question_status' => '1', 'created_at' => '2017-02-11 15:33:13'),
            array('question_freelancer' => 'Test feedback qus 3 for locum', 'question_employer' => 'Test feedback qus 3 fro Employer', 'question_cat_id' => '1', 'question_sort_order' => '3', 'question_status' => '1', 'created_at' => '2017-02-11 15:33:36'),
            array('question_freelancer' => 'Test feedback qus 3 fro locum', 'question_employer' => 'Test feedback qus 3 fro Employer', 'question_cat_id' => '4', 'question_sort_order' => '3', 'question_status' => '0', 'created_at' => '2017-02-11 15:33:54'),
            array('question_freelancer' => 'Test feedback qus 3 fro locum Pharmacy', 'question_employer' => 'Test feedback qus 3 fro Employer Pharmacy', 'question_cat_id' => '4', 'question_sort_order' => '3', 'question_status' => '0', 'created_at' => '2017-02-11 15:37:12'),
            array('question_freelancer' => 'Test feedback qus 3 fro freelancer', 'question_employer' => 'Test feedback qus 3 fro Employer', 'question_cat_id' => '1', 'question_sort_order' => '3', 'question_status' => '2', 'created_at' => '2017-02-11 15:37:33'),
            array('question_freelancer' => 'Test feedback qus 3 fro freelancer', 'question_employer' => 'Test feedback qus 3 fro Employer', 'question_cat_id' => '1', 'question_sort_order' => '3', 'question_status' => '2', 'created_at' => '2017-02-11 15:40:07'),
            array('question_freelancer' => 'Test feedback qus 4 for freelancer Optometry 123', 'question_employer' => '', 'question_cat_id' => '3', 'question_sort_order' => '1', 'question_status' => '2', 'created_at' => '2017-02-14 12:49:13'),
            array('question_freelancer' => 'Was the store/equipment as described in the job advert?', 'question_employer' => '', 'question_cat_id' => '3', 'question_sort_order' => '0', 'question_status' => '1', 'created_at' => '2017-02-14 16:36:48'),
            array('question_freelancer' => '', 'question_employer' => 'Test question 5 for Employer optometry', 'question_cat_id' => '3', 'question_sort_order' => '1', 'question_status' => '2', 'created_at' => '2017-02-14 18:56:20'),
            array('question_freelancer' => 'Time', 'question_employer' => 'Time', 'question_cat_id' => '1', 'question_sort_order' => '0', 'question_status' => '0', 'created_at' => '2017-03-26 06:33:42'),
            array('question_freelancer' => '', 'question_employer' => 'How would you rate the locum\'s flexibility?', 'question_cat_id' => '3', 'question_sort_order' => '0', 'question_status' => '1', 'created_at' => '2017-03-26 06:34:14'),
            array('question_freelancer' => '', 'question_employer' => 'How would you rate the locum\'s punctuality/time-keeping?', 'question_cat_id' => '3', 'question_sort_order' => '0', 'question_status' => '1', 'created_at' => '2017-03-26 06:34:38'),
            array('question_freelancer' => '', 'question_employer' => 'How would you rate the locum\'s professionalism? ', 'question_cat_id' => '3', 'question_sort_order' => '0', 'question_status' => '1', 'created_at' => '2017-03-26 06:34:51'),
            array('question_freelancer' => '', 'question_employer' => 'How would you rate the locum\'s team work and staff interaction?', 'question_cat_id' => '3', 'question_sort_order' => '0', 'question_status' => '1', 'created_at' => '2017-03-26 06:35:02'),
            array('question_freelancer' => '', 'question_employer' => 'Clinical', 'question_cat_id' => '3', 'question_sort_order' => '0', 'question_status' => '2', 'created_at' => '2017-03-26 06:35:34'),
            array('question_freelancer' => '', 'question_employer' => 'Clinical', 'question_cat_id' => '3', 'question_sort_order' => '0', 'question_status' => '2', 'created_at' => '2017-03-26 06:35:35'),
            array('question_freelancer' => 'How would you rate the employer\'s professionalism? ', 'question_employer' => '', 'question_cat_id' => '3', 'question_sort_order' => '0', 'question_status' => '1', 'created_at' => '2017-03-26 06:38:11'),
            array('question_freelancer' => 'How satisfied were you with the the team and working environment?', 'question_employer' => '', 'question_cat_id' => '3', 'question_sort_order' => '0', 'question_status' => '1', 'created_at' => '2017-03-26 06:38:20'),
            array('question_freelancer' => 'How would you rate the stores\' time-keeping and diary management?', 'question_employer' => '', 'question_cat_id' => '3', 'question_sort_order' => '0', 'question_status' => '1', 'created_at' => '2017-03-26 06:38:28'),
            array('question_freelancer' => 'Overall how satisfied were you with the employer?', 'question_employer' => '', 'question_cat_id' => '3', 'question_sort_order' => '0', 'question_status' => '1', 'created_at' => '2017-03-26 06:38:48'),
            array('question_freelancer' => 'test', 'question_employer' => 'test', 'question_cat_id' => '6', 'question_sort_order' => '1', 'question_status' => '2', 'created_at' => '2017-07-19 19:41:54'),
            array('question_freelancer' => '', 'question_employer' => 'How would you rate the locum\'s clinical ability?', 'question_cat_id' => '3', 'question_sort_order' => '0', 'question_status' => '1', 'created_at' => '2018-05-28 21:55:17'),
            array('question_freelancer' => '', 'question_employer' => 'Overall how satisfied were you with the locum?', 'question_cat_id' => '3', 'question_sort_order' => '0', 'question_status' => '1', 'created_at' => '2018-05-28 21:55:59'),
            array('question_freelancer' => 'Test new freelancer 11/12/2018', 'question_employer' => 'Testing for employer 11/12/2018', 'question_cat_id' => '3', 'question_sort_order' => '0', 'question_status' => '2', 'created_at' => '2018-12-11 12:46:54')
        );
        FeedbackQuestion::insert($feedback_qustions);
    }
}