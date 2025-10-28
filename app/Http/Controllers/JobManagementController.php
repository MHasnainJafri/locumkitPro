<?php

namespace App\Http\Controllers;

use App\Helpers\AppNotificationHelper;
use App\Helpers\JobMailHelper;
use App\Helpers\JobSmsHelper;
use App\Mail\FreelancerJobInvitationMail;
use App\Mail\JobNegotiateMail;
use App\Models\FinanceEmployer;
use App\Models\JobAction;
use App\Models\JobCancelation;
use App\Models\JobInvitedUser;
use App\Models\JobOnDay;
use App\Models\JobPost;
use App\Models\JobReminder;
use App\Models\PrivateUser;
use App\Models\PrivateUserJobAction;
use App\Models\User;
use App\Notifications\CancelJobAdminNotification;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class JobManagementController extends Controller
{
    private AppNotificationHelper $notifyController;
    private JobSmsHelper $jobsmsController;
    private JobMailHelper $mailController;

    public function __construct()
    {
        $this->notifyController =  new AppNotificationHelper();
        $this->mailController =  new JobMailHelper();
        $this->jobsmsController =  new JobSmsHelper();
    }
    public function sendJobInvitation(Request $request, $id)
    {
        $request->validate([
            "checkinvite" => "nullable|array",
            "checkinvitep" => "nullable|array"
        ]);
        $employer_answers = Auth::user()->user_answers;
        $live_freelancer_ids = $request->input("checkinvite", []);
        $private_freelancer_ids = $request->input("checkinvitep", []);
        if (sizeof($live_freelancer_ids) == 0 && sizeof($private_freelancer_ids) == 0) {
            return back()->with("error", "Please select someone to send invitation");
        }
        $live_freelancer_ids = array_values(array_unique($live_freelancer_ids));
        $private_freelancer_ids = array_values(array_unique($private_freelancer_ids));
        $job = JobPost::findOrFail($id);
        if ($job->is_invitation_sent) {
            return back()->with("error", "Job invitation has already been sent to freelancers.");
        }
        $job_employer_details = Auth::user()->user_extra_info;
        $store_contact_details = $job_employer_details->telephone ?? '';
        if ($store_contact_details == "") {
            $store_contact_details = $job_employer_details->mobile ?? '';
        }
        $total_freelancer_count = ($live_freelancer_ids && is_array($live_freelancer_ids) ? sizeof($live_freelancer_ids) : 0) + ($private_freelancer_ids && is_array($private_freelancer_ids) ? sizeof($private_freelancer_ids) : 0);
        $employer_cancellation_rate = get_job_cancellation_rate_by_user(Auth::user()->id, "employer");
        $employer_feedback_average = get_overall_feedback_rating_by_user(Auth::user()->id, "employer");

        $job_store_address = $job->job_address . ", " . $job->job_region . ", " . $job->job_zip;                    

        $job_timeline_data = "";
        foreach ($job->job_post_timelines as $timeline) {
            $job_timeline_data .= '<p><strong>Date:</strong> ' . get_date_with_default_format($timeline->job_date_new) . ' <strong>Rate:</strong> ' . set_amount_format($timeline->job_rate_new) . '</p>';
        }
        if ($job_timeline_data === "") {
            $job_timeline_data = "N/A";
        }

        $freelancer_email_section1 = '
            <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px;background-color:#2DC9FF;color:#fff;" colspan="2"> Locumkit Job invitation (Key Details)</th>
                </tr>
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . get_date_with_default_format($job->job_date) . '</td>
                </tr>
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Daily Rate</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . set_amount_format($job->job_rate) . '</td>
                </tr>
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Contact Details</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_contact_details . '</td>
                </tr>
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Address</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job_store_address . '</td>
                </tr>
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Additional Booking Info</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;color:red; font-weight:bold;">' . $job->job_post_desc . '</td>
                </tr>
			</table>
        ';

        $admin_email_section1 = '
            <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%">
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px;background-color:#2DC9FF;color:#fff;" colspan="2"> Locumkit job invitation (Key Details)</th>
                </tr>
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Employer</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . Auth::user()->firstname . ' ' .  Auth::user()->lastname . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Employer ID</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . Auth::user()->id . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job ref</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->id . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . get_date_with_default_format($job->job_date) . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Daily Rate</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . set_amount_format($job->job_rate) . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Increase rate timeline</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job_timeline_data . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Contact Details</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_contact_details . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Address</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job_store_address . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Additional Booking Info</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;color:red; font-weight:bold;">' . $job->job_post_desc . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date posted</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . get_date_with_default_format($job->created_at) . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Number of people sent to</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $total_freelancer_count . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;"></th>
                <td style=" border: 1px solid black;  text-align:left;">
                    <table style="text-align:left;" width="100%">
                    <tr>
                    <td width="50%" style="border-right:1px solid black;">SMS SEND : 0 </td>
                    <td style="margin-left: 10px; display: block;">EMAIL SEND : ' . $total_freelancer_count . '</td>
                    </tr>
                    </table>
                </td>
                </tr>
			</table>
        ';

        $employer_email_section1 = '
            <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; background-color:#2DC9FF;color:#fff;" colspan="2"> Locumkit job invitation (Key Details)</th>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job ref</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->id . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . get_date_with_default_format($job->job_date) . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Daily Rate</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . set_amount_format($job->job_rate) . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Increase rate timeline</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job_timeline_data . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Contact Details</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_contact_details . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Address</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job_store_address . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Additional Booking Info</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;color:red; font-weight:bold;">' . $job->job_post_desc . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date posted</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . get_date_with_default_format($job->created_at) . '</td>
                </tr>
			</table>
        ';

        $email_data_employer = "";
        foreach ($employer_answers as $user_answer) {
           // dd($user_answer);
$decoded = json_decode($user_answer->type_value, true);
$answer_value = is_array($decoded) ? implode(" / ", $decoded) : $user_answer->type_value;

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

        
        if ($live_freelancer_ids && sizeof($live_freelancer_ids) > 0) {
            $job_invited_users_insert_data = [];
            $job_action_insert_data = [];
            
            $freelancers = User::with("user_answers", "user_acl_package")->whereIn("id", $live_freelancer_ids)->where("user_acl_role_id", 2)->get();
            foreach ($freelancers as $freelancer) {
                $email_freelancer_data = '';
                foreach ($freelancer->user_answers as $user_answer) {
                    // $answer_value = json_decode($user_answer->type_value) ? join(" / ", json_decode($user_answer->type_value)) : $user_answer->type_value;
                    $decoded_value = json_decode($user_answer->type_value, true);
                    $answer_value = is_array($decoded_value) ? join(" / ", $decoded_value) : $user_answer->type_value;
                    $email_freelancer_data .= '
                        <tr>
                            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">' . $user_answer->question->freelancer_question . '</th>
                            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $answer_value . '</td>
                        </tr>
                    ';
                }

                $freelancer_email_section2 = '
                    <tr>
					    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Start Time:</th>
					    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_start_time() . '</td>
                    </tr>
                    <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Finish Time:</th>
                        <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_finish_time() . '</td>
                    </tr>
                    <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Lunch Break (minutes):</th>
                        <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_lunch_time() . '</td>
                    </tr>
                ' . $email_data_employer;

                $freelancer_email_section3 = '
                    <tr>
					  <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">GOC Number:</th>
					  <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freelancer?->user_extra_info?->goc . '</td>
				  	</tr>
				  	<tr>
					  <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Opthalmic number (OPL):</th>
					  <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freelancer?->user_extra_info?->aoc_id . '</td>
				  	</tr>';
                if ($freelancer?->user_extra_info?->aop != '') {
                    $freelancer_email_section3 .= '<tr>
						  <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Insurance (AOP):</th>
						  <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freelancer?->user_extra_info?->aop . '</td>
					  </tr>';
                } elseif ($freelancer?->user_extra_info?->inshurance_company != '' && $freelancer?->user_extra_info?->inshurance_no != '') {
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
                
                $job_action_insert_data[] = [
                    "job_post_id" => $job->id,
                    "action" => '0',
                    "freelancer_id" => $freelancer->id,
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
                $job_invited_users_insert_data[] = [
                    "job_post_id" => $job->id,
                    "invited_user_id" => $freelancer->id,
                    "invited_user_type" => JobInvitedUser::USER_TYPE_LIVE,
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
                
                $encrypted_job_id = encrypt($job->id);
                $encrypted_freelancer_id = encrypt($freelancer->id);
                $encrypted_freelancer_type = encrypt("live");
                $accept_href_link = url("/accept-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");
                $negotiate_href_link = url("/negotiate/freelancer-negotiate-on-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");
                
                // $link = '<a href="' . $accept_href_link . '" style="float: left;  margin-bottom: 15px;  margin-top: -10px;outline: none !important;border-radius: 25px;float: left;margin-bottom: 15px;font-size: 18px;color: #fff;background-color: #2dc9ff;padding: 10px 35px;text-decoration: none;text-transform: uppercase;font-weight: 500;">Accept</a>';
                // $negotiate_link = '<a href="' . $negotiate_href_link . '" style="float: left;  margin-bottom: 15px;  margin-top: -10px;outline: none !important;border-radius: 25px;float: left;margin-bottom: 15px;font-size: 18px;color: #fff;background-color: #2dc9ff;padding: 10px 35px;text-decoration: none;text-transform: uppercase;font-weight: 500;">Negotiate on Rate</a>';
                
                // $can_user_freeze_job = can_user_package_has_privilege($freelancer, 'job_freeze');
                // if (today()->addDays(2)->lessThan($job->job_date) && $can_user_freeze_job) {
                //     $freeze_href_link = url("/freeze-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");
                //     $link .= ' <p style="float: left; margin: 13px; font-size: 20px;"> OR &nbsp; </p> <a style="outline: none !important;border-radius: 25px;float: left;margin-bottom: 22px;font-size: 18px;color: #fff;background-color: #2dc9ff;padding: 10px 35px;text-decoration: none;text-transform: uppercase;font-weight: 500;" href="' . $freeze_href_link . '">Freeze</a>';
                // }

                $link = '<a href="' . $accept_href_link . '" style="display: inline-block; margin: 0 10px; border-radius: 25px; font-size: 18px; color: #fff; background-color: #2dc9ff; padding: 10px 35px; text-decoration: none; text-transform: uppercase; font-weight: 500;">Accept</a>';
                $negotiate_link = '<a href="' . $negotiate_href_link . '" style="display: inline-block; margin: 0 10px; border-radius: 25px; font-size: 18px; color: #fff; background-color: #2dc9ff; padding: 10px 35px; text-decoration: none; text-transform: uppercase; font-weight: 500;">Negotiate on Rate</a>';
                
                $can_user_freeze_job = can_user_package_has_privilege($freelancer, 'job_freeze');
                
                if (today()->addDays(2)->lessThan($job->job_date) && $can_user_freeze_job) {
                    $freeze_href_link = url("/freeze-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");
                    $link .= ' <span style="margin: 0 10px; font-size: 20px;">OR</span> <a href="' . $freeze_href_link . '" style="display: inline-block; margin: 0 10px; border-radius: 25px; font-size: 18px; color: #fff; background-color: #2dc9ff; padding: 10px 35px; text-decoration: none; text-transform: uppercase; font-weight: 500;">Freeze</a>';
                }


                if ($freelancer_email_section3 != '') {
                    $freelancer_email_section3_data = '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%">
					       <tr>
							<th style=" border: 1px solid black;  text-align:left; padding:5px;background-color:#2DC9FF;color:#fff;" colspan="2"> Locumkit job invitation – information you provided us
							</th>
						  </tr>
						  <tr>
							<th style=" border: 1px solid black;  text-align:left; padding:5px;color:red; font-weight:bold;text-align:center;" colspan="2">
							Please check the details below and advise us immediately if this information is incorrect
							</th>
						  </tr>
						' . $freelancer_email_section3 . '
						</table>';
                }

                $mail_body = '
                    <div style="padding: 25px 50px 5px; text-align: left; ">
                    <p>Hi ' . $freelancer->firstname . ',</p>
                    <p>We would like to inform you that a new job that matches your requirements has been posted. You can see the job details below:</p>
                    <h3>Job Information</h3>
                    ' . $freelancer_email_section1 . '
                    <br/>
                    <p>' . $link . '<p>
                    <br/>
                    <p>' . $negotiate_link . '<p>
                    <br/>

                    <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%">
                        <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; background-color:#2DC9FF;color:#fff;" colspan="2">Locumkit job invitation (additional information)</th>
                        </tr>
                    ' . $freelancer_email_section2 . '
                    </table>
                    <br/>
                    ' . $freelancer_email_section3_data . '
                    <br/>
                    ' . get_locum_email_terms() . '
                    </div>
                ';

                $mail_subject = 'Locumkit job notification: Date : ' . get_date_with_default_format($job->job_date) . ' / Location : ' . $job_store_address . ' / Rate : ' . set_amount_format($job->job_rate);

                Mail::to($freelancer->email)->send(new FreelancerJobInvitationMail($mail_subject, $mail_body));

                $this->notifyController->notification($job->id, $message = "Job Ref:" . $job->id . ', Date:' . get_date_with_default_format($job->job_date) . ', Location:' . $job_store_address . ', Rate:' . set_amount_format($job->job_rate) . ', Open this message to view full details.', $title = 'Job invitation', $freelancer->id, $types = "acceptJob");
                $this->jobsmsController->jobInvitationFreeSms($freelancer, $job, $accept_href_link);
            }

            JobAction::insert($job_action_insert_data);
            JobInvitedUser::insert($job_invited_users_insert_data);
        }

        if ($private_freelancer_ids && sizeof($private_freelancer_ids) > 0) {
            $job_invited_users_insert_data = [];
            $job_action_insert_data = [];

            $freelancers = PrivateUser::whereIn("id", $private_freelancer_ids)->where("employer_id", Auth::user()->id)->get();
            foreach ($freelancers as $freelancer) {
                $job_action_insert_data[] = [
                    "employer_id" => Auth::user()->id,
                    "private_user_id" => $freelancer->id,
                    "job_post_id" => $job->id,
                    "status" => PrivateUserJobAction::ACTION_WAITING,
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
                $job_invited_users_insert_data[] = [
                    "job_post_id" => $job->id,
                    "invited_user_id" => $freelancer->id,
                    "invited_user_type" => JobInvitedUser::USER_TYPE_PRIVATE,
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
                $encrypted_job_id = encrypt($job->id);
                $encrypted_freelancer_id = encrypt($freelancer->id);
                $encrypted_freelancer_type = encrypt("private");
                $accept_href_link = url("/accept-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");

                $link = '<a style="outline: none !important;float: left;font-size: 18px;background-color: #2dc9ff;padding: 7px 30px;color: #fff;text-transform: uppercase;text-decoration: none;border-radius: 25px;margin-bottom: 0px;" href="' . $accept_href_link . '">Accept</a>';

                $private_freelancer_email_section2 = $email_data_employer;
                // <p>To continue receiving job notifications like these please <a href="' . url('/private-invitation') . '" target="_blank">click here</a></p>

                $mail_body = '
                    <div style="padding: 25px 50px 5px; text-align: left; ">
                        <p>Hello ' . $freelancer->name . ',</p>
                        <p>Locumkit is a platform that matches employers with locums, with no middleman involved.</p>
                        <p>To find out more about Locumkit, please <a href="https://www.youtube.com/watch?v=uM4Og3BxQm0" target="_blank">click here</a> </p>
                        <p>Our client is looking for a locum - please find below details for the day in question. To accept the job, please click on accept and we shall notify the employer, who can then close the job. </p>

                        <h3>Job Information</h3>
                        ' . $freelancer_email_section1 . '
                        <br/>
                        <p style="float:left;width:100%;">' . $link . '<p>

                        <br/>
                        <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%">
                        <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; background-color:#2DC9FF;color:#fff;" colspan="2"> Locumkit job invitation (additional information)</th>
                        </tr>
                        <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Start Time:</th>
                        <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_start_time() . '</td>
                        </tr>
                        <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Finish Time:</th>
                        <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_finish_time() . '</td>
                        </tr>
                        <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Lunch Break (minutes):</th>
                        <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_lunch_time() . '</td>
                        </tr>
                        ' . $private_freelancer_email_section2 . '
                        </table>
                        <br/>
                        ' . get_locum_email_terms() . '
                        <p>About Locumkit:</p>
                        <p>Locumkit is designed to connect employers with locums. Locumkit offers plenty of benefits, functions, and services that you will certainly find very useful. From a single location, you will be able to monitor your bookings, work history, financials, new job opportunities, and much more. </p>
                        <p>Locumkit not only puts you at the center of our focus, we field highly cable teams, with depth and experience of Optometry and Accounting, on every job. Locumkit is a  bespoke & innovative platform created and run by experienced optometrists over 25 years of first hand experience of which 15 years has been as locums with a range of employers from multiples, independents, to eye casualties and domiciliary. </p>
                        <p>In addition to that there are many other benefits of Locumkit such as: </p>
                        <ul>
                            <li><p>Get many more job bookings like this</p></li>
                            <li><p>Get job bookings tailored to your requirements; day rate, distance willing to travel</p></li>
                            <li><p>Get job reminders irrespective if from our website or "off website"</p></li>
                            <li><p>Upto date accounting - accessed from anywhere, anytime</p></li>
                            <li><p>Automated book keeping and all your statutory financial compliance taken care of</p></li>
                        </ul>

                        <p>Why not visit Locumkit and join the platform where you can have that many significant benefits and dramatically boost your job opportunities?</p>

                        <p>Please visit our website for more information <a href="' . url('/') . '">www.locumkit.com</a></p>

                    </div>
                ';

                $mail_subject = 'Locumkit job notification: Date : ' . get_date_with_default_format($job->job_date) . ' / Location : ' . $job_store_address . ' / Rate : ' . set_amount_format($job->job_rate);

                Mail::to($freelancer->email)->send(new FreelancerJobInvitationMail($mail_subject, $mail_body));

                $this->jobsmsController->jobInvitationFreeSms($freelancer, $job, $accept_href_link);
            }

            PrivateUserJobAction::insert($job_action_insert_data);
            JobInvitedUser::insert($job_invited_users_insert_data);
        }

        $job->is_invitation_sent = true;
        $job->save();

        //Sending emails to admin and employer

        $freelancer_email_section2 = '
            <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Start Time:</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_start_time() . '</td>
            </tr>
            <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Finish Time:</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_finish_time() . '</td>
            </tr>
            <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Lunch Break (minutes):</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_lunch_time() . '</td>
            </tr>
        ';
        $freelancer_email_section2 .= $email_data_employer;

        $admin_mail_body = '
            <div style="padding: 25px 50px 5px; text-align: left;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
                <p>Hello <b>Admin</b>,</p>
                <p>A new job has just been posted by: <b>' . Auth::user()->firstname . '</b></p>
                <h3>Job Information</h3>
                ' . $admin_email_section1 . '
                <br/>
                <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%">
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; background-color:#2DC9FF;color:#fff;" colspan="2"> Locumkit Job invitation (additional information)</th>
                    </tr>
                ' . $freelancer_email_section2 . '
                </table>
                <br/>
            </div>
        ';
        $cancel_job_href = url("/cancel-job?job_id={$job->id}");

        $employer_mail_subject = 'Locumkit: New job posting (#' . $job->id . ')';
        $employer_mail_body = '
            <div style="padding: 25px 50px 5px; text-align: left;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
                <p>Hello ' . Auth::user()->firstname . ',</p>
                <p>We would like to inform you that your job post has been confirmed and is now active. The selected locums have been notified.</p><p>You will be notified once a locum has accepted your booking.</p>
                <h3>Job Information</h3>
                ' . $employer_email_section1 . '
                <br/>
                <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%">
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; background-color:#2DC9FF;color:#fff;" colspan="2" > Locumkit job invitation (additional information)</th>
                    </tr>
                ' . $freelancer_email_section2 . '
                </table>
                <br/>
                
                <p>Should you need to cancel this job, please <a href="' . $cancel_job_href . '">click here</a>. </p>
            </div>
        ';
                // <p>Should you need to edit this job, please <a href="' . url('/employer/managejob/' . $job->id) . '">click here</a>.</p>
        // <p>Should you need to cancel this job, please <a href="' . url('/cancel-job/' . $job->id) . '">click here</a>.</p>
        $admin_mail_subject = 'Locumkit job notification: New job posting : #' . $job->id;

        Mail::to(config('app.admin_mail'))->send(new FreelancerJobInvitationMail($admin_mail_subject, $admin_mail_body));

        Mail::to(Auth::user()->email)->send(new FreelancerJobInvitationMail($employer_mail_subject, $employer_mail_body));

        $this->jobsmsController->jobInvitationemployerSms($job->employer, $job->id, null);

        return redirect()->route("employer.job-listing", ['sort_by' => 'job_date', 'order' => 'DESC'])->with("success", "Invitation send to freelancers");
    }

    public function acceptJob(Request $request)
    {
        
        try {
            $job_id = decrypt($request->query("job_id"));
            $freelancer_id = decrypt($request->query("freelancer_id"));
            $freelancer_type = decrypt($request->query("freelancer_type"));
        } catch (DecryptException $e) {
            return abort(404);
        }

        if (in_array($freelancer_type, ["live", "private"]) == false) {
            return abort(404);
        }


        $job = JobPost::findOrFail($job_id);
        $employer = $job->employer;

        if ($job->job_status == JobPost::JOB_STATUS_CANCELED) {
            return redirect()->route('freelancer.dashboard')->with('error', 'This job has been canceled and cannot be accepted.');
        }
        if ($job->job_status == JobPost::JOB_STATUS_DELETED) {
            return redirect()->route('freelancer.dashboard')->with('error', 'This job has been deleted and cannot be accepted.');
        }

        $success = null;
        $error = null;
        $freelancer = null;

        if ($freelancer_type == "live") {
            $freelancer = User::findOrFail($freelancer_id);
            if (Auth::check() && Auth::user()->id == $freelancer_id) {
                
                
                $is_available_on_date = $freelancer->is_available_on_date($job->job_date);
                $user_job_action = JobAction::where("freelancer_id", $freelancer_id)->where("job_post_id", $job_id)->first();
                // dd($job , $employer, $user_job_action , $is_available_on_date && $user_job_action);
                if ($is_available_on_date && $user_job_action) {
                    // dd(JobPost::JOB_STATUS_OPEN_WAITING, 'under development', $job, $user_job_action->action, JobAction::ACTION_APPLY);
                    switch (intval($job->job_status)) {
                        case JobPost::JOB_STATUS_OPEN_WAITING:
                            switch (intval($user_job_action->action)) {
                                case JobAction::ACTION_NONE:
                                    $this->updateJobToaccepted($job, $user_job_action, $employer, $freelancer);
                                    $success = "Job accepted successfully.";
                                    break;
                                case JobAction::ACTION_APPLY:
                                    $error = 'You have successfully applied for this job.';
                                    break;
                                case JobAction::ACTION_ACCEPT:
                                    $error = 'You have already accepted this job.';
                                    break;
                                case JobAction::ACTION_DONE:
                                    $error = 'This job is done.';
                                    break;
                                default:
                                    $error = 'Invalid action.';
                                    break;
                            }
                            break;
                        case JobPost::JOB_STATUS_CLOSE_EXPIRED:
                            $error = "Job is closed.";
                            break;
                        case JobPost::JOB_STATUS_DISABLED:
                            $error = "Employer no longer needs a locum for this day and hence has removed the posting.";
                            break;
                        case JobPost::JOB_STATUS_ACCEPTED:
                            if ($user_job_action->action == JobAction::ACTION_ACCEPT) {
                                $success = "You have already accepted this job.";
                            } else {
                                $error = "Sorry - this job is no longer available.";
                            }
                            break;
                        case JobPost::JOB_STATUS_FREEZED:
                            if ($user_job_action->action == JobAction::ACTION_FREEZE && $user_job_action->freeze_notification_count == 1) {
                                $this->updateJobToaccepted($job, $user_job_action, $employer, $freelancer);
                                $success = "Job accepted successfully.";
                            } else {
                                $error = "Thank you for your interest however this job is curently held by another locum - If it goes live again we shall notify you.";
                            }
                            break;
                        case JobPost::JOB_STATUS_DELETED:
                            $error = "Employer no longer needs a locum for this day and hence has removed the posting.";
                            break;
                        case JobPost::JOB_STATUS_CANCELED:
                            $error = "Employer no longer needs a locum for this day and hence has removed the posting.";
                            break;
                        default:
                            $error = "Sorry - this job is no longer available.";
                    }
                } else {
                    if ($user_job_action && $user_job_action->action == JobAction::ACTION_ACCEPT) {
                        $success = "You have already accepted this job.";
                    } else {
                        $error = "Sorry - this job is no longer available.";
                    }
                }
            } else {
                return redirect(route("login"))->with("error", "Please login first and then go to accept job link");
            }
        } else {
            $freelancer = PrivateUser::findOrFail($freelancer_id);
            $private_user_action = PrivateUserJobAction::query()->where("private_user_id", $freelancer_id)->where("job_post_id", $job_id)->first();
            if (is_null($private_user_action)) {
                return abort(404);
            }
            switch ($job->job_status) {
                case JobPost::JOB_STATUS_OPEN_WAITING:
                    switch ($private_user_action->status) {
                        case PrivateUserJobAction::ACTION_WAITING:
                            $this->updateJobToacceptedForPrivate($job, $private_user_action);
                            $success = "Job accepted successfully.";
                            break;
                        case PrivateUserJobAction::ACTION_APPLY:
                            $error = 'You have already apply for this job.';
                            break;
                        case PrivateUserJobAction::ACTION_ACCEPT:
                            $error = 'You have already accepted this job.';
                            break;
                        case PrivateUserJobAction::ACTION_DONE:
                            $error = 'This job is done.';
                            break;
                        default:
                            $error = 'Invalid action.';
                            break;
                    }
                    break;
                case JobPost::JOB_STATUS_CLOSE_EXPIRED:
                    $error = "Job is closed.";
                    break;
                case JobPost::JOB_STATUS_DISABLED:
                    $error = "Employer no longer needs a locum for this day and hence has removed the posting.";
                    break;
                case JobPost::JOB_STATUS_ACCEPTED:
                    if ($private_user_action->status == JobAction::ACTION_ACCEPT) {
                        $success = "You have already accepted this job.";
                    } else {
                        $error = "Sorry - this job is no longer available.";
                    }
                    break;
                case JobPost::JOB_STATUS_FREEZED:
                    $error = "Thank you for your interest however this job is curently held by another locum - If it goes live again we shall notify you.";
                    break;
                case JobPost::JOB_STATUS_DELETED:
                    $error = "Employer no longer needs a locum for this day and hence has removed the posting.";
                    break;
                case JobPost::JOB_STATUS_CANCELED:
                    $error = "Employer no longer needs a locum for this day and hence has removed the posting.";
                    break;
                default:
                    $error = "Sorry - this job is no longer available.";
            }
        }

        $store_contact_details = $employer->user_extra_info?->telephone;
        if (is_null($store_contact_details) || $store_contact_details == "") {
            $store_contact_details = $employer->user_extra_info?->mobile;
        }
        $mail_body = '
            <div style="padding: 25px 50px 5px; text-align: left; ">
                <p>Hello ' . $employer->firstname . ' '.$employer->lastname.',</p>
                <h3>Your Job has been Accepted by the Freelancer</h3>
                <br/>
                <p>Freelancer Name: '.$freelancer->login ?? $freelancer->name ?? $freelancer->firstname . ' ' . $freelancer->lastname.' </p>
                <br/>
                <p>Freelancer Id: '.$freelancer->id.' </p>
                <br/>
                <table style="border-collapse: collapse; border: 1px solid black; text-align:left; padding:5px;" width="100%">
                    <tr>
                        <th style="border: 1px solid black; text-align:left; padding:5px; background-color:#2DC9FF;color:#fff;" colspan="2">Locumkit job invitation (additional information)</th>
                    </tr>
                    <tr>
                        <th style="border: 1px solid black; text-align:left; padding:5px; width: 200px;">Job Date:</th>
                        <td style="border: 1px solid black; text-align:left; padding:5px;">' . $job->job_date . '</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid black; text-align:left; padding:5px; width: 200px;">Start Time:</th>
                        <td style="border: 1px solid black; text-align:left; padding:5px;">' . $job->get_store_start_time() . '</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid black; text-align:left; padding:5px; width: 200px;">Finish Time:</th>
                        <td style="border: 1px solid black; text-align:left; padding:5px;">' . $job->get_store_finish_time() . '</td>
                    </tr>
                    <tr>
                        <th style="border: 1px solid black; text-align:left; padding:5px; width: 200px;">Lunch Break (minutes):</th>
                        <td style="border: 1px solid black; text-align:left; padding:5px;">' . $job->get_store_lunch_time() . '</td>
                    </tr>
                </table>
                <br/>
                ' . get_locum_email_terms() . '
                <p>About Locumkit:</p>
                <p>Locumkit is designed to connect employers with locums. Locumkit offers plenty of benefits, functions, and services that you will certainly find very useful. From a single location, you will be able to monitor your bookings, work history, financials, new job opportunities, and much more.</p>
                <p>Locumkit not only puts you at the center of our focus, we field highly capable teams, with depth and experience of Optometry and Accounting, on every job. Locumkit is a bespoke & innovative platform created and run by experienced optometrists over 25 years of firsthand experience of which 15 years has been as locums with a range of employers from multiples, independents, to eye casualties and domiciliary.</p>
                <p>In addition to that there are many other benefits of Locumkit such as:</p>
                <ul>
                    <li><p>Get many more job bookings like this</p></li>
                    <li><p>Get job bookings tailored to your requirements; day rate, distance willing to travel</p></li>
                    <li><p>Get job reminders irrespective if from our website or "off website"</p></li>
                    <li><p>Up-to-date accounting - accessed from anywhere, anytime</p></li>
                    <li><p>Automated bookkeeping and all your statutory financial compliance taken care of</p></li>
                </ul>
                <p>Why not visit Locumkit and join the platform where you can have that many significant benefits and dramatically boost your job opportunities?</p>
                <p>Please visit our website for more information <a href="' . url('/') . '">www.locumkit.com</a></p>
            </div>
        ';
        
        $job_store_address = $job->job_address . ", " . $job->job_region . ", " . $job->job_zip;    
        $mail_subject = 'Locumkit Accept Job Notification ' . get_date_with_default_format($job->job_date) . ' / Location: ' . $job_store_address . ' / Rate: ' . set_amount_format($job->job_rate);
        
        Mail::send([], [], function ($message) use ($employer, $mail_subject, $mail_body) {
            $message->to($employer->email)
                ->subject($mail_subject)
                ->html($mail_body);
        });
        
        $employer_answers = $employer->user_answers;

        return view('shared.accept-job', compact('success', 'error', 'job', 'store_contact_details', 'employer_answers', 'freelancer_type', 'freelancer'));
    }

    public function negotiateOnJob(Request $request)
    {
       
        try {
            $job_id = decrypt($request->query("job_id"));
            $freelancer_id = decrypt($request->query("freelancer_id"));
            $freelancer_type = decrypt($request->query("freelancer_type"));
        } catch (DecryptException $e) {
            
            return abort(404);
        }

        if (in_array($freelancer_type, ["live", "private"]) == false) {
            return abort(404);
        }
        if (Auth::user()->id != $freelancer_id) {
            //  dd(auth::user()->id,'auth user',$freelancer_id);
            return abort(404);
        }

        $job = JobPost::where("id", $job_id)->where("job_status", JobPost::JOB_STATUS_OPEN_WAITING)->whereDate("job_date", ">=", today())->whereHas("job_actions", function ($query) use ($freelancer_id) {
            $query->where("freelancer_id", $freelancer_id);
        })->first();
        if (is_null($job)) {
            return redirect(route("freelancer.dashboard"))->with("error", "This Job is not available");
        }

        $job_action_for_freelancer = JobAction::where("freelancer_id", $freelancer_id)->where("job_post_id", $job->id)->first();
        if ($job_action_for_freelancer->is_negotiated) {
            return redirect(route("freelancer.dashboard"))->with("error", "You already negotiated for this job");
        }

        return view("freelancer.negotiate-on-job", compact("job"));
    }

    public function negotiateOnJobPost(Request $request, $job_id)
    {
        $freelancer = Auth::user();

        $job = JobPost::where("id", $job_id)->where("job_status", JobPost::JOB_STATUS_OPEN_WAITING)->whereDate("job_date", ">=", today())->whereHas("job_actions", function ($query) use ($freelancer) {
            $query->where("freelancer_id", $freelancer->id);
        })->first();
        if (is_null($job)) {
            return redirect(route("freelancer.dashboard"))->with("error", "This Job is not available");
        }
        $request->validate([
            "rate" => "required|numeric|min:{$job->job_rate}",
            "message" => "required"
        ]);

        $job_action_for_freelancer = JobAction::where("freelancer_id", $freelancer->id)->where("job_post_id", $job->id)->first();
        if ($job_action_for_freelancer->is_negotiated) {
            return redirect(route("freelancer.dashboard"))->with("error", "You already negotiated for this job");
        }

        $expected_rate = $request->input("rate");
        $freelancer_message = $request->input("message");
        $employer = $job->employer;

        $sent = Mail::to($employer->email)->send(new JobNegotiateMail($job, $freelancer, $employer, $expected_rate, $freelancer_message));
        $this->notifyController->notification($job->id, "Freelancer want to negotiate on job.", "Locumkit Job Negotiation. Rate expected {$expected_rate}", $employer->id, "negotiateJob");
        if ($sent) {
            $job_action_for_freelancer->is_negotiated = true;
            $job_action_for_freelancer->negotiation_rate = $expected_rate;
            $job_action_for_freelancer->negotiation_message = $freelancer_message;
            $job_action_for_freelancer->save();
            return redirect(route('freelancer.dashboard'))->with("success", "We notify the employer about your expected rate. If employer accept the offer we notify you.");
        }

        return redirect(route('freelancer.dashboard'))->with("error", "Some error occured during notification to employer. Please try again.");
    }

    public function acceptJobNegotiate(Request $request)
    {
        try {
            $job_id = decrypt($request->query("job_id"));
            $freelancer_id = decrypt($request->query("freelancer_id"));
            $job_expected_rate = decrypt($request->query("job_expected_rate"));
        } catch (DecryptException $e) {
            return abort(404);
        }

        $job = JobPost::where("id", $job_id)->where("employer_id", Auth::user()->id)
            ->where("job_status", JobPost::JOB_STATUS_OPEN_WAITING)->whereDate("job_date", ">=", today())
            ->whereHas("job_actions", function ($query) use ($freelancer_id) {
                $query->where("freelancer_id", $freelancer_id)->where("is_negotiated", true);
            })->first();

        if (is_null($job)) {
            return redirect(route('employer.dashboard'))->with("error", "Job not found or not avaible for negotiate.");
        }
        $user_job_action = JobAction::where("freelancer_id", $freelancer_id)->where("job_post_id", $job_id)->where("is_negotiated", true)->first();
        if (is_null($user_job_action)) {
            return redirect(route('employer.dashboard'))->with("error", "Job action is invalid.");
        }

        $freelancer = User::findOrFail($freelancer_id);

        $job->job_rate = $job_expected_rate;
        $job->save();


        $this->updateJobToaccepted($job, $user_job_action, $job->employer, $freelancer);

        return redirect(route('employer.dashboard'))->with("success", "Job rate updated and job status changed to accepted.");
    }

    public function updateJobToaccepted(JobPost $job, JobAction $user_job_action, User $employer, User $freelancer)
    {
        $job->job_status = JobPost::JOB_STATUS_ACCEPTED;
        $user_job_action->action = JobAction::ACTION_ACCEPT;
        JobReminder::create([
            "job_post_id" => $job->id,
            "employer_id" => $employer->id,
            "freelancer_id" => $freelancer->id,
            "job_date" => $job->job_date,
            "job_reminder_date" => $job->job_date->copy()->addDays(-1),
        ]);
        JobOnDay::create([
            "job_post_id" => $job->id,
            "employer_id" => $employer->id,
            "freelancer_id" => $freelancer->id,
            "job_date" => $job->job_date,
            "status" => JobOnDay::STATUS_NOT_ATTEND,
        ]);
        $job->save();
        $user_job_action->save();
        Log::info('this function is running in the accepted function');
        $this->mailController->sendAcceptMailToUser($job, $employer, $freelancer);
    }
    public function updateJobToacceptedForPrivate(JobPost $job, PrivateUserJobAction $user_job_action)
    {
        $job->job_status = JobPost::JOB_STATUS_ACCEPTED;
        $user_job_action->status = PrivateUserJobAction::ACTION_ACCEPT;
        $job->save();
        $user_job_action->save();
        
        $this->mailController->sendAcceptMailToPrivateUser($user_job_action->private_user, $job);
    }

    public function viewJobFreelancer($id)
    {
       
        $job = JobPost::where("id", $id)->whereHas("job_actions", function ($query) {
            $query->where("freelancer_id", Auth::user()->id);
        })->first();
        if ($job == null) {
            return abort(404);
        }
        $job->get_store_start_time();
        $store_contact_details = $job->employer->user_extra_info->mobile;
        if ($store_contact_details == null || empty($store_contact_details)) {
            $store_contact_details = $job->employer->user_extra_info->telephone;
        }

        $statusUrl = 0;
        $status = 0;
        $setTitle = "";
        $setTitle2 = "";
        $type_jst = "blue";
        $cssstyle = "";
        if ($job->job_status == 3) {
            $cssstyle = 'style="color: #7aae00"';
            $statusUrl = '/single-job?view=' . $job->job_id . '&action=disable';
            $status = 'Enable';
            $actionupdatemsg = "<div class='alert alert-danger'>Job status is Disable.</div>";
        } elseif ($job->job_status == 1) {
            $cssstyle = 'style="color: #ff0000"';
            $statusUrl = '/single-job?view=' . $job->job_id . '&action=enable';
            $status = 'Disable';
        }
        if ($job->job_status == 4) {
            $type_jst = "green";
        }
        if ($job->job_status == 2 || $job->job_status == 4) {
            $setTitle1 = "  Booking confirmation (Key Details)";
            $setTitle2 = "  Booking confirmation (additional information)";
            $setTitle3 = "  Booking Confirmation – Details of Locum booked for you";
        } else {
            $setTitle1 = "  Job Invitation (Key Details)";
            $setTitle2 = "  Job Invitation (additional information)";
            $setTitle3 = "  Job invitation – Details of Locum booked for you";
        }
        $job_status_html = match ($job->job_status) {
            1 => "<span style='color:green'>Enable</span>",
            2 => "Close",
            3 => "<span style='color:red'>Disable</span>",
            4 => "Accepted",
            5 => "Done",
            6 => "Freeze",
            default => ""
        };


        return view("shared.view-single-job", compact("job", "type_jst", "setTitle2", "job_status_html", "setTitle1", "store_contact_details"));
    }
    public function viewJobEmployer(Request $request)
    {
        $id = $request['view'];
        // $job = JobPost::where("id", $id)->whereHas("job_actions", function ($query) {
        //     $query->where("freelancer_id", Auth::user()->id);
        // })->first();
        $job = JobPost::where("id", $id)->first();
        if ($job == null) {
            return abort(404);
        }
        $job->get_store_start_time();
        // $store_contact_details = $job->employer->user_extra_info->mobile;
        $store_contact_details = $job->employer?->user_extra_info?->mobile;
        if ($store_contact_details == null || empty($store_contact_details)) {
            $store_contact_details = $job->employer->user_extra_info->telephone;
        }

        $statusUrl = 0;
        $status = 0;
        $setTitle = "";
        $setTitle2 = "";
        $type_jst = "blue";
        $cssstyle = "";
        if ($job->job_status == 3) {
            $cssstyle = 'style="color: #7aae00"';
            $statusUrl = '/single-job?view=' . $job->job_id . '&action=disable';
            $status = 'Enable';
            $actionupdatemsg = "<div class='alert alert-danger'>Job status is Disable.</div>";
        } elseif ($job->job_status == 1) {
            $cssstyle = 'style="color: #ff0000"';
            $statusUrl = '/single-job?view=' . $job->job_id . '&action=enable';
            $status = 'Disable';
        }
        if ($job->job_status == 4) {
            $type_jst = "green";
        }
        if ($job->job_status == 2 || $job->job_status == 4) {
            $setTitle1 = "  Booking confirmation (Key Details)";
            $setTitle2 = "  Booking confirmation (additional information)";
            $setTitle3 = "  Booking Confirmation – Details of Locum booked for you";
        } else {
            $setTitle1 = "  Job Invitation (Key Details)";
            $setTitle2 = "  Job Invitation (additional information)";
            $setTitle3 = "  Job invitation – Details of Locum booked for you";
        }
        $job_status_html = match ($job->job_status) {
            1 => "<span style='color:green'>Enable</span>",
            2 => "Close",
            3 => "<span style='color:red'>Disable</span>",
            4 => "Accepted",
            5 => "Done",
            6 => "Freeze",
            default => ""
        };


        return view("shared.view-single-job", compact("job", "type_jst", "setTitle2", "job_status_html", "setTitle1", "store_contact_details"));
    }

    public function cancelJobFreelancer($id)
    {
        $job = JobPost::where("id", $id)->whereHas("job_actions", function ($query) {
            $query->where("freelancer_id", Auth::user()->id);
        })->first();
        if ($job == null) {
            return abort(404);
        }
        if ($job->job_status != JobPost::JOB_STATUS_ACCEPTED) {
            return redirect(route('freelancer.job-listing'))->with("error", "Job status must be accepted to cancel the job");
        }
        $user_cancellation_rate = get_job_cancellation_rate_by_user(Auth::user()->id);
        $user_type = "freelancer";
        $form_post_action = "/{$user_type}/cancel-job/{$job->id}";

        return view("shared.cancel-job", compact("job", "user_cancellation_rate", "user_type", "form_post_action"));
    }

    public function cancelJobPostFreelancer(Request $request, $id)
    {
        $job = JobPost::where("id", $id)->whereHas("job_actions", function ($query) {
            $query->where("freelancer_id", Auth::user()->id)->where("action", JobAction::ACTION_ACCEPT);
        })->first();
        if ($job == null) {
            return abort(404);
        }
        if ($job->job_status != JobPost::JOB_STATUS_ACCEPTED) {
            return back()->with("error", "Job status must be accepted to cancel the job");
        }

        $cancel_reason = $request->input("cancel-reason");

        JobCancelation::create([
            "job_id" => $job->id,
            "user_id" => Auth::user()->id,
            "reason" => $cancel_reason,
            "cancel_by_user_type" => JobCancelation::CANCEL_BY_LIVE_FREELANCER,
        ]);
        $job->job_status = JobPost::JOB_STATUS_CANCELED;
        $job_action = JobAction::where("job_post_id", $job->id)->where("freelancer_id", Auth::user()->id)->where("action", JobAction::ACTION_ACCEPT)->first();
        if ($job_action) {
            $job_action->action = JobAction::ACTION_CANCEL_JOB_BY_FREELANCER;
            $job_action->save();
        }
        $job->save();
        
        $admin = User::where('user_acl_role_id', 1)->first();
        if ($admin) {
            $admin->notify(new CancelJobAdminNotification($job, Auth::user()->name, $cancel_reason));
        }

        $this->mailController->cancelJobByFreNotificationToFreelancer($job->employer, $job_action->freelancer, $job, $cancel_reason);
        $this->mailController->cancelJobByFreNotificationToEmployer($job_action->freelancer, $job->employer, $job, $cancel_reason, $job->job_relist);
        $this->mailController->cancelJobByFreNotificationToAdmin($job_action->freelancer, $job, $cancel_reason);

        FinanceEmployer::where("employer_id", $job->employer_id)->where("job_id", $job->id)->where("freelancer_id", Auth::user()->id)->delete();
        return redirect(route('freelancer.job-listing'))->with("success", "Job is cancelled");
    }

    public function cancelJobEmployer($id)
    {
        $job = JobPost::where("id", $id)->where("employer_id", Auth::user()->id)->first();
        if ($job == null) {
            return abort(404);
        }
        if ($job->job_status != JobPost::JOB_STATUS_ACCEPTED) {
            return redirect(route('freelancer.job-listing'))->with("error", "Job status must be accepted to cancel the job");
        }
        $user_cancellation_rate = get_job_cancellation_rate_by_user(Auth::user()->id);
        $user_type = "employer";
        $form_post_action = "/{$user_type}/cancel-job/{$job->id}";

        return view("shared.cancel-job", compact("job", "user_cancellation_rate", "user_type", "form_post_action"));
    }

    public function cancelJobPostEmployer(Request $request, $id)
    {
        $job = JobPost::where("id", $id)->where("employer_id", Auth::user()->id)->first();

        if ($job == null) {
            return abort(404);
        }

        $cancel_reason = $request->input("cancel-reason");

        JobCancelation::create([
            "job_id" => $job->id,
            "user_id" => Auth::user()->id,
            "reason" => $cancel_reason,
            "cancel_by_user_type" => JobCancelation::CANCEL_BY_EMPLOYER,
        ]);
        $job_action = JobAction::where("job_post_id", $job->id)->where("action", JobAction::ACTION_ACCEPT)->first();
        if ($job_action) {
            $job_action->action = JobAction::ACTION_CANCEL_ACCEPTED_JOB_BY_EMPLOYER;
            $job_action->save();
        } else {
            JobAction::where("job_post_id", $job->id)->update([
                "action" => JobAction::ACTION_CANCEL_OPEN_JOB_BY_EMPLOYER
            ]);
        }
        $job_private_action = PrivateUserJobAction::where("job_post_id", $job->id)->where("status", PrivateUserJobAction::ACTION_ACCEPT)->first();
        if ($job_private_action) {
            $job_private_action->status = PrivateUserJobAction::ACTION_CANCEL;
            $job_private_action->save();
        }
        $job->job_status = JobPost::JOB_STATUS_CANCELED;
        $job->save();
        if ($job_action) {
            $this->mailController->cancelJobByEmpNotificationToFreelancer($job_action->freelancer, $job, $cancel_reason);
            $this->mailController->cancelJobByEmpNotificationToEmployer($job->employer, $job_action->freelancer, $job, $cancel_reason);
            FinanceEmployer::where("employer_id", $job->employer_id)->where("job_id", $job->id)->where("freelancer_id", $job_action->freelancer_id)->delete();
        } else if ($job_private_action) {
            $this->mailController->cancelJobByEmpNotificationToPrivateFreelancer($job_private_action->private_user, $job, $cancel_reason);
            $this->mailController->cancelJobByEmpNotifyToEmployerIFPrivatefreelancer($job->employer, $job_private_action->private_user, $job, $cancel_reason);
        }
        $this->mailController->cancelJobByEmpNotificationToAdmin($job->employer, $job, $cancel_reason);
        $admin = User::where('user_acl_role_id', 1)->first();
        if ($admin) {
            $admin->notify(new CancelJobAdminNotification($job, Auth::user()->name, $cancel_reason));
        }


        return redirect(route('employer.job-listing'))->with("success", "Job is cancelled");
    }

    public function cancelJob(Request $request)
    {
        $job_id = $request->query("job_id");

        if (Auth::check() == false) {
            return redirect(route('login'))->with("error", "Please login first to cancel the job. After login click on link again, or cancel it from user dashboard");
        }

        $job = JobPost::findOrFail($job_id);
        if ($job->job_status != JobPost::JOB_STATUS_ACCEPTED) {
            // return "You can only cancel a job with status of accepted";
            return redirect()->route('employer.dashboard')->with('error', 'You can only cancel a job with a status of accepted');
        }

        if (Auth::check() && Auth::user()->user_acl_role_id == 2) {
            return $this->cancelJobFreelancer($job_id);
        }
        if (Auth::check() && Auth::user()->user_acl_role_id == 3) {
            return $this->cancelJobEmployer($job_id);
        }

        return abort(404);
    }

    public function freezeJob(Request $request)
    {
        try {
            $job_id = decrypt($request->query("job_id"));
            $freelancer_id = decrypt($request->query("freelancer_id"));
            $freelancer_type = decrypt($request->query("freelancer_type"));
        } catch (DecryptException $e) {
            return abort(404);
        }
        if (in_array($freelancer_type, ["live", "private"]) == false) {
            return abort(404);
        }

        if ($freelancer_type == "private") {
            return redirect(route('login'))->with("error", "You cannot freeze a job as private user. Create an account with us");
        }
        if (Auth::check() == false) {
            return redirect(route('login'))->with("error", "Please login with your account and then visit the link");
        }
        if (Auth::user()->id != $freelancer_id) {
            return redirect("/")->with("error", "You are not invited for this job");
        }
        $job = JobPost::findOrFail($job_id);
        $job_invited_user = JobInvitedUser::where("job_post_id", $job_id)->where("invited_user_id", $freelancer_id)->where("invited_user_type", JobInvitedUser::USER_TYPE_LIVE)->first();
        $user_job_action = JobAction::where("freelancer_id", $freelancer_id)->where("job_post_id", $job_id)->first();

        if (is_null($job_invited_user) || is_null($user_job_action)) {
            return redirect("/")->with("error", "You are not invited for this job");
        }
        $employer = $job->employer;

        $success = null;
        $error = null;

        //freezing job
        $freelancer = User::findOrFail($freelancer_id);
        switch ($job->job_status) {
            case JobPost::JOB_STATUS_OPEN_WAITING:
                if ($user_job_action->freeze_notification_count < 1) {
                    if (today()->lessThan($job->job_date)) {
                        JobAction::where("job_post_id", $job->id)->where("id", "!=", $user_job_action->id)->update([
                            "action" => JobAction::ACTION_WAITING_FOR_UNFREEZE,
                            "updated_at" => now()
                        ]);
                        JobAction::where("id", $user_job_action->id)->update([
                            "action" => JobAction::ACTION_FREEZE,
                            "freeze_notification_count" => 1,
                            "updated_at" => now()
                        ]);
                        $job->job_status = JobPost::JOB_STATUS_FREEZED;
                        $job->save();
                        $success = "Job will be frozen for 15 minutes only";
                    } else {
                        $error = "Job is no longer available. You cannot freeze the job.";
                    }
                } else {
                    $error = "You have already freeze this job, you cannot freeze it again.";
                }
                break;
            case JobPost::JOB_STATUS_CLOSE_EXPIRED:
                $error = "Job is closed.";
                break;
            case JobPost::JOB_STATUS_DISABLED:
                $error = "Employer no longer needs a locum for this day and hence has removed the posting.";
                break;
            case JobPost::JOB_STATUS_ACCEPTED:
                if ($user_job_action->action == JobAction::ACTION_ACCEPT) {
                    $success = "You have already accepted this job.";
                } else {
                    $error = "Sorry - this job is no longer available.";
                }
                break;
            case JobPost::JOB_STATUS_FREEZED:
                if ($user_job_action->action == JobAction::ACTION_FREEZE && $user_job_action->freeze_notification_count >= 1) {
                    $success = "You already freezed this job.";
                } else {
                    $error = "Thank you for your interest however this job is curently held by another locum - If it goes live again we shall notify you.";
                }
                break;
            case JobPost::JOB_STATUS_DELETED:
                $error = "Employer no longer needs a locum for this day and hence has removed the posting.";
                break;
            case JobPost::JOB_STATUS_CANCELED:
                $error = "Employer no longer needs a locum for this day and hence has removed the posting.";
                break;
            default:
                $error = "Sorry - this job is no longer available.";
        }

        $store_contact_details = $employer->user_extra_info->telephone;
        if (is_null($store_contact_details) || $store_contact_details == "") {
            $store_contact_details = $employer->user_extra_info->mobile;
        }

        $employer_answers = $employer->user_answers;

    return view('shared.freeze-job', compact('success', 'error', 'job', 'store_contact_details', 'employer_answers', 'freelancer_type', 'freelancer'));
    }
}
