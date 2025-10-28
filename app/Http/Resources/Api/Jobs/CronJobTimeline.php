<?php

namespace App\Jobs;

use App\Helpers\AppNotificationHelper;
use App\Models\JobInvitedUser;
use App\Models\JobPost;
use App\Models\JobPostTimeline;
use App\Models\PrivateUser;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CronJobTimeline implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private AppNotificationHelper $notifyController;
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('updating the invoice here');
        $this->notifyController = new AppNotificationHelper();

        $header   = get_mail_header();
        $footer   = get_mail_footer();

        $result_jobs = JobPost::with(["job_post_timelines", "employer"])->where("job_date", ">=", today())->where("job_status", JobPost::JOB_STATUS_OPEN_WAITING)->whereHas("job_post_timelines", function ($query) {
            $query->whereDate("job_date_new", now())->where("job_timeline_status", 3)->where("job_timeline_hrs", now()->hour);
        })->get();

        $job_timeline_update_list = [];
        foreach ($result_jobs as $result_job_data) {
            $parant_job_id     = $result_job_data['id'];
            $employer_id     = $result_job_data['employer_id']; // employer id
            $job_title_e     = $result_job_data['job_title'];
            $job_date_e     = $result_job_data['job_date'];

            //Current EMP cancellation percentage
            $employer_cancellation_rate = get_job_cancellation_rate_by_user($employer_id, "employer") . "%";
            $employer_feedback_average = get_overall_feedback_rating_by_user($employer_id, "employer") . "%";
            //Current EMP feedback percentage
            if ($result_job_data->job_post_timelines && sizeof($result_job_data->job_post_timelines) > 0) {
                $job_timeline = $result_job_data->job_post_timelines()->whereDate("job_date_new", now())->where("job_timeline_status", 3)->where("job_timeline_hrs", now()->hour)->first();
                $employer = $result_job_data->employer;

                $job_date_new         = $job_timeline['job_date_new'];
                $job_rate_new         = $job_timeline['job_rate_new'];
                $job_timeline_hrs    = $job_timeline['job_timeline_hrs'];
                $tid                 = $job_timeline['id'];

                $result_job_data["job_rate"] = $job_rate_new;
                $result_job_data->save();
                $job_timeline_update_list[] = $job_timeline->id;
                //get user details
                $emp_fname = $employer['firstname'];
                $emp_email = $employer['email'];

                $empMsg = '<p>Hello ' . $emp_fname . ',</p><p>In accordance with your pre-specified job rate incremental, we have increased the job rate to ' . set_amount_format($job_rate_new) . '. We have also notified all applicable locums.</p><p>If incorrect, please check your listing  by clicking here or contacting us at Locumkit. </p>';
                $adminMsg = '<p>Hello Admin,</p>
					<h3>The job <b>' . $job_title_e . '</b> posted by <b>' . $emp_fname . '</b> rate get changed as per timeline set:</h3>';
                $emp_sub = 'Job (' . $job_title_e . ') Rate increase to ' . set_amount_format($job_rate_new);


                $message = $header . '
					<div style="padding: 25px 50px 5px; text-align: left;">
					' . $empMsg . '
					<table width="100%" style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;">
		    		  <tr>
						<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Title</th>
						<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job_title_e . '</td>
					  </tr>
					  <tr>
						<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Start Date</th>
						<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job_date_e . '</td>
					  </tr>
					  <tr>
						<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Rate Offered</th>
						<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . set_amount_format($job_rate_new) . '</td>
					  </tr>
					</table><br/>
					<p>Please note that you can edit your job by accessing the &#34;manage job&#34; option and clicking on “edit.”<p>
					</div>' . $footer;


                $emp_store_address     = $result_job_data->job_store['store_address'];

                $job_id         = $result_job_data['id'];
                $job_rate         = set_amount_format($result_job_data['job_rate']);
                $job_date         = get_date_with_default_format($result_job_data["job_date"]);
                $job_post_desc     = $result_job_data['job_post_desc'];


                $job_store_address = $result_job_data->job_address . ", " . $result_job_data->job_region . ", " . $result_job_data->job_zip;

                //Store timing for posted day
                $store_start_time = $result_job_data->get_store_start_time();
                $store_end_time = $result_job_data->get_store_finish_time();
                $store_lunch_time = $result_job_data->get_store_lunch_time();

                $store_contact_details = $employer->telephone;
                if ($store_contact_details == "") {
                    $store_contact_details = $employer->mobile;
                }

                // eamil variables
                $freelancer_email_section1 = '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%">
					  <tr>
						<th style=" border: 1px solid black;  text-align:left;  padding:5px;background-color:#2DC9FF;" colspan="2">LocumKit Job Invitation (Key Details)</th>
					  </tr>
					  <tr>
						<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
						<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job_date . '</td>
					  </tr>
					  <tr>
						<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Daily Rate</th>
						<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job_rate . '</td>
					  </tr>
					  <tr>
						<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Contact Details</th>
						<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_contact_details . '</td>
					  </tr>
					  <tr>
						<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Address</th>
						<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $emp_store_address . '</td>
					  </tr>
					  <tr>
						<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Additional Booking Info</th>
						<td style=" border: 1px solid black;  text-align:left;  padding:5px;color:red; font-weight:bold;">' . $job_post_desc . '</td>
					  </tr>
					  </table>';

                $email_data_employer = "";
                foreach ($employer->user_answers as $user_answer) {
                    $answer_value = json_decode($user_answer->type_value) ? join(" / ", json_decode($user_answer->type_value)) : $user_answer->type_value;

                    $email_data_employer .= '
							<tr>
								<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">' . $user_answer->question->freelancer_question . '</th>
								<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $answer_value . '</td>
							</tr>
						';
                }
                $email_data_employer .= '
					<tr>
						<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store cancellation percentage</th>
						<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $employer_cancellation_rate . '</td>
					</tr>
					<tr>
						<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store feedback percentage</th>
						<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $employer_feedback_average . '</td>
					</tr>
				';

                $invitedUserArray = JobInvitedUser::with("invited_user")->where("job_post_id", $parant_job_id)->get();

                foreach ($invitedUserArray as $invitedUser) {
                    if ($invitedUser->invited_user_type == JobInvitedUser::USER_TYPE_LIVE) {
                        $this->sendLiveUserNewInvitation($invitedUser->invited_user, $result_job_data, $email_data_employer, $store_start_time, $store_end_time, $store_lunch_time, $job_rate_new, $header, $footer, $freelancer_email_section1, $job_store_address);
                    } else {
                        $this->sendPrivateUserNewInvitation($invitedUser->invited_user, $result_job_data, $email_data_employer, $store_start_time, $store_end_time, $store_lunch_time, $job_rate_new, $header, $footer, $freelancer_email_section1, $job_store_address);
                    }
                }

                //Employer notification
                try {
                    Mail::html($message, function (Message $message) use ($emp_email, $emp_sub) {
                        $message->to($emp_email)->subject($emp_sub);
                    });
                    $this->notifyController->notification($job_id, "As per your posting we have increased the rate for the following job: Job Ref: " . $job_id . ', Date: ' . $job_date . ', Location: ' . $emp_store_address . ', Revised rate:' . $job_rate . '. Open this message to view full details.', $title = 'Rate increase notification', $employer->id, $types = "");
                } catch (Exception $e) {
                }

                //Admin Notification
                try {
                    Mail::html($message, function (Message $message) {
                        $message->to(config('app.admin_mail'))->subject('Locumkit:Job Rate Changed');
                    });
                } catch (Exception $e) {
                }
            }
        }

        JobPostTimeline::whereIn("id", $job_timeline_update_list)->update([
            "job_timeline_status" => 1
        ]);
    }

    private function sendLiveUserNewInvitation(User $freelancer, $job, $email_data_employer, $store_start_time, $store_end_time, $store_lunch_time, $job_rate_new, $header, $footer, $freelancer_email_section1, $job_store_address)
    {
        $encrypted_job_id = encrypt($job->id);
        $encrypted_freelancer_id = encrypt($freelancer->id);
        $encrypted_freelancer_type = encrypt("live");
        $accept_href_link = url("/accept-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");
        $freeze_href_link = url("/freeze-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");

        $link = '<a href="' . $accept_href_link . '" style=" float: left;  margin-bottom: 15px;  margin-top: -10px;"><img src="' . url('/frontend/images/accept.png') . '"/></a> <p style="float: left; margin: 13px; font-size: 20px;">OR &nbsp;</p>
			<a href="' . $freeze_href_link . '" style=" float: left;"><img src="' . url('/frontend/images/freez.png') . '"/></a>';


        $freelancer_email_section2 = '<tr>
			<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Start Time:</th>
			<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_start_time . '</td>
			</tr>
			<tr>
			<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Finish Time:</th>
			<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_end_time . '</td>
			</tr>
			<tr>
			<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Lunch Break (minutes):</th>
			<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_lunch_time . '</td>
			</tr>' . $email_data_employer;

        $email_freelancer_data = '';
        foreach ($freelancer->user_answers as $user_answer) {
            $answer_value = json_decode($user_answer->type_value) ? join(" / ", json_decode($user_answer->type_value)) : $user_answer->type_value;

            $email_freelancer_data .= '
					<tr>
						<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">' . $user_answer->question->freelancer_question . '</th>
						<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $answer_value . '</td>
					</tr>
				';
        }


        $freelancer_email_section3 = '
		<tr>
		  <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">GOC Number:</th>
		  <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freelancer->user_extra_info->goc . '</td>
		  </tr>
		  <tr>
		  <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Opthalmic number (OPL):</th>
		  <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freelancer->user_extra_info->aoc_id . '</td>
		  </tr>';
        if ($freelancer->user_extra_info->aop != '') {
            $freelancer_email_section3 .= '<tr>
			  <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Insurance (AOP):</th>
			  <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freelancer->user_extra_info->aop . '</td>
		  </tr>';
        } elseif ($freelancer->user_extra_info->inshurance_company != '' && $freelancer->user_extra_info->inshurance_no != '') {
            $freelancer_email_section3 .= '<tr>
			  <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Insurance:</th>
			  <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . ucfirst($freelancer->user_extra_info->inshurance_company) . '-' . $freelancer->user_extra_info->inshurance_no . '</td>
			  </tr>
			<tr>
				<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Insurance expiry:</th>
				<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freelancer->user_extra_info->inshurance_renewal_date . '</td>
			  </tr>';
        }

        $freelancer_email_section3 .= $email_freelancer_data;


        $freelancer_email_section3_data = '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%">
					       <tr>
							<th style=" border: 1px solid black;  text-align:left; padding:5px;background-color:#2DC9FF;" colspan="2">LocumKit Job Invitation – Information you provided us
							</th>
						  </tr>
						  <tr>
							<th style=" border: 1px solid black;  text-align:left; padding:5px;color:red; font-weight:bold;text-align:center;" colspan="2">
							Please check the details below and advise us immediately if this information is incorrect
							</th>
						  </tr>
						' . $freelancer_email_section3 . '
						</table>';
        // freelancer and private user terms and condition
        $freelancer_email_section4 = get_locum_email_terms("#2dc9ff");

        $fname = $freelancer['firstname'] . ' ' . $freelancer['lastname'];
        $femail = $freelancer['email'];
        $fre_sub = 'Rate increase to ' . set_amount_format($job_rate_new);
        $message_free = $header . '
							<div style="padding: 25px 50px 5px; text-align: left;">
							<p>Hi ' . $fname . ',</p>
							<p>We would like to inform you that the rate for the job shown below has increased.</p>
							<h3>Job Information</h3>
							' . $freelancer_email_section1 . '
							<br/>
							<p>' . $link . '<p>
							<br/>
							<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%">
							   <tr>
								<th style=" border: 1px solid black;  text-align:left;  padding:5px; background-color:#2DC9FF;" colspan="2">LocumKit Job Invitation (additional information)</th>
							  </tr>
							' . $freelancer_email_section2 . '
							</table>
							<br/>
							' . $freelancer_email_section3_data . '
							<br/>
							' . $freelancer_email_section4 . '

							</div>' . $footer;
        try {

            Mail::html($message_free, function (Message $message) use ($femail, $fre_sub) {
                $message->to($femail)->subject($fre_sub);
            });
            $this->notifyController->notification($job->id, "The following jobs rate has increased: Job Ref: " . $job->id . ', Date: ' . get_date_with_default_format($job->job_date) . ', Location: ' . $job_store_address . ', Rate:' . $job_rate_new . '. Open this message to view full details.', $title = 'Rate increase notification', $freelancer['id'], $types = "acceptJob");
        } catch (Exception $e) {
        }
    }
    private function sendPrivateUserNewInvitation(PrivateUser $freelancer, $job, $email_data_employer, $store_start_time, $store_end_time, $store_lunch_time, $job_rate_new, $header, $footer, $freelancer_email_section1, $job_store_address)
    {
        $encrypted_job_id = encrypt($job->id);
        $encrypted_freelancer_id = encrypt($freelancer->id);
        $encrypted_freelancer_type = encrypt("private");
        $accept_href_link = url("/accept-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");

        $link = '<a href="' . $accept_href_link . '" style=" float: left;  margin-bottom: 15px;  margin-top: -10px;"><img src="' . url('/frontend/images/accept.png') . '"/></a> <p style="float: left; margin: 13px; font-size: 20px;">OR &nbsp;</p>';

        $freelancer_email_section2 = '<tr>
			<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Start Time:</th>
			<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_start_time . '</td>
			</tr>
			<tr>
			<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Finish Time:</th>
			<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_end_time . '</td>
			</tr>
			<tr>
			<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Lunch Break (minutes):</th>
			<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_lunch_time . '</td>
			</tr>' . $email_data_employer;

        $email_freelancer_data = '';

        $freelancer_email_section3 = '';


        $freelancer_email_section3 .= $email_freelancer_data;


        $freelancer_email_section3_data = '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%">
					       <tr>
							<th style=" border: 1px solid black;  text-align:left; padding:5px;background-color:#2DC9FF;" colspan="2">LocumKit Job Invitation – Information you provided us
							</th>
						  </tr>
						  <tr>
							<th style=" border: 1px solid black;  text-align:left; padding:5px;color:red; font-weight:bold;text-align:center;" colspan="2">
							Please check the details below and advise us immediately if this information is incorrect
							</th>
						  </tr>
						' . $freelancer_email_section3 . '
						</table>';
        // freelancer and private user terms and condition
        $freelancer_email_section4 = get_locum_email_terms("#2dc9ff");

        $fname = $freelancer['name'];
        $femail = $freelancer['email'];
        $fre_sub = 'Rate increase to ' . set_amount_format($job_rate_new);
        $message_free = $header . '
							<div style="padding: 25px 50px 5px; text-align: left;">
							<p>Hi ' . $fname . ',</p>
							<p>We would like to inform you that the rate for the job shown below has increased.</p>
							<h3>Job Information</h3>
							' . $freelancer_email_section1 . '
							<br/>
							<p>' . $link . '<p>
							<br/>
							<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%">
							   <tr>
								<th style=" border: 1px solid black;  text-align:left;  padding:5px; background-color:#2DC9FF;" colspan="2">LocumKit Job Invitation (additional information)</th>
							  </tr>
							' . $freelancer_email_section2 . '
							</table>
							<br/>
							' . $freelancer_email_section3_data . '
							<br/>
							' . $freelancer_email_section4 . '

							</div>' . $footer;
        try {

            Mail::html($message_free, function (Message $message) use ($femail, $fre_sub) {
                $message->to($femail)->subject($fre_sub);
            });
        } catch (Exception $e) {
        }
    }
}
