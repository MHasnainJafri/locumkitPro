<?php

namespace App\Helpers;

use App\Models\FinanceExpense;
use App\Models\FinanceIncome;
use App\Models\FreelancerPrivateJob;
use App\Models\JobFeedback;
use App\Models\JobFeedbackDispute;
use App\Models\JobPost;
use App\Models\PrivateUser;
use App\Models\User;
use App\Models\UserAclPackage;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use App\Models\EmployerStoreList;
use App\Models\UserExtraInfo;

class JobMailHelper
{
    private AppNotificationHelper $notifyController;
    private JobSmsHelper $jobsmsController;
    public function __construct()
    {
        $this->notifyController = new AppNotificationHelper();
        $this->jobsmsController = new JobSmsHelper();
    }
    public function sendCloseJobNotification(JobPost $job, User $employer, string $viewJobLink)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();

        $empEmail   = $employer['email'];
        $empName  = $employer['firstname'] . ' ' . $employer['lastname'];
        $closeJobMsg = $header;
        $closeJobMsg .= '<div style="padding: 25px 50px 5px;text-align: left; font-family: sans-serif;"><p>Hello <b>' . $empName . '</b>,</p>';
        $closeJobMsg .= '<p>As no successful match was found, job no #' . $job->id . ' is now closed.</p>';
        $closeJobMsg .= '<p>If you find this is a regular occurrence, then please contact us and one of our assistance can look into why you could be struggling to obtain locums. </p>';
        $closeJobMsg .= '<p>Click below button to view your job.</p>';
        $closeJobMsg .= $viewJobLink;
        $closeJobMsg .= '</div>';
        $closeJobMsg .= $footer;

        $closeJobMsgAdmin = $header;
        $closeJobMsgAdmin .= '<div style="padding: 25px 50px 5px;text-align: left; font-family: sans-serif;"><p>Hello <b>Admin</b>,</p>';
        $closeJobMsgAdmin .= '<p>The following employers job has just expired.</p>';
        $closeJobMsgAdmin .= get_expired_job_info($job, $employer);
        $closeJobMsgAdmin .= '</div>';
        $closeJobMsgAdmin .= $footer;

        try {
            Mail::html($closeJobMsg, function (Message $message) use ($empEmail) {
                $message->to($empEmail)->subject('Job Post Expired');
            });
            $this->notifyController->notification($job->id, $message = "One of your job has just expired.", $title = 'Job Expired', $employer->id, $types = "");
        } catch (Exception $ignore) {
        }

        $adminEmail = config('app.admin_mail');
        try {
            Mail::html($closeJobMsgAdmin, function (Message $message) use ($adminEmail) {
                $message->to($adminEmail)->subject('LocumKit job expired');
            });
        } catch (Exception $ignore) {
        }
    }

    public function sendFeedbackNotification(JobPost $job, User $freelancer, User $employer, string $feedback_link_fre, string $feedback_link_emp, string $block_locum_link)
    {
        $adminEmail = config('app.admin_mail');
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $freEmail   = $freelancer['email'];
        $freName  = $freelancer['firstname'] . ' ' . $freelancer['lastname'];
        $empEmail   = $employer['email'];
        $empName  = $employer['firstname'] . ' ' . $employer['lastname'];
        $jobData  = $this->getJobInfo($job);
        $massageFre = $header;
        $massageFre .= '<div style="padding: 25px 50px 5px;text-align: left;">
                <p>Hi ' . $freName . ',</p>';
        $massageFre .= '<p>Hope you are well.</p>';
        $massageFre .= '<p>We are emailing you in regards to the following job.</p>';
        $massageFre .= $jobData;
        $massageFre .= '<p>We would like you to leave feedback for the employer about your day there.</p>';
        $massageFre .= '<p>This would help other Locums and also help improve clinical competition amongst users.</p>';
        $massageFre .= '<p>Please click here on below button to submit your valuable feedback.</p><br/>';
        $massageFre .= '<p>' . $feedback_link_fre . '</p>';
        $massageFre .= '</div>';
        $massageFre .= $footer;

        $massageEmp = $header;
        $massageEmp .= '<div style="padding: 25px 50px 5px;text-align: left;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
                <p>Hi ' . $empName . ',</p>';
        $massageEmp .= '<p>Hope you are well.</p>';
        $massageEmp .= '<p>We are emailing you in regards to the following job.</p>';
        $massageEmp .= $jobData;
        $massageEmp .= '<p>Please could you leave feedback for the locum on their day with you.</p>';
        $massageEmp .= '<p>This would help other employers when looking to hire locums.</p>';
        $massageEmp .= '<p>Please click below to submit your feedback.</p>';
        $massageEmp .= '<p>' . $feedback_link_emp . '</p>';
        $massageEmp .= $block_locum_link;
        $massageEmp .= '</div>';
        $massageEmp .= $footer;

        $jobDate = get_date_with_default_format($job->job_date);

        try {
            $sub = 'Feedback request for #' . $job->id;
            Mail::html($massageFre, function (Message $message) use ($freEmail, $sub) {
                $message->to($freEmail)->subject($sub);
            });

            $this->notifyController->notification($job->id, $message = "Please leave feedback for work carried out on Date :" . $jobDate . '. Open this message to leave the feedback.', $title = 'Feedback request', $freelancer->id, $types = "feedbackRequest");
        } catch (Exception $e) {
        }

        try {
            $sub = 'Feedback request for #' . $job->id;
            Mail::html($massageEmp, function (Message $message) use ($empEmail, $sub) {
                $message->to($empEmail)->subject($sub);
            });
            $this->notifyController->notification($job->id, $message = "Please leave feedback for work carried out on Date :" . $jobDate . '. Open this message to leave the feedback.', $title = 'Feedback request', $employer->id, $types = "feedbackRequest");
        } catch (Exception $e) {
        }
    }

    public function sendOnDayNotificationToFreelancer(JobPost $job,  User $freelancer, string $yesBtnLink)
    {
        /* Fetch record of job */
        $jobId  = $job['id'];
        $jobDate  = get_date_with_default_format($job['job_date']);
        $jobRate  = set_amount_format($job['job_rate']);
        $jobAddress = $job['job_address'] . ", " . $job['job_region'] . "-" . $job['job_zip'];

        $freEmail = $freelancer['email'];
        $freName = $freelancer['firstname'] . ' ' . $freelancer['lastname'];

        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $mail_css   = $header;
        $job_info = '
            <h3 style="text-align:left;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;"> Job Information </h3>
            <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
            <tr style="background-color: #f2f2f2;">
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job ID</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">#' . $jobId . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job date</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobDate . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Location</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobAddress . '</td>
            </tr>
            <tr style="background-color: #f2f2f2;">
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->job_store->store_name . '</td>
            </tr>
            <tr style="background-color: #f2f2f2;">
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job rate</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobRate . '</td>
            </tr>
          </table>
          </div>' . $footer . '</body></html>';
        $massageFre = $mail_css . '
            <div style="padding: 25px 50px 5px;text-align: left;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
              <p>Hi ' . $freName . ',</p>
            <h3 style="font-weight: normal;">Please confirm arrival for the below booking:</h3>
            <p>' . $yesBtnLink . '</p>
            <!----<p>The details of the work are as per below:</p>--->
            ' . $job_info;

        try {
            Mail::html($massageFre, function (Message $message) use ($freEmail) {
                $message->to($freEmail)->subject('LocumKit confirmation of arrival');
            });
            $this->notifyController->notification($job->id, $message = "Please open this message to confirm your arival for the day.", $title = 'Arrival confirmation', $freelancer->id, $types = "attendance");
        } catch (Exception $e) {
        }
    }

    /* Private Job On Day reminder notification mail */
    public function sendPrivateJobOnDayReminder($jobPvid, User $freelancer, $pEmpName, $pEmpEmail, $pJobTitle, $pJobRate, $pJobDate, $pJobLocation, $yesBtnLink)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $freEmail   = $freelancer['email'];
        $freName  = $freelancer['firstname'] . ' ' . $freelancer['lastname'];
        $pJobRate = set_amount_format($pJobRate);
        $privateJobMsg = $header;
        $privateJobMsg .= '<div style="padding: 25px 50px 5px;text-align: left; font-family: sans-serif;">
          <p>Hello <b>' . $freName . '</b>,</p>';
        $privateJobMsg .= '<h3 style="font-weight: normal;">Please confirm your arrival at work for the booking stated below:</h3>';
        $privateJobMsg .= '<p>' . $yesBtnLink . '</p>';
        $privateJobMsg .= '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;">
        <tr style="background-color: #f2f2f2;">
          <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Title</th>
          <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $pJobTitle . '</td>
        </tr>
        <tr>
          <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Date</th>
          <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' .\Carbon\Carbon::parse($pJobDate)->format('d-m-Y') . '</td>
        </tr>
        <tr style="background-color: #f2f2f2;">
          <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Rate</th>
          <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $pJobRate . '</td>
        </tr>
        <tr>
          <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Address</th>
          <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $pJobLocation . '</td>
        </tr>
      </table>';
        $privateJobMsg .= '</div>';
        $privateJobMsg .= $footer;
        /* Mail Send to employer */
        try {
            Mail::html($privateJobMsg, function (Message $message) use ($freEmail) {
                $message->to($freEmail)->subject('LocumKit private job confirmation of arrival');
            });
            $mobile_invitation_send = $this->notifyController->notification($jobPvid, $message = 'Please open this message to confirm your arival for the day.', $title = 'Arrival confirmation', $freelancer->id, $types = 'privateJobAttendance');
        } catch (Exception $e) {
        }
    }

    public function sendOnDayRemindertoprivateuser(PrivateUser $private_user, JobPost $job)
    {
        $jobDate  = get_date_with_default_format($job['job_date']);
        $jobRate  = set_amount_format($job['job_rate']);
        $jobAddress     = $job['job_address'] . ", " . $job['job_region'] . "-" . $job['job_zip'];
        $storeName  = $job->job_store->store_name;

        $freEmail   = $private_user['email'];
        $freName  = $private_user['name'];

        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $mail_css   = $header;
        $job_info = '
        <h3 style="text-align:left;"> Job Information </h3>
        <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;">
            <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Date</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobDate . '</td>
            </tr>
            <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Location</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobAddress . '</td>
            </tr>
            <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $storeName . '</td>
            </tr>
            <tr style="background-color: #f2f2f2;">
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Rate</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobRate . '</td>
            </tr>
        </table>';
        $massageFre = $mail_css . '
            <div style="padding: 25px 50px 5px;">
            <p>Hello <b>' . $freName . '</b>,</p>
            <p>We would like to remind you that you have a booking coming up. Please, see the summary of details below:</p>
            ' . $job_info . '
            <br/>
            </div>' . $footer . '</body></html>';
        $reminderSubject = 'Job attendance reminder';


        try {
            if ($freEmail) {
                Mail::html($massageFre, function (Message $message) use ($freEmail, $reminderSubject) {
                    $message->to($freEmail)->subject($reminderSubject);
                });
            }
        } catch (Exception $e) {
        }
    }

    public function sendExpenseNotification(JobPost|FreelancerPrivateJob|Builder $job, User $freelancer, $link, $type = null)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $freName  = $freelancer['firstname'] . ' ' . $freelancer['lastname'];
        $ferEmail = $freelancer['email'];

        $expenseMsg = $header;
        $expenseMsg .= '<div style="padding: 25px 50px 5px; text-align: left; font-family: sans-serif;">
          <p>Hi ' . $freName . ',</p>';
        $expenseMsg .= '<p>Please enter your expenses for the day (if any) by ' . $link . '</p>';
        $expenseMsg .= '</div>';
        $expenseMsg .= $footer;

        Mail::html($expenseMsg, function (Message $message) use ($ferEmail) {
            $message->to($ferEmail)->subject('Locumkit: Job expenses');
        });

        if ($type == 'private') {
            $this->notifyController->notification($job->id, $message = 'Please open this message to confirm your expenses for the day.', $title = 'Expense ', $freelancer->id, $types = "privateJobExpense");
        } else {
            $this->notifyController->notification($job->id, $message = 'Please open this message to confirm your expenses for the day.', $title = 'Expense', $freelancer->id, $types = "jobExpense");
        }
    }

    /* Expired membership */
    public function sendMembershipExpired($user_id)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $user = User::find($user_id);
        if (is_null($user)) {
            return;
        }
        $freEmail   = $user['email'];
        $freName  = $user['firstname'] . ' ' . $user['lastname'];

        $pkgMessage = $header;
        $pkgMessage .= '<div style="padding: 25px 50px 5px;text-align: left; font-family: sans-serif;">
          <p>Hello ' . $freName . ',</p>';
        $pkgMessage .= '<p>Sorry to say you that, your Locum account membership is expired today. You can not access website any more. </p>';
        $pkgMessage .= '<p>To resume the account please renew the membership by login to your account.</p>';
        $pkgMessage .= '</div>';
        $pkgMessage .= $footer;
        try {
            Mail::html($pkgMessage, function (Message $message) use ($freEmail) {
                $message->to($freEmail)->subject('User Account Membership Expired');
            });
        } catch (Exception $e) {
        }
    }

    private function getJobInfo(JobPost $job)
    {
        $jobTitle   = $job['job_title'];
        $jobDate  = get_date_with_default_format($job['job_date']);
        $jobRate  = set_amount_format($job['job_rate']);
        $jobAddress = $job['job_address'] . ", " . $job['job_region'] . "-" . $job['job_zip'];
        $jobDesc  = $job['job_post_desc'];

        $job_info = '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;">
          <tr style="background-color: #f2f2f2;">
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job title</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobTitle . '</td>
          </tr>
          <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job date</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobDate . '</td>
          </tr>
          <tr style="background-color: #f2f2f2;">
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job rate</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobRate . '</td>
          </tr>
          <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job address</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobAddress . '</td>
          </tr>
          <tr style="background-color: #f2f2f2;">
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job description</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobDesc . '</td>
          </tr>
        </table>';
        return $job_info;
    }

    /* On Day Notification mail To freelancer */
    public function sendOnDayNotificationToEmployer(JobPost $job, User $freelancer, User $employer, $yesBtnLink)
    {
        $jobId    = $job['id'];
        $jobDate  = get_date_with_default_format($job['job_date']);
        $jobRate  = set_amount_format($job['job_rate']);

        $EmpEmail = $employer['email'];
        $EmpName = $employer['firstname'] . ' ' . $employer['lastname'];
        $freName = $freelancer['firstname'] . ' ' . $freelancer['lastname'];

        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $mail_css   = $header;
        $job_info = '
        <h3 style="text-align:left;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;"> Job Information </h3>
        <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
          <tr style="background-color: #f2f2f2;">
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job ID</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">#' . $jobId . '</td>
          </tr>
          <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Date</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobDate . '</td>
          </tr>
          <tr style="background-color: #f2f2f2;">
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Locum</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freName . '</td>
          </tr>
          <tr style="background-color: #f2f2f2;">
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Rate</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobRate . '</td>
          </tr>
        </table>
      </div>' . $footer . '</body></html>';
        $massageEmp = $mail_css . '
        <div style="padding: 25px 50px 5px;text-align: left;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
          <p>Hi ' . $EmpName . ',</p>
        <h3 style="font-weight: normal;">This email is sent to you to confirm that your locum for today has just confirmed arrival for today. </h3>

        ' . $job_info;

        try {
            Mail::html($massageEmp, function (Message $message) use ($EmpEmail) {
                $message->to($EmpEmail)->subject('LocumKit confirmation of arrival');
            });
            $this->jobsmsController->sendOnDayNotificationToEmployerSms($employer, $jobId);

            $this->notifyController->notification($jobId, $message = "The locum for the day has just confirmed their attendance.", $title = 'LocumKit confirmation of arrival.', $employer->id, $types = "");
        } catch (Exception $e) {
        }
    }

    public function recievedFeedbackEmployerNotification(JobFeedback $feedback, JobPost $job, User $freelancer, User $employer)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $empEmail   = $employer['email'];
        $empName  = $employer['firstname'] . ' ' . $employer['lastname'];
        $freName  = $freelancer['firstname'] . ' ' . $freelancer['lastname'];
        $empId = $feedback['employer_id'];
        $jobId = $feedback['job_id'];
        $freId = $feedback['freelancer_id'];
        $averageRate = $feedback['rating'];
        $feedbackArray = json_decode($feedback['feedback'], true);

        $jobDate  = get_date_with_default_format($job['job_date']);
        $jobRate  = set_amount_format($job['job_rate']);

        $job_info = '
          <h3 style="text-align:left;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;"> Job Information </h3>
          <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
          <tr style="background-color: #f2f2f2;">
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job ID</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">#' . $jobId . '</td>
          </tr>
          <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job date</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobDate . '</td>
          </tr>

          <tr style="background-color: #f2f2f2;">
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Locum</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freName . '</td>
          </tr>
          <tr style="background-color: #f2f2f2;">
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job rate</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobRate . '</td>
          </tr>
          </table>';
        $feedbackQusAns = '';
        $i = 1;
        foreach ($feedbackArray as $key => $feedbackData) {
            $displaystars = $this->calculatestars($feedbackData['qusRate']);

            $feedbackQusAns .= '
              <div style="border: 1px solid #cfcfcf; padding: 10px;background: #eee;border-radius: 3px;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
                <p style="font-style: italic;font-weight: bold;padding:0 0 10px;">Qus ' . $i . ') ' . $feedbackData['qus'] . '</p>
                <p style="font-weight: bold;padding:0 0 10px;">Ans : ' . $displaystars . ' ' . $feedbackData['qusRate'] . ' star(s)  </p>
              </div>
              <div style="height:10px"></div>
            ';
            $i++;
        }

        $massageEmp = $header;
        $massageEmp .= '<div style="padding: 25px 50px 5px;text-align: left;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
              <p>Hi ' . $empName . ',</p>';
        $massageEmp .= '<p>We would like to inform you that you have received feedback for the following booking:</p>';
        $massageEmp .= $job_info;
        $massageEmp .= '<p>&nbsp;</p>';
        $massageEmp .= '<p>Below you can see the details of the feedback:</p>';
        $massageEmp .= $feedbackQusAns;
        $massageFre = "";
        if (isset($feedbackArray['comments']) && $feedbackArray['comments'] != '') {
            $massageFre .= '<p>Feedback Comment : ' . $feedbackArray['comments'] . '</p>';
        }

        $encrypted_feedback_id = encrypt($feedback->id);
        $encrypted_emp_id = encrypt($empId);
        $encrypted_user_type = encrypt("employer");
        $disputeHrefLink = url("/feedback-dispute?feedback_id={$encrypted_feedback_id}&user_id={$encrypted_emp_id}&user_type={$encrypted_user_type}");
        $massageEmp .= '<p>&nbsp;</p>';
        $massageEmp .= '<p style="float: left;padding: 0 10px 0 0;"><b>Average star rating: ' . $this->calculatestarsaverage($averageRate) . '</b></p>';
        $massageEmp .= '<p>&nbsp;</p>';
        $massageEmp .= '<p>If you feel this feedback is not a true reflection of your performance then please <a href="' . $disputeHrefLink . '">click here</a>, so we at LocumKit can look into this. </p>';
        $massageEmp .= '<p>If you are happy with this then this feedback shall automatically be posted against your profile within the next 48 hours.</p>';
        $massageEmp .= '</div>';
        $massageEmp .= $footer;
        try {
            $sub = 'Feedback received for ' . get_date_with_default_format(today()->subDay());
            Mail::html($massageEmp, function (Message $message) use ($empEmail, $sub) {
                $message->to($empEmail)->subject($sub);
            });
            $this->jobsmsController->recievedFeedbackEmployerNotificationSms($employer, $jobId, $disputeHrefLink);

            $this->notifyController->notification($feedback->id, $message = "You have recieved feedback for work on date:" . $jobDate . '. Open this message to view the results.', $title = 'Feedback recieved', $empId, $types = "feedbackRecieved");
        } catch (Exception $ignore) {
        }
    }

    private function calculatestars($rating)
    {
        $totalStar = 5;
        $ratingStar = $rating;
        $currentStar = 1;
        $showstar = '';
        while ($totalStar > 0) {
            if ($ratingStar >= $currentStar) {
                $starurl = url('/frontend/locumkit-template/img/star-rating-sprite_fill.png');
            } else {
                $starurl = url('/frontend/locumkit-template/img/star-rating-sprite_blank.png');
            }
            $showstar .= '<img src="' . $starurl . '" width="12px"/> ';
            $totalStar--;
            $currentStar++;
        }
        return $showstar;
    }

    private function calculatestarsaverage($avgrating)
    {
        $pre = ($avgrating * 2) * 10;
        $star =  '<div style="padding-top:8px"> <div style="background: url(' . url('/frontend/locumkit-template/img/star-rating-sprite.png') . ') repeat-x;
        font-size: 0;
        height: 21px;
        line-height: 0;
        overflow: hidden;
        text-indent: -999em;
        width: 110px;
        float: left;">
        <span style=" width:' . $pre . '% ;  background: url(' . url('/frontend/locumkit-template/img/star-rating-sprite.png') . ') repeat-x;
        background-position: 0 100%;
        float: left;
        height: 21px;
        display: block;"></span></div><div style="padding: 5px 0 0 0;"> &nbsp;&nbsp;' . $avgrating . ' star(s)</div></div>';
        return $star;
    }

    public function recievedFeedbackFreelancerNotification(JobFeedback $feedback, JobPost $job, User $freelancer)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $freEmail   = $freelancer['email'];
        $freName  = $freelancer['firstname'] . ' ' . $freelancer['lastname'];
        $freId = $feedback['freelancer_id'];
        $jobId = $feedback['job_id'];
        $averageRate = $feedback['rating'];
        $feedbackArray = json_decode($feedback['feedback'], true);

        $jobId    = $job['job_id'];
        $jobDate  = get_date_with_default_format($job['job_date']);
        $jobRate  = set_amount_format($job['job_rate']);
        $jobAddress = $job['job_address'] . ", " . $job['job_region'] . "-" . $job['job_zip'];
        $storeName  = $job->job_store->store_name;

        $job_info = '
          <h3 style="text-align:left;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;"> Job Information </h3>
          <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
          <tr style="background-color: #f2f2f2;">
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job ID</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">#' . $jobId . '</td>
          </tr>
          <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Date</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobDate . '</td>
          </tr>
          <tr>
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Location</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobAddress . '</td>
          </tr>
          <tr style="background-color: #f2f2f2;">
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $storeName . '</td>
          </tr>
          <tr style="background-color: #f2f2f2;">
            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Rate</th>
            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobRate . '</td>
          </tr>
          </table>';
        $feedbackQusAns = '';
        $i = 1;
        foreach ($feedbackArray as $key => $feedbackData) {
            $displaystars = $this->calculatestars($feedbackData['qusRate']);
            $feedbackQusAns .= '
              <div style="border: 1px solid #cfcfcf; padding: 10px;background: #eee;border-radius: 3px;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
                <p style="font-style: italic;font-weight: bold;padding:0 0 10px;">Qus ' . $i . ') ' . $feedbackData['qus'] . '</p>
                <p style="font-weight: bold;padding:0 0 10px;">Ans : ' . $displaystars . ' ' . $feedbackData['qusRate'] . ' star(s) </p>
              </div>
              <div style="height:10px"></div>
            ';
            $i++;
        }

        $massageFre = $header;
        $massageFre .= '<div style="padding: 25px 50px 5px;text-align: left;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
              <p>Hi ' . $freName . ',</p>';
        $massageFre .= '<p>We would like to inform you that you have received feedback for the following booking:</p>';
        $massageFre .= $job_info;
        $massageFre .= '<p>&nbsp;</p>';
        $massageFre .= '<p>Please see below how the employer has left feedback for you</p>';
        $massageFre .= $feedbackQusAns;

        if (isset($feedbackArray['comments']) && $feedbackArray['comments'] != '') {
            $massageFre .= '<p>Feedback Comment : ' . $feedbackArray['comments'] . '</p>';
        }

        $encrypted_feedback_id = encrypt($feedback->id);
        $encrypted_fre_id = encrypt($freId);
        $encrypted_user_type = encrypt("freelancer");
        $disputeHrefLink = url("/feedback-dispute?feedback_id={$encrypted_feedback_id}&user_id={$encrypted_fre_id}&user_type={$encrypted_user_type}");

        $massageFre .= '<p>&nbsp;</p>';
        $massageFre .= '<p style="float: left;padding: 0 10px 0 0;"><b>Average star rating: ' . $this->calculatestarsaverage($averageRate) . '</b></p>';
        $massageFre .= '<p>&nbsp;</p>';
        $massageFre .= '<p>If you feel this feedback is not a true reflection of your performance, please <a href="' . $disputeHrefLink . '">click here</a> and the LocumKit Team will look into the matter. If you are happy with the feedback, then it will be automatically posted on your profile in the next 48 hours </p>';

        $massageFre .= '<p>If you are happy with this then this feedback shall automatically be posted against your profile within the next 48 hours.</p>';
        $massageFre .= '</div>';
        $massageFre .= $footer;
        try {
            $sub = 'Feedback received for ' . get_date_with_default_format(today()->subDay());

            Mail::html($massageFre, function (Message $message) use ($freEmail, $sub) {
                $message->to($freEmail)->subject($sub);
            });
            $this->jobsmsController->recievedFeedbackFreelancerNotificationSms($freelancer, $jobId, $disputeHrefLink);

            $this->notifyController->notification($feedback->id, $message = "You have recieved feedback for work carried out on date:" . $jobDate . '. Open this message to view the results.', $title = 'Feedback recieved', $freId, $types = "feedbackRecieved");
        } catch (Exception $e) {
        }
    }


    public function sendAcceptMailToUser(JobPost $job, User $employer, User $freelancer)
    {
        /* Fetch record of job */
        $jobTitle   = $job['job_title'];
        $jobDate  = get_date_with_default_format($job['job_date']);
        $jobRate  = set_amount_format($job['job_rate']);
        $subject_jobRate  = $jobRate;
        $jobDesc  = $job['job_post_desc'];
        $jobEmpId   = $job['employer_id'];

        $empName  = $employer['firstname'] . " " . $employer['lastname'];
        $empEmail   = $employer['email'];

        //Current EMP cancellation percentage
        $employer_cancellation_rate = get_job_cancellation_rate_by_user($employer->id, "employer");
        $employer_feedback_average = get_overall_feedback_rating_by_user($employer->id, "employer");

        /*Get store job details*/
        $job_store_address = $job->job_address . ", " . $job->job_region . ", " . $job->job_zip;

        //Store timing for posted day
        $store_start_time = $job->get_store_start_time();
        $store_end_time = $job->get_store_finish_time();
        $store_lunch_time = $job->get_store_lunch_time();

        $freName     = $freelancer['firstname'] . " " . $freelancer['lastname'];
        $freEmail      = $freelancer['email'];
        $freID         = $freelancer['id'];
        $freprofession = $freelancer['user_acl_profession_id'];

        $store_contact_details = $employer->telephone ?? '';
        if ($store_contact_details == "") {
            $store_contact_details = $employer->mobile ?? 'N/A';
        }
        // $store_contact_details = $store_contact_details::where('id', $job->employer_id)->select('mobile')->first();
        $store_contact_details = UserExtraInfo::where('user_id', $job->employer_id)
            ->pluck('mobile')
            ->first();


        /* Get record of freelancer answer */
        $free_qu_ans = "";
        foreach ($freelancer->user_answers as $user_answer) {
$decoded = json_decode($user_answer->type_value, true);
$answer_value = is_array($decoded) ? implode(" / ", $decoded) : $user_answer->type_value;

            $free_qu_ans .= '
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">' . $user_answer->question->freelancer_question . '</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $answer_value . '</td>
                </tr>
            ';
        }

        /* Get record of employer answer */
        $emp_qu_ans = "";
        foreach ($employer->user_answers as $user_answer) {
$decoded = json_decode($user_answer->type_value, true);
$answer_value = is_array($decoded) ? implode(' / ', $decoded) : $user_answer->type_value;

            $emp_qu_ans .= '
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">' . $user_answer->question->freelancer_question . '</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $answer_value . '</td>
                </tr>
            ';
        }
        $emp_qu_ans .= '
            <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store cancellation percentage</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $employer_cancellation_rate . '</td>
            </tr>
            <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store feedback percentage</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $employer_feedback_average . '</td>
            </tr>
        ';

        $cancel_job_href = url("/cancel-job?job_id={$job->id}");

        /* Get record of freelancer */
        $freGoc      = $freelancer->user_extra_info['goc'];
        $freaop      = $freelancer->user_extra_info['aop'];
        $freaoc_id     = $freelancer->user_extra_info['aoc_id'];
        $freinsurance  = $freelancer->user_extra_info['inshurance_company'];
        $freinsuranceno = $freelancer->user_extra_info['inshurance_no'];
        $freinsurance_date  = $freelancer->user_extra_info['inshurance_renewal_date'];
        if ($freprofession == 3) {
            $fre_addinfo = '
                <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
                <tr style="background-color: #92D000;">
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2"> Locumkit job invitation - information you provided us </th>
                </tr>
                <tr>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px; color:red; font-weight:bold;" colspan="2">Please check the details below and advise us immediately if this information is incorrect</td>
                </tr>
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Goc</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freGoc . '</td>
                </tr>';
            if ($freaoc_id && $freaoc_id != '') {
                $fre_addinfo .= '<tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Opthalmic number (OPL):</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freaoc_id . '</td>
                </tr>';
            } else {
                $fre_addinfo .= '<tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Insurance:</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . ucfirst($freinsurance) . '-' . $freinsuranceno . '</td>
                    </tr>
                    <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Insurance expiry:</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freinsurance_date . '</td>
                </tr>';
            }
            $fre_addinfo .= $free_qu_ans . '</table><br>';
        } else {
            $fre_addinfo = '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
                <tr style="background-color: #92D000;">
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2"> LocumKit job invitation - information you provided us </td>
                    </tr>
                    <tr>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px; color:red; font-weight:bold;" colspan="2">Please check the details below and advise us immediately if this information is incorrect</td>
                </tr> ' . $free_qu_ans . '</table><br>';
        }

        $header   = get_mail_header();
        $footer   = get_mail_footer();

        $mail_css   = '
            <style type="text/css">
          table {
              border-collapse: collapse;
          }

          table, th, td {
              border: 1px solid black;
              text-align:left;
              padding:5px;
          }
          h3{
            text-align:left;
          }
          tr:nth-child(odd){
            background-color: #f2f2f2;
          }
          th{
            width: 200px;
          }

          .mail-job-info {
              padding: 25px 5px 30px;
          }
        </style>' . $header;
        $freelancer_terms = get_locum_email_terms("#92D000");
        $job_info_free = '
          <h3 style="text-align:left;"> Job Information </h3>
          <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
            <tr style="background-color: #92D000;">
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2"> Locumkit booking confirmation (Key Details)</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobDate . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Daily Rate</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobRate . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Contact Details</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_contact_details . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Address</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job_store_address . '</td>
            </tr>
            <tr style="background-color: #f2f2f2;">
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Additional Booking Info:</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; color:red;font-weight:bold;">' . $jobDesc . '</td>
            </tr>
          </table>
          <br>
          <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
            <tr style="background-color: #92D000;">
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2"> Locumkit booking confirmation (additional information) </td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Start Time</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_start_time . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Finish Time</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_end_time . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Lunch Break (minutes)</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_lunch_time . '</td>
            </tr>
            ' . $emp_qu_ans . '
          </table>
          <br>
          ' . $fre_addinfo . '

            <p><br/></p>
            <p>Should you need to cancel this job, please <a href="' . $cancel_job_href . '">click here</a>. </p>
            <p><br/></p>
          ' . $freelancer_terms . '
        </div>' . $footer . '</body></html>';

        $job_info_emp = '
          <h3 style="text-align:left;"> Job Information </h3>
          <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
            <tr style="background-color: #92D000;">
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2"> Locumkit booking confirmation (Key Details)</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobDate . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Daily Rate</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobRate . '</td>
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
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Additional Booking Info:</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; color:red; font-weight:bold;">' . $jobDesc . '</td>
            </tr>
          </table>
          <br>
          <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
            <tr style="background-color: #92D000;">
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2"> Locumkit booking confirmation - details of locum booked</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Name</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freName . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Id</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freID . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Goc</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freGoc . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Insurance</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freinsuranceno . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Insurance expiry</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freinsurance_date . '</td>
            </tr>
            ' . $free_qu_ans . '
          </table>
          <br>
          <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
            <tr style="background-color: #92D000;">
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2"> Locumkit booking confirmation (additional information)</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Start Time</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_start_time . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Finish Time</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_end_time . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Lunch Break (minutes)</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_lunch_time . '</td>
            </tr>
            ' . $emp_qu_ans . '
          </table>

            <p>Should you need to cancel this job, please <a href="' . $cancel_job_href . '">click here</a>. </p>
        </div>' . $footer . '</body></html>';
        $job_info_admin = '
          <h3 style="text-align:left;"> Job Information </h3>
          <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
            <tr style="background-color: #92D000;">
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2"> Locumkit booking confirmation (Key Details) </td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobDate . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Daily Rate</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobRate . '</td>
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
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Additional Booking Info:</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; color:red; font-weight:bold;">' . $jobDesc . '</td>
            </tr>
          </table>
           <br>
          <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
            <tr style="background-color: #92D000;">
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2"> Booking confirmation - details of locum booked</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Name</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freName . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Id</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freID . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">GOC Number</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freGoc . '</td>
            </tr>
            ' . $emp_qu_ans . '
          </table>
          <br>
          <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
            <tr style="background-color: #92D000;">
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2"> Locumkit booking confirmation (additional information)</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Start Time</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_start_time . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Finish Time</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_end_time . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Lunch Break (minutes)</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_lunch_time . '</td>
            </tr>
            ' . $free_qu_ans . '
          </table>
        </div>' . $footer . '</body></html>';

        $massageFre = $mail_css . '
            <div class="mail-job-info" style="padding: 25px 50px 5px;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
            <p>Hi ' . $freName . ',</p>
            <p>We would like to inform you that the following booking has been confirmed. </p>
            <p>Please review the details below:</p>
            ' . $job_info_free;
        //echo "<br/>";

        $massageEmp = $mail_css . '
            <div class="mail-job-info" style="padding: 25px 50px 5px;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
              <p>Hi ' . $empName . ',</p>
              <p> We would like to inform you that the following booking has been confirmed</p>
              ' . $job_info_emp;
        //echo "<br/>";

        $massageAdm = $mail_css . '
            <div class="mail-job-info" style="padding: 25px 50px 5px;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
              <p>The following booking has been confirmed </p>
              <p>A job has been posted by: <b>' . $empName . ' (' . $jobEmpId . ')</b> & accepted by : <b>' . $freName . ' (' . $freID . ')</b> </p>
                <p>Following is job information : </p>
              ' . $job_info_admin;

        /* Mail Send to freelancer */
        try {
            Mail::html($massageFre, function (Message $message) use ($freEmail) {
                $message->to($freEmail)->subject('Booking Confirmation');
            });

            $this->jobsmsController->bookingConfirmationfre($freelancer, $job, null);
            //send sms end

        } catch (Exception $e) {
        }

        /* Mail Send to employer */
        try {
            $sub = 'Booking confirmation: ' . $jobTitle;
            Mail::html($massageEmp, function (Message $message) use ($empEmail, $sub) {
                $message->to($empEmail)->subject($sub);
            });
            $this->jobsmsController->bookingConfirmationemp($employer, $job, null);
            //send sms end

        } catch (Exception $e) {
        }

        $this->notifyController->notification($job->id, $message = "Job Ref: " . $job->id . ", Location: " . $job_store_address . ", Rate: " . $subject_jobRate . ". Open this message to view full details.", $title = 'Booking confirmation', $freelancer->id, $types = "");
        $this->notifyController->notification($job->id, $message = "Job Ref: " . $job->id . ", Locum: " . $freName . ", Rate: " . $subject_jobRate . ". Open this message to view full details.", $title = 'Booking confirmation', $jobEmpId, $types = "");

        /* Mail Send to Admin */
        try {
            $sub = 'Booking confirmation: ' . $jobTitle;
            $adminEmail = config('app.admin_mail');
            Mail::html($massageAdm, function (Message $message) use ($adminEmail, $sub) {
                $message->to($adminEmail)->subject($sub);
            });
        } catch (Exception $e) {
        }
    }


    public function sendAcceptMailToPrivateUser(PrivateUser $user, JobPost $job)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();

        $privateUserEmail   = $user['email'];
        $privateUserName  = $user['name'];
        $empEmail   = $job->employer['email'];
        $empName  = $job->employer['firstname'] . ' ' . $job->employer['lastname'];

        $emp_qu_ans = "";
        foreach ($job->employer->user_answers as $user_answer) {
$decoded = json_decode($user_answer->type_value, true);
$answer_value = is_array($decoded) ? implode(' / ', $decoded) : $user_answer->type_value;

            $emp_qu_ans .= '
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">' . $user_answer->question->freelancer_question . '</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $answer_value . '</td>
                </tr>
            ';
        }
        $store_contact_details = $job->employer->telephone;
        if ($store_contact_details == "") {
            $store_contact_details = $job->employer->mobile;
        }
        $job_store_address = $job->job_address . ", " . $job->job_region . ", " . $job->job_zip;

        $store_start_time = $job->get_store_start_time();
        $store_end_time = $job->get_store_finish_time();
        $store_lunch_time = $job->get_store_lunch_time();

        $job_info_admin = '
          <h3 style="text-align:left;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;"> Job Information </h3>
          <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;width:100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;font-weight: initial;color: #000;">
            <tr style="background-color: #92D000;">
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2"> Locumkit booking confirmation (Key Details)</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . get_date_with_default_format($job['job_date']) . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Daily rate</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . set_amount_format($job['job_rate']) . '.00' . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store contact details</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_contact_details . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Address</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_contact_details . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Additional booking info:</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; color:red; font-weight:bold;">' . $job['job_post_desc'] . '</td>
            </tr>
          </table>
           <br>
          <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px; width:100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;font-weight: initial;color: #000;">
            <tr style="background-color: #92D000;">
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2"> Booking confirmation - details of locum booked</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Name</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $privateUserName . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Id</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;"> <b>Private Locum </b> </td>
            </tr>
            <tr style="background-color: #92D000;">
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2"> Locumkit booking confirmation (additional information)</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Start Time</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_start_time . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Finish Time</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_end_time . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Lunch Break (minutes)</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_lunch_time . '</td>
            </tr>
            ' . $emp_qu_ans . '
          </table>';
        $freelancer_terms = get_locum_email_terms('#92D000');
        $job_info_free = '
          <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left; padding:5px;width:100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;font-weight: initial;color: #000;">
            <tr style="background-color: #92D000;">
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2">  Locumkit booking confirmation (Key Details)</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . get_date_with_default_format($job['job_date']) . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Daily Rate</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . set_amount_format($job['job_rate']) . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Contact Details</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_contact_details . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Address</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_contact_details . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Additional Booking Info:</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; color:red; font-weight:bold;">' . $job['job_post_desc'] . '</td>
            </tr>
          </table>
          <br>
          <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;width:100%;font-weight: initial;color: #000;">
            <tr style="background-color: #92D000;">
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2">  Locumkit booking confirmation (additional information) </td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Start Time</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_start_time . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Finish Time</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_end_time . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Lunch Break (minutes)</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_lunch_time . '</td>
            </tr>
            ' . $emp_qu_ans . '
          </table>
          <br>
          ' . $freelancer_terms . ' ';
        $job_info_emp = '
          <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px; width:100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;font-weight: initial;color: #000;">
            <tr style="background-color: #92D000;">
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2">  Locumkit booking confirmation (Key Details)</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job['job_date'] . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Daily Rate</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job['job_rate'] . '.00' . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Contact Details</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_contact_details . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Address</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_contact_details . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Additional Booking Info:</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; color:red; font-weight:bold;">' . $job['job_post_desc'] . '</td>
            </tr>
          </table>
           <br>
          <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px; width:100%;font-weight: initial;color: #000;">
            <tr style="background-color: #92D000;">
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2">  Booking confirmation - details of locum booked</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Name</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $privateUserName . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Id</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;"> <b>Private Locum</b> </td>
            </tr>
            <tr style="background-color: #92D000;">
              <td style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;" colspan="2">  Locumkit booking confirmation (additional information)</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Start Time</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_start_time . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Finish Time</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_end_time . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Lunch Break (minutes)</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_lunch_time . '</td>
            </tr>
            ' . $emp_qu_ans . '
          </table>';

        $massagePrivateUser = $header;
        $massagePrivateUser .= '<div style="padding: 25px 50px 5px;text-align: left;">
              <p>Hello ' . $privateUserName . ',</p>';
        $massagePrivateUser .= '<p>We would like to inform you that the following booking has been confirmed.</p>';
        $massagePrivateUser .= '<p>Please review the details below:</p>';
        $massagePrivateUser .= $job_info_free;
        $massagePrivateUser .= '</div>';
        $massagePrivateUser .= $footer;
        //echo $massagePrivateUser;

        $massageEmp = $header;
        $massageEmp .= '<div style="padding: 25px 50px 5px;text-align: left;">
              <p>Hello ' . $empName . ',</p>';
        $massageEmp .= '<p>We would like to inform you that the following booking has been confirmed for you: </p>';
        $massageEmp .= $job_info_emp;
        $massageEmp .= '</div>';
        $massageEmp .= $footer;

        //Admin EMail
        $mailAdmin = $header;
        $mailAdmin .= '<div style="padding: 25px 50px 5px;text-align: left;">
              <p>Hello <b>Admin</b>,</p>';
        $mailAdmin .= '<p>Job has been accepted by the Private Locum . </p>';
        $mailAdmin .= '<p>Following is your job information.</p>';
        $mailAdmin .= $job_info_admin;
        $mailAdmin .= '</div>';
        $mailAdmin .= $footer;

        try {
            Mail::html($massagePrivateUser, function (Message $message) use ($privateUserEmail) {
                $message->to($privateUserEmail)->subject('Booking Confirmation');
            });
        } catch (Exception $e) {
        }

        try {
            Mail::html($massageEmp, function (Message $message) use ($empEmail) {
                $message->to($empEmail)->subject('Booking Confirmation');
            });
            $this->notifyController->notification($job->id, $message = "We are pleased to inform you that a locum has been found. Open this message to view full details.", $title = 'Job accepted', $job->employer_id, $types = "");
        } catch (Exception $e) {
        }

        try {
            $adminEmail = config('app.admin_mail');
            Mail::html($mailAdmin, function (Message $message) use ($adminEmail) {
                $message->to($adminEmail)->subject('Booking Confirmation');
            });
        } catch (Exception $e) {
        }
    }

    public function cancelJobByFreNotificationToFreelancer(User $employer, User $freelancer, JobPost $job, string $cancel_reason)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $cancelationPercent = get_job_cancellation_rate_by_user($freelancer->id);
        $empName  = $employer['firstname'] . ' ' . $employer['lastname'];
        $empAddress = $job['job_address'] . ', ' . $job['job_region'] . ', ' . $job['job_zip'];

        $jobDate  = get_date_with_default_format($job['job_date']);

        $jobDetails = '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;">
            <tr style="background-color: #f2f2f2;">
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobDate . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Employer name </th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $empName . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Employer Address
              </th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $empAddress . '</td>
            </tr>
          </table>';

        if ($freelancer) {
            $freEmail   = $freelancer['email'];
            $freName  = $freelancer['firstname'] . ' ' . $freelancer['lastname'];
            $cancelJobMsg = $header;
            $cancelJobMsg .= '<div style="padding: 25px 50px 5px;text-align: left; font-family: sans-serif;">
                <p>Hi ' . $freName . ',</p>';
            $cancelJobMsg .= '<p>We would like to inform you that you have cancelled the following job:</p>';

            $cancelJobMsg .= $jobDetails;
            $cancelJobMsg .= '<h5>Your cancellation percentage is now: ' . $cancelationPercent . ' </h5>';
            $cancelJobMsg .= '<p>We have also notified the employer of this action.</p>';
            $cancelJobMsg .= '<div style="margin: 0; margin-top: 20px;border: 1px solid #ff0000;">';
            $cancelJobMsg .= '<h3 style="background: #ff0000;margin: 0; padding: 5px 10px;  color: #fff;">Reason provided for cancellation:</h3><div style="padding: 10px;"> ' . $cancel_reason . '</div>';
            $cancelJobMsg .= '</div>';

            $cancelJobMsg .= '<div style="border: 1px solid #00A9E0; margin-top: 20px;">';
            $cancelJobMsg .= '<h3 style="margin: 0;padding: 5px 10px;background: #00A9E0;color: #fff;">Additional information </h3>';
            $cancelJobMsg .= '<p style="padding: 10px;">Please, bear in mind that your cancellation percentage is visible to other potential employers. That is one of the ways in which we try to promote supportive and pleasant working environment with minimum cancellation. We understand that sometimes this action is necessary; therefore, employers can see your reason for cancellation. Your cancellation percentage is based on your results in the past six months.</p>';

            $cancelJobMsg .= '</div></div>';
            $cancelJobMsg .= $footer;
            try {
                Mail::html($cancelJobMsg, function (Message $message) use ($freEmail) {
                    $message->to($freEmail)->subject('Locumkit: Cancellation of job');
                });

                $this->jobsmsController->cancelJobByFreNotificationToFreelancerSms($freelancer, $job);
            } catch (Exception $e) {
            }
        }
    }

    public function cancelJobByFreNotificationToEmployer(User $freelancer, User $employer, JobPost $job, string $cancel_reason, $is_relist)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $userEid = encrypt($employer->id);
        $userFre = encrypt($freelancer->id);
        $blockFreLink = url("/block-user?employer_id={$userEid}&freelancer_id={$userFre}");
        $locum_name = $freelancer['firstname'] . ' ' . $freelancer['lastname'];
        $jobDetails = $this->getJobInfo($job);

        $empEmail   = $employer['email'];
        $empName  = $employer['firstname'] . ' ' . $employer['lastname'];
        $cancelJobMsg = $header;
        $cancelJobMsg .= '<div style="padding: 25px 50px 5px;text-align: left; font-family: sans-serif;">
                <p>Hi ' . $empName . ',</p>';
        $cancelJobMsg .= '<p>We are sorry to inform you that the following job has just been cancelled by the locum.</p>';
        $manageJobHref = url("/employer/managejob/{$job->id}");
        $cancelJobMsg .= $jobDetails;
        $cancelJobMsg .= '<div style="margin: 0; margin-top: 20px;border: 1px solid #ff0000;">';
        $cancelJobMsg .= '<h3 style="background: #ff0000;margin: 0; padding: 5px 10px;  color: #fff;">Reason for cancellation:</h3><div style="padding: 10px;"> ' . $cancel_reason . '</div>';
        $cancelJobMsg .= '</div>';
        $cancelJobMsg .= '<div style="border: 1px solid #00A9E0; margin-top: 20px;">';
        $cancelJobMsg .= '<h3 style="margin: 0;padding: 5px 10px;background: #00A9E0;color: #fff;">Additional information</h3>';
        $cancelJobMsg .= '<p style="padding:0px 10px;"><b>To avoid using this locum in the future, please <a href="' . $blockFreLink . '"> click here </a></b></p><p style="padding: 0px 10px;">For the betterment of the profession, we would only advise you to do this, if you strongly feel this locum has a tendency of continuously cancelling last minute.</p>';
        if ($is_relist == 1) {
            $cancelJobMsg .= '<p style="padding: 0px 10px;">Please <a href="' . $manageJobHref . '">click here</a> to relist the job so that Locumkit can find you a matching locum.</p>';
        } else {
            $cancelJobMsg .= '<p style="padding: 0px 10px;">As per your original posting this job has not  been reslisted automatically. If you want please go into <b>Manage job</b> and copy the job to repost.</p>';
        }

        $cancelJobMsg .= '<p style="padding: 0px 10px;"><b>We apologise for any inconvenience this may have caused you.</b></p>';

        $cancelJobMsg .= '</div></div><br/>';
        $cancelJobMsg .= $footer;

        $jobRate  = set_amount_format($job['job_rate']);
        $subject_jobRate  = set_amount_format($job['job_rate']);
        try {
            Mail::html($cancelJobMsg, function (Message $message) use ($empEmail) {
                $message->to($empEmail)->subject('Locumkit: Job cancelled');
            });
            $this->jobsmsController->cancelJobByFreNotificationToEmployerSms($employer, $job);
            $this->notifyController->notification($job->id, $message = 'The following job has been cancelled Job Ref:' . $job->id . ', Locum: ' . $locum_name . ', Rate: ' . $subject_jobRate . '. Reason : ' . $cancel_reason . '. Open this message to view full details. ', $title = 'Cancellation of job', $employer->id, $types = "jobCancel");
        } catch (Exception $e) {
        }
    }

    public function cancelJobByFreNotificationToAdmin(User $freelancer, JobPost $job, string $cancel_reason)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $cancelationPercent = get_job_cancellation_rate_by_user($freelancer->id);
        $freId  = $freelancer->id;
        $freName  = $freelancer['firstname'] . ' ' . $freelancer['lastname'];
        $jobDetails = '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;">
            <tr style="background-color: #f2f2f2;">
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Name</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freName . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">ID No.</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freId . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job ref</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->id . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Reason
              </th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $cancel_reason . '
              </td>
            </tr>
          </table>';
        $cancelJobMsg = $header;
        $cancelJobMsg .= '<div style="padding: 25px 50px 5px;text-align: left; font-family: sans-serif;">
                <p>Hi Admin,</p>';
        $cancelJobMsg .= '<p>The following Locum has just cancelled a job</p>';

        $cancelJobMsg .= $jobDetails;
        $cancelJobMsg .= '<h5>Their cancellation rate now is: ' . $cancelationPercent . ' </h5>';

        $cancelJobMsg .= '</div>';
        $cancelJobMsg .= $footer;
        $adminEmail = config('app.admin_mail');
        try {
            Mail::html($cancelJobMsg, function (Message $message) use ($adminEmail) {
                $message->to($adminEmail)->subject('Locumkit: Cancellation of job');
            });
        } catch (Exception $e) {
        }
    }

    /* Cancel Emp Job notification to Freelancer */
    public function cancelJobByEmpNotificationToFreelancer(User $freelancer, JobPost $job, $cancel_reason)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $jobDetails = $this->getJobInfo($job);
        $freEmail   = $freelancer['email'];
        $freName  = $freelancer['firstname'] . ' ' . $freelancer['lastname'];
        $cancelJobMsg = $header;
        $cancelJobMsg .= '<div style="padding: 25px 50px 5px;text-align: left; font-family: sans-serif;">
            <p>Hi ' . $freName . ',</p>';
        $cancelJobMsg .= '<p>We are sorry to inform you that the following booking has just been cancelled by the employer.</p>';

        $cancelJobMsg .= $jobDetails;
        $cancelJobMsg .= '<div style="margin: 0; margin-top: 20px;border: 1px solid #ff0000;">';
        $cancelJobMsg .= '<h3 style="background: #ff0000;margin: 0; padding: 5px 10px;  color: #fff;">Reason for cancellation:</h3><div style="padding: 10px;"> ' . $cancel_reason . '</div>';
        $cancelJobMsg .= '</div>';
        $cancelJobMsg .= '<div style="border: 1px solid #00A9E0; margin-top: 20px;">';
        $cancelJobMsg .= '<h3 style="margin: 0;padding: 5px 10px;background: #00A9E0;color: #fff;">Additional information </h3>';
        $cancelJobMsg .= '<p style="padding:0px 10px;">We have updated your calendar such that you are now available for the jobs on the designated day. You will now receive e-mails for that day. If you are no longer available to work on this day, please login and adjust your availability settings. We apologise for any inconvience this may have caused you. </p>';

        $cancelJobMsg .= '</div></div>';
        $cancelJobMsg .= $footer;
        try {
            Mail::html($cancelJobMsg, function (Message $message) use ($freEmail) {
                $message->to($freEmail)->subject('Locumkit: Cancellation of job');
            });
            $this->jobsmsController->cancelJobByEmpNotificationToFreelancerSms($freelancer, $job);
            /* Fetch record of job */
            $jobTitle   = $job['job_title'];
            $jobDate  = get_date_with_default_format($job['job_date']);
            $jobRate  = set_amount_format($job['job_rate']);
            $subject_jobRate  = set_amount_format($job['job_rate']);
            $jobAddress = $job['job_address'] . ", " . $job['job_region'] . "-" . $job['job_zip'];

            //Mobile APP Notification
            $this->notifyController->notification($job->id, $message = 'The following job has been cancelled by employer. Job Ref.: ' . $job->id . ', Date: ' . $jobDate . ', Location: ' . $jobAddress . ', Rate: ' . $subject_jobRate . ', Reason : ' . $cancel_reason . '. Open this message to view full details. Your calender has been updated accordingly.', $title = 'Job cancelled', $freelancer->id, $types = "jobCancel");
        } catch (Exception $e) {
        }
    }

    public function cancelJobByEmpNotificationToEmployer(User $employer, User $freelancer, JobPost $job, string $cancel_reason)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $cancelationPercent = get_job_cancellation_rate_by_user($employer->id, "employer");
        $freEmail   = $freelancer['email'];
        $freName  = $freelancer['firstname'] . ' ' . $freelancer['lastname'];
        $jobDetails = '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;">
            <tr style="background-color: #f2f2f2;">
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . date('d-m-Y') . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Locum name </th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freName . '</td>
            </tr>
          </table>';
        $empEmail   = $employer['email'];
        $empName  = $employer['firstname'] . ' ' . $employer['lastname'];
        $cancelJobMsg = $header;
        $cancelJobMsg .= '<div style="padding: 25px 50px 5px;text-align: left; font-family: sans-serif;">
                <p>Hi <b>' . $empName . '</b>,</p>';
        $cancelJobMsg .= '<p>You have just cancelled the following booking:</p>';
        //$cancelJobMsg .= '<p>This email is a confirmation for the following booking being cancelled by you: </p>';
        $cancelJobMsg .= $jobDetails;
        $cancelJobMsg .= '<h5>Your cancellation rate now is : ' . $cancelationPercent . ' </h5>';
        $cancelJobMsg .= '<p>We have notified the Locum of the cancellation .</p>';
        $cancelJobMsg .= '<div style="margin: 0; margin-top: 20px;border: 1px solid #ff0000;">';
        $cancelJobMsg .= '<h3 style="background: #ff0000;margin: 0; padding: 5px 10px;  color: #fff;">Your reason for cancellation was:</h3><div style="padding: 10px;"> ' . $cancel_reason . '</div>';
        $cancelJobMsg .= '</div>';

        $cancelJobMsg .= '<div style="border: 1px solid #00A9E0; margin-top: 20px;">';
        $cancelJobMsg .= '<h3 style="margin: 0;padding: 5px 10px;background: #00A9E0;color: #fff;">Important Information</h3>';
        $cancelJobMsg .= "<p style='padding: 0px 10px;margin: 10px 0px 0px;'>Your cancellation rate is based on the last six months of results. </p><p style='padding: 0px 10px;'>Your cancellation percentage is advertised to all locums that you invite for future potential bookings.</p><p style='padding: 0px 10px;margin: 0px 0px 10px;'>Our aim at Locumkit is to promote an environment where these cancellations are kept at a minimum and we hope to achieve this by having everyone's cancellation rates transparent. </p>";

        $cancelJobMsg .= '</div></div>';
        $cancelJobMsg .= $footer;
        try {
            Mail::html($cancelJobMsg, function (Message $message) use ($empEmail) {
                $message->to($empEmail)->subject('Locumkit: Cancellation of job');
            });
            $this->jobsmsController->cancelJobByEmpNotificationToEmployerSms($employer, $job);
        } catch (Exception $e) {
        }
    }

    /* Cancel Emp Job notifiction to Admin */
    public function cancelJobByEmpNotificationToAdmin(User $employer, JobPost $job, string $cancel_reason)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $cancelationPercent = get_job_cancellation_rate_by_user($employer->id, "employer");
        $empId  = $employer->id;
        $empName  = $employer['firstname'] . ' ' . $employer['lastname'];
        $jobDetails = '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;">
            <tr style="background-color: #f2f2f2;">
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Name</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $empName . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">ID No.</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $empId . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job ref</th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->id . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Reason
              </th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $cancel_reason . '
              </td>
            </tr>
          </table>';
        $cancelJobMsg = $header;
        $cancelJobMsg .= '<div style="padding: 25px 50px 5px;text-align: left; font-family: sans-serif;">
                <p>Hi <b>Admin</b>,</p>';
        $cancelJobMsg .= '<p>The folowing employer has just cancelled a job</p>';

        $cancelJobMsg .= $jobDetails;
        $cancelJobMsg .= '<h5>Their cancellation rate now is: ' . $cancelationPercent . ' </h5>';

        $cancelJobMsg .= '</div>';
        $cancelJobMsg .= $footer;

        $adminEmail = config('app.admin_mail');
        try {
            Mail::html($cancelJobMsg, function (Message $message) use ($adminEmail) {
                $message->to($adminEmail)->subject('Locumkit: Cancellation of job');
            });
        } catch (Exception $e) {
        }
    }


    /* Send alert of feedback after 1 week if user not submitted the feedback*/
    public function sendFeedbackNotificationOneWeekAlert(JobPost $job, User $user, string $feedback_link, $user_type)
    {
        $adminEmail = config('app.admin_mail');
        $header   = get_mail_header();
        $footer   = get_mail_footer();

        $jobDate  = get_date_with_default_format($job['job_date']);
        $jobData = $this->getJobInfo($job);

        if ($user_type == 2) {
            $freEmail   = $user['email'];
            $freName  = $user['firstname'] . ' ' . $user['lastname'];

            $massageFre = $header;
            $massageFre .= '<div style="padding: 25px 50px 5px;text-align: left;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
            <p>Hi <b>' . $freName . '</b>,</p>';
            $massageFre .= '<p>This is a reminder request for you to leave feedback for the following </p>';
            $massageFre .= $jobData;
            $massageFre .= '<p>We would now like you to leave feedback for the employer on your day there.</p>';
            $massageFre .= '<p>This would help other Locums and also help improve clinical competition amongst users.</p>';
            $massageFre .= '<p>Please click here on below button to submit your valuable feedback.</p>';
            $massageFre .= '<p>' . $feedback_link . '</p>';
            $massageFre .= '</div>';
            $massageFre .= $footer;

            try {
                $sub = 'Feedback reminder for#' . $job->id;
                Mail::html($massageFre, function (Message $message) use ($freEmail, $sub) {
                    $message->to($freEmail)->subject($sub);
                });
                $this->notifyController->notification($job->id, $message = "Please leave feedback for work carried out on date:" . $jobDate . '. Open this message to leave the feedback.', $title = 'Feedback reminder', $user->id, $types = "feedbackRequest");
            } catch (Exception $e) {
            }
        }

        if ($user_type == 3) {
            $jobData  = $this->getJobInfo($job);
            $empEmail   = $user['email'];
            $empName  = $user['firstname'] . ' ' . $user['lastname'];
            $massageEmp = $header;
            $massageEmp .= '<div style="padding: 25px 50px 5px;text-align: left;">
            <p>Hi ' . $empName . ',</p>';
            $massageEmp .= '<p>This is a reminder request for you to leave feedback for the following </p>';
            $massageEmp .= $jobData;
            $massageEmp .= '<p>Your feedback is would be highly valuable as it would allow for:</p>';
            $massageEmp .= '<p>1) Self reflection for the locum in question.</p>';
            $massageEmp .= '<p>2) Increase in clinical competition amongst locum.</p>';
            $massageEmp .= '<p>3) Allow your fellow employers to determine the locums competency.</p>';
            $massageEmp .= '<p>Please click below to submit your feedback</p>';
            $massageEmp .= '<p>' . $feedback_link . '</p>';
            $massageEmp .= '</div>';
            $massageEmp .= $footer;

            try {
                $sub = 'Feedback reminder for#' . $job->id;
                Mail::html($massageEmp, function (Message $message) use ($empEmail, $sub) {
                    $message->to($empEmail)->subject($sub);
                });
                $this->notifyController->notification($job->id, $message = "Please leave feedback for work carried out on date:" . $jobDate . '. Open this message to leave the feedback.', $title = 'Feedback reminder', $user->id, $types = "feedbackRequest");
            } catch (Exception $e) {
            }
        }
    }

    /* Reminder Notification mail */
    public function sendReminder(JobPost $job, User $freelancer, User $employer, int $notifyDay)
    {
        $jobId    = $job['id'];
        $eId    = $job['employer_id'];
        $jobTitle   = $job['job_title'];
        $jobDate  = get_date_with_default_format($job['job_date']);
        $jobRate  = set_amount_format($job['job_rate']);
        $subject_jobRate  = set_amount_format($job['job_rate']);
        $jobAddress     = $job['job_address'] . ", " . $job['job_region'] . "-" . $job['job_zip'];
        $storeName  = $job->job_store->store_name;

        $freEmail = $freelancer['email'];
        $freName = $freelancer['firstname'] . ' ' . $freelancer['lastname'];

        $empEmail = $employer['email'];
        $empName = $employer['firstname'] . ' ' . $employer['lastname'];

        $header   = get_mail_header();
        $footer   = get_mail_footer();

        $mail_css   = $header;
        $job_info = '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
        <tr style="background-color: #f2f2f2;">
          <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job ID</th>
          <td style=" border: 1px solid black;  text-align:left;  padding:5px;">#' . $jobId . '</td>
        </tr>
        <tr>
          <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job date</th>
          <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobDate . '</td>
        </tr>
        <tr>
          <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job location</th>
          <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobAddress . '</td>
        </tr>
        <tr>
          <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store</th>
          <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $storeName . '</td>
        </tr>
        <tr style="background-color: #f2f2f2;">
          <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Rate</th>
          <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobRate . '</td>
        </tr>

      </table>';

        $hrefViewUrl = url("/single-job/{$job->id}");
        $cancelUrl = url("/cancel-job/{$job->id}");

        $remider_on = ($notifyDay > 1) ? 'on job day.' : 'tomorrow';
        $massageFre = $mail_css . '
        <div style="padding: 25px 50px 5px;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
          <p>Hello ' . $freName . ',</p>
          <p>This is a courteous reminder of the upcoming booking:</p>
          ' . $job_info . '
          <br/>
          <p>To view full details of the booking, <a href="">click here </a></p>
          <p>If for whatever reason you can not make it, please cancel by <a href="' . $hrefViewUrl . '">clicking here</a></p>
          <p>If you have signed up to our finance packages then all your income and expenses will be automatically recorded/triggered. ' . $remider_on . '</p>
          </div>' . $footer . '</body></html>';


        $massageEmp = $mail_css . '
          <div style="padding: 25px 50px 5px;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
          <p>Hello ' . $empName . ',</p>
          <p>This is a courteous reminder of the upcoming booking:</p>
        ' . $job_info . '
        <br/>
        <p>To view full details of the booking, <a href="' . $hrefViewUrl . '">click here.</a></p>
        <p>If for whatever reason you can not make it, please cancel by <a href="' . $cancelUrl . '">clicking here</a></p>
        </div>' . $footer . '</body></html>';

        if ($notifyDay > 1) {
            $reminderSubject = 'Job reminder';
            $appNotificationSub = $reminderSubject;
        } else {
            $reminderSubject = 'Job reminder for - TOMORROW';
            $appNotificationSub = 'Job reminder (next day)';
        }

        try {
            if ($freEmail) {
                Mail::html($massageFre, function (Message $message) use ($freEmail, $reminderSubject) {
                    $message->to($freEmail)->subject($reminderSubject);
                });

                $smsLinksArray =  array('detail' => $hrefViewUrl, 'cancel' => $cancelUrl);
                $this->jobsmsController->sendReminderSms($freelancer, $job, $smsLinksArray);
                $this->notifyController->notification($jobId, $message = "Job Ref: " . $jobId . ", Location: " . $jobAddress . ", Rate: " . $subject_jobRate . ". Open this message to view full details.", $title = $appNotificationSub, $freelancer->id, $types = "");
            }
        } catch (Exception $e) {
        }

        try {
            if ($empEmail) {
                Mail::html($massageEmp, function (Message $message) use ($empEmail, $reminderSubject) {
                    $message->to($empEmail)->subject($reminderSubject);
                });

                $smsLinksArray =  array('detail' => $hrefViewUrl, 'cancel' => $cancelUrl);
                $this->jobsmsController->sendReminderSms($employer, $job, $smsLinksArray);
                $this->notifyController->notification($jobId, $message = "Job Ref: " . $jobId . ", Locum: " . $freName . ", Rate: " . $subject_jobRate . ". Open this message to view full details.", $title = $appNotificationSub, $eId, $types = "");
            }
        } catch (Exception $e) {
        }
    }

    /* Private Job reminder notification mail */
    public function sendPrivateJobReminder(User $freelancer, $job)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();

        $freEmail   = $freelancer['email'];
        $freName  = $freelancer['firstname'] . ' ' . $freelancer['lastname'];
        $pJobRate = set_amount_format($job->job_rate);
        $privateJobMsg = $header;
        $privateJobMsg .= '<div style="padding: 25px 50px 5px;text-align: left; font-family: sans-serif;">
          <p>Hello ' . $freName . ',</p>';
        $privateJobMsg .= '<p>Please see below details of your upcoming booking for tomorrow:</p>';
        $privateJobMsg .= '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;">
                <tr style="background-color: #f2f2f2;">
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Title</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->job_title . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Date</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . get_date_with_default_format($job->job_date) . '</td>
                </tr>
                <tr style="background-color: #f2f2f2;">
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Rate</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $pJobRate . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Address</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->job_location . '</td>
                </tr>
            </table><br/>
        ';
        $privateJobMsg .= '</div>';
        $privateJobMsg .= $footer;

        try {
            Mail::html($privateJobMsg, function (Message $message) use ($freEmail) {
                $message->to($freEmail)->subject('Private Job reminder');
            });
            $this->notifyController->notification($job->id, $message = "This is just a courtesy reminder that you have a booking coming up for private job.", $title = 'Job reminder', $freelancer->id, $types = "privateJobReminder");
        } catch (Exception $e) {
        }
    }

    /* Reminder Notification mail to private locum */
    public function sendRemindertoprivateuser(JobPost $job, PrivateUser $user, User $employer,)
    {
        $jobDate  = get_date_with_default_format($job['job_date']);
        $jobRate  = set_amount_format($job['job_rate']);
        $jobAddress = $job['job_address'] . ", " . $job['job_region'] . "-" . $job['job_zip'];
        $storeName  = $job->job_store->store_name;

        $freEmail   = $user['email'];
        $freName  = $user['name'];

        $empEmail = $employer['email'];
        $empName = $employer['firstname'] . ' ' . $employer['lastname'];

        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $mail_css   = $header;
        $job_info = '
            <h3 style="text-align:left;"> Job Information </h3>
            <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;">
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Date</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobDate . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job Location</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobAddress . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $storeName . '</td>
                </tr>
                <tr style="background-color: #f2f2f2;">
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Rate</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobRate . '</td>
                </tr>
            </table>
        ';
        $massageFre = $mail_css . '
        <div style="padding: 25px 50px 5px;">
          <p>Hello ' . $freName . '</b>,</p>
          <p>We would like to remind you that you have a booking coming up. Please, see the summary of details below:</p>
        ' . $job_info . '
        <br/>
        </div>' . $footer . '</body></html>';
        $reminderSubject = 'Job reminder';

        $massageEmp = $mail_css . '
        <div style="padding: 25px 50px 5px;">
          <p>Hello ' . $empName . ',</p>
          <p>This is just a courteous reminder for the below upcoming job:</p>
        ' . $job_info . '
        <br/>
        </div>' . $footer . '</body></html>';
        $reminderSubject = 'Job reminder';

        try {
            if ($freEmail) {
                Mail::html($massageFre, function (Message $message) use ($freEmail, $reminderSubject) {
                    $message->to($freEmail)->subject($reminderSubject);
                });
            }
        } catch (Exception $e) {
        }
        /* Mail Send to employer */
        try {
            if ($empEmail) {
                Mail::html($massageEmp, function (Message $message) use ($empEmail, $reminderSubject) {
                    $message->to($empEmail)->subject($reminderSubject);
                });
            }
        } catch (Exception $e) {
        }
    }

    /* Email notification to Expired package user */
    public function sendPackageExpiredMail(User $user, $package, $btnLink, $day)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $freEmail   = $user['email'];
        $freName  = $user['firstname'] . ' ' . $user['lastname'];

        $pkgMessage = $header;
        $pkgMessage .= '<div style="padding: 25px 50px 5px;text-align: left; font-family: sans-serif;">
          <p>Hello ' . $freName . ',</p>';
        $pkgMessage .= '<p>Your current package: <b style="text-transform:uppercase">' . $package['name'] . ' ( ' . set_amount_format($package['price']) . ' ) </b>.</p>';
        $pkgMessage .= '<p>A friendly reminder that your membership at Locumkit is about to expire in <b>' . $day . ' day(s)</b>. To renew or update your membership log in to you profile and follow the suggested actions. </p>';
        $pkgMessage .= '<p>Your current plan is: <b style="text-transform:uppercase">' . $package['name'] . ' ( ' . set_amount_format($package['price']) . ' ) </b><br>Last renewal date:<br>Expiry date: </p>';
        $pkgMessage .= '<p>Please note if you do not renew your account in time then you will no longer be able to access your details (booking information and your financials) or receive any further job notifications and/or reminders. </p>';
        $pkgMessage .= '<p>Click below button to upgrade your account.</p>';
        $pkgMessage .= '<p>' . $btnLink . '</p>';
        $pkgMessage .= '<p>If you have any questions, please do not hesitate to contact us and one of our team members will look to address your concern at the earliest convenience. </p>';
        $pkgMessage .= '</div>';
        $pkgMessage .= $footer;

        try {
            Mail::html($pkgMessage, function (Message $message) use ($freEmail) {
                $message->to($freEmail)->subject('Locumkit Membership');
            });
            $this->jobsmsController->sendPackageExpiredMailSms($user);
            $this->notifyController->notification('', $message = 'Your membership going to expired in ' . $day . ' day please upgrade ASAP.', $title = 'Membership Upgrade', $user->id, $types = "packageUpgrade");
        } catch (Exception $e) {
        }
    }

    /* Send job summary Notification to freelancer */
    public function sendFreJobSummaryNotification(User $freelancer, JobPost|Builder $job, float $income, float $expense, $freFeedback,)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        if (!empty($freelancer)) {
            $freName  = $freelancer['firstname'] . ' ' . $freelancer['lastname'];
            $ferEmail = $freelancer['email'];
        }
        $expense = $expense != 0 ? $expense : 'Not submited yet.';
        $freFeedback = $freFeedback != 0 ? $this->calculatestarsaverage_summary($freFeedback) : 'Not submitted yet.';
        $summaryMsg = $header;
        $summaryMsg .= '<div style="padding: 25px 50px 5px; text-align: left; font-family: sans-serif;">
          <p>Hi <b>' . $freName . '</b>,</p>';
        $summaryMsg .= '<p>This below is your summary for the following job:</p>';
        $summaryMsg .= '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;">
                <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job ID
              </th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;"> #' . $job->id . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Income
              </th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;"> ' . set_amount_format($income) . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Expenses
              </th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;"> ' . set_amount_format($expense) . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Feedback
              </th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;"> ' . $freFeedback . '</td>
            </tr>
          </table>';

        $summaryMsg .= '<p>&nbsp;</p><p>To see details of these detials please login to your profile</p>';
        $summaryMsg .= '<p>If there are entires with N/A then this is because we did not get information from you or the employer (feedback). You can always go into your profile to add information on your income and expense.</p>';

        $summaryMsg .= '</div>';
        $summaryMsg .= $footer;

        try {
            $sub = 'Summary on job ' . get_date_with_default_format(today()->subDays(2));
            Mail::html($summaryMsg, function (Message $message) use ($ferEmail, $sub) {
                $message->to($ferEmail)->subject($sub);
            });
        } catch (Exception $e) {
        }
    }

    public function calculatestarsaverage_summary($avgrating)
    {
        $pre = ($avgrating * 2) * 10;
        $star =  '<div style="width: 135px;"> <div style="background: url(' . url('/frontend/locumkit-template/img/star-rating-sprite.png') . ') repeat-x;
          font-size: 0;
          height: 15px;
          line-height: 0;
          overflow: hidden;
          text-indent: -999em;
          width: 70px;
                            background-size: 14px 30px;
          float: left;">
          <span style=" width:' . $pre . '% ;  background: url(' . url('/frontend/locumkit-template/img/star-rating-sprite.png') . ') repeat-x;
          background-position: 0 100%;
          float: left;
          height: 15px;
                            background-size: 14px 29px;
          display: block;"></span></div><div> &nbsp;&nbsp;' . $avgrating . ' star(s)</div></div>';
        return $star;
    }

    /* Send job summary Notification to employer */
    public function sendEmpJobSummaryNotification(User $employer, User $freelancer, JobPost|Builder $job, float $income, float $expense, $empFeedback)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $encrypted_employer_id = encrypt($employer->id);
        $encrypted_freelancer_id = encrypt($freelancer->id);

        $blockFreLink = "/block-user?employer_id={$encrypted_employer_id}&freelancer_id={$encrypted_freelancer_id}";
        $empName  = $employer['firstname'] . ' ' . $employer['lastname'];
        $empEmail = $employer['email'];
        $expense = $expense != 0 ? $expense : 'Not submited yet.';
        $empFeedback = $empFeedback != 0 ? $this->calculatestarsaverage_summary($empFeedback)  : 'Not submitted yet.';
        $summaryMsg = $header;
        $summaryMsg .= '<div style="padding: 25px 50px 5px; text-align: left; font-family: sans-serif;">
          <p>Hi <b>' . $empName . '</b>,</p>';
        $summaryMsg .= '<p>This below is your summary for the following job:</p>';
        $summaryMsg .= '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;">
                <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job ID
              </th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;"> #' . $job->id . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Expense
              </th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;"> ' . set_amount_format($income) . '</td>
            </tr>
            <tr>
              <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Feedback
              </th>
              <td style=" border: 1px solid black;  text-align:left;  padding:5px;"> ' . $empFeedback . ' </td>
            </tr>
          </table>';

        $summaryMsg .= '<p>&nbsp;</p><p>Want to block this locum, please <a href="' . $blockFreLink . '">click here.</a></p>';
        $summaryMsg .= '<p>To see details of these detials please login to your profile</p>';
        $summaryMsg .= '<p>If there are entires with N/A then this is because we did not get information from you or the employer (feedback). You can always go into your profile to add information on your income and expense.</p>';

        $summaryMsg .= '</div>';
        $summaryMsg .= $footer;

        try {
            $sub = 'Summary on job ' . get_date_with_default_format(today()->subDays(2));

            Mail::html($summaryMsg, function (Message $message) use ($empEmail, $sub) {
                $message->to($empEmail)->subject($sub);
            });
        } catch (Exception $e) {
        }
    }

    /* Send email to freelance that 5 min left to reset freeze job */
    public function sendExpireFreezeNotification(JobPost $job, User $freelancer, $expired_note_type, $link)
    {
        $freEmail   = $freelancer['email'];
        $freName  = $freelancer['firstname'] . ' ' . $freelancer['lastname'];
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $mail_css   = $header;
        $jobDate  = get_date_with_default_format($job['job_date']);
        $jobRate  = set_amount_format($job['job_rate']);
        $jobAddress = $job['job_address'] . ", " . $job['job_region'] . "-" . $job['job_zip'];
        $job_info   = $this->getJobInfo($job);

        if ($expired_note_type == 2) {
            $notification_sub = 'Freeze confirmation expiring';
            $mail_sub = '5 mins left for job to unfreeze';
            $mail_title = '<p style="line-height: 20px;"> We would like to inform you that the following job will be frozen for just another 5 minutes before it opens to other locums. </p><p style="line-height: 20px;"> Please review the details and apply now to confirm your booking for the job: </p>';
            $notification_title = "Job Ref: " . $job->id . ", Date: " . $jobDate . ", Location: " . $jobAddress . ", Rate: " . $jobRate . ". Note: Job is frozen for 5 minutes only.";
        } else {
            $mail_sub = $jobDate . ' / ' . $jobAddress . ' / ' . $jobRate;
            $mail_title = '<p>The following job (ref no ' . $job->id . ') is open again for all applicants.<b style="font-weight: normal;display: block;margin-bottom: 5px;">Please review the job details below:</b></p> ';
            $notification_title = "Job Ref: " . $job->id . ", Date: " . $jobDate . ", Location: " . $jobAddress . ", Rate: " . $jobRate . ". Open this message to view full details.";
            $notification_sub = 'Job invitation - unfrozen';
        }
        $user_info = ' <p style="text-align:left;"> ' . $mail_title . ' </p>' . $job_info . '<br/>' . $link . '</div>' . $footer . '</body></html>';
        $massageFre = $mail_css . '
				<div style="padding: 25px 50px 5px;text-align: left;">
				<p>Hello ' . $freName . ',</p>' . $user_info;

        try {
            Mail::html($massageFre, function (Message $message) use ($freEmail, $mail_sub) {
                $message->to($freEmail)->subject($mail_sub);
            });
            $this->jobsmsController->sendExpireFreezeNotificationSms($freelancer, $job->id);
            $this->notifyController->notification($job->id, $message = $notification_title, $title = $notification_sub, $freelancer->id, $types = "interest");
        } catch (Exception $e) {
        }
    }

    /* Send email to private locum about job freeze time expired */
    public function sendExpireFreezeNotificationPrivateLocum(JobPost $job, PrivateUser $locum, $link)
    {
        $job_info   = $this->getJobInfo($job);
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $mail_css   = $header;

        $jobDate  = get_date_with_default_format($job['job_date']);
        $jobRate  = set_amount_format($job['job_rate']);
        $jobAddress = $job['job_address'] . ", " . $job['job_region'] . "-" . $job['job_zip'];

        $mail_sub = $jobDate . ' / ' . $jobAddress . ' / ' . $jobRate;
        $mail_title = '<p>The following job (ref no ' . $job->id . ') is no longer frozen and is open again to all applicable locums.</p> ';

        $user_info = '<p style="text-align:left;"> ' . $mail_title . ' </p>' . $job_info . '<br/>' . $link . '</div>' . $footer . '</body></html>';
        $massageFre = $mail_css . '<div style="padding: 25px 50px 5px;text-align: left;"><p>Hello ' . $locum->name . ',</p>' . $user_info;

        try {
            Mail::html($massageFre, function (Message $message) use ($locum, $mail_sub) {
                $message->to($locum->email)->subject($mail_sub);
            });
        } catch (Exception $e) {
        }
    }

    /* Send dispute notificatin to all users */
    public function sendDisputeSubmitNotification($id, User $freelancer, User $employer, $job_id, $user_type)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $feedbackAdminLink = url("/admin/config/user/feedback/user-feedback/edit/{$id}");

        $empName  = $employer['firstname'] . ' ' . $employer['lastname'];
        $empEmail = $employer['email'];

        $freName  = $freelancer['firstname'] . ' ' . $freelancer['lastname'];
        $freEmail = $freelancer['email'];

        if ($user_type == JobFeedbackDispute::FEEDBACK_DISPUTE_BY_FREELANCER) {
            $disputeFreMsg = $header;
            $disputeFreMsg .= '<div style="padding: 25px 50px 5px; text-align: left; font-family: sans-serif;">
			<p>Hi ' . $freName . ',</p>';
            $disputeFreMsg .= '<p>' . $empName . ' has just disputed the feedback you have left for them in regards to the job <b>#' . $job_id . '</b>. </p>';

            $disputeFreMsg .= '<p>We at LocumKit shall look into this and hope to come to a resolution within the next 24-48 hrs. We might contact you to help us in this process.</p>';

            $disputeFreMsg .= '<p>Thank you for your co-operation. </p>';

            $disputeFreMsg .= '</div>';
            $disputeFreMsg .= $footer;

            $disputeEmpMsg = $header;
            $disputeEmpMsg .= '<div style="padding: 25px 50px 5px; text-align: left; font-family: sans-serif;">
			<p>Hi ' . $empName . ',</p>';
            $disputeEmpMsg .= '<p>We have received your application for dispute on the feedback submitted by ' . $freName . ' regarding the job <b>#' . $job_id . '</b>. </p>';

            $disputeEmpMsg .= '<p>We aim to resolve this within the next two days.</p>';
            $disputeEmpMsg .= '<p>Thank you for your co-operation. </p>';

            $disputeEmpMsg .= '</div>';
            $disputeEmpMsg .= $footer;

            $disputeAdminMsg = $header;
            $disputeAdminMsg .= '<div style="padding: 25px 50px 5px; text-align: left; font-family: sans-serif;">
			<p>Hi Admin,</p>';
            $disputeAdminMsg .= '<p><b style="text-transform: capitalize;">' . $empName . '</b> submit dispute on feedback that submitted by <b style="text-transform: capitalize;">' . $freName . '</b> on job <b>#' . $job_id . '</b>. </p>';

            $disputeAdminMsg .= '<p>Please <a href="' . $feedbackAdminLink . '">click here</a> to view the datails.</p>';

            $disputeAdminMsg .= '</div>';
            $disputeAdminMsg .= $footer;
        } elseif ($user_type == JobFeedbackDispute::FEEDBACK_DISPUTE_BY_EMPLOYER) {
            $disputeEmpMsg = $header;
            $disputeEmpMsg .= '<div style="padding: 25px 50px 5px; text-align: left; font-family: sans-serif;">
			<p>Hi ' . $empName . ',</p>';
            $disputeEmpMsg .= '<p>We would like to inform you that ' . $freName . ' has filed a dispute on the feedback you submitted regarding the job <b>#' . $job_id . '</b>. </p>';

            $disputeEmpMsg .= '<p>We at LocumKit shall look into this and hope to come to a resolution within the next 24-48 hrs. We might contact you to help us in this process.</p>';
            $disputeEmpMsg .= '<p>Thank you for your co-operation. </p>';

            $disputeEmpMsg .= '</div>';
            $disputeEmpMsg .= $footer;

            $disputeFreMsg = $header;
            $disputeFreMsg .= '<div style="padding: 25px 50px 5px; text-align: left; font-family: sans-serif;">
			<p>Hi ' . $freName . ',</p>';
            $disputeFreMsg .= '<p>We have received your application for dispute on the feedback submitted by ' . $empName . ' regarding the job <b>#' . $job_id . '</b>. </p>';

            $disputeFreMsg .= '<p>We aim to resolve this within the next two days.</p>';
            $disputeFreMsg .= '<p>Thank you for your co-operation.</p>';

            $disputeFreMsg .= '</div>';
            $disputeFreMsg .= $footer;

            $disputeAdminMsg = $header;
            $disputeAdminMsg .= '<div style="padding: 25px 50px 5px; text-align: left; font-family: sans-serif;">
			<p>Hi Admin,</p>';
            $disputeAdminMsg .= '<p><b style="text-transform: capitalize;">' . $freName . '</b> submit dispute on feedback that submitted by <b style="text-transform: capitalize;">' . $empName . '</b> on job <b>#' . $job_id . '</b>. </p>';

            $disputeAdminMsg .= '<p>Please <a href="' . $feedbackAdminLink . '">click here</a> to view the datails.</p>';

            $disputeAdminMsg .= '</div>';
            $disputeAdminMsg .= $footer;
        }

        try {
            $sub = 'Dispute Alert on job: #' . $job_id;
            Mail::html($disputeFreMsg, function (Message $message) use ($freEmail, $sub) {
                $message->to($freEmail)->subject($sub);
            });
            $this->jobsmsController->sendDisputeSubmitNotificationSms($freelancer, $job_id, $freName, $empName);
        } catch (Exception $e) {
        }

        try {
            $sub = 'Dispute Alert on job: #' . $job_id;
            Mail::html($disputeFreMsg, function (Message $message) use ($empEmail, $sub) {
                $message->to($empEmail)->subject($sub);
            });
            $this->jobsmsController->sendDisputeSubmitNotificationSms($employer, $job_id, $empName, $freName);
        } catch (Exception $e) {
        }

        try {
            $adminEmail = config('app.admin_mail');
            $sub = 'Dispute Alert on job: #' . $job_id;
            Mail::html($disputeFreMsg, function (Message $message) use ($adminEmail, $sub) {
                $message->to($adminEmail)->subject($sub);
            });
        } catch (Exception $e) {
        }
    }

    public function updateProfileMails($job_data)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        if ($job_data &&  $job_data['email']) {
            $firstname = $job_data['firstname'];
            $email = $job_data['email'];
            $message = $header . '
				<div style="padding: 25px 50px 30px; text-align: left;">
					<p>Hello ' . $firstname . ',</p>
					<p style="font-weight: normal;">Your account details have just been changed .</p>
					<p style="font-weight: normal;">If this was you then you can safely ignore this email .</p>
					<p style="font-weight: normal;">If this was not you, your account has been compromised. Please follow these steps.</p>

					<p>1- Reset your password</p>
					<p>2- Check your account details</p>
					<p><p>
				</div>
			';
            $message .= $footer;
            try {
                Mail::html($message, function (Message $message) use ($email) {
                    $message->to($email)->subject('Profile update notification');
                });
            } catch (Exception $e) {
            }
        }
    }

    /* Cancel Emp Job notifiction to Employer if job accepct by private locum*/
    public function cancelJobByEmpNotificationToEmployerIFPrivatefreelancer(PrivateUser $user, User $employer, JobPost $job, $cancel_reason)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $freEmail   = $user['email'];
        $freName  = $user['name'];
        $jobDetails = '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;">
						<tr style="background-color: #f2f2f2;">
						<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
						<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . date('d-m-Y') . '</td>
						</tr>
						<tr>
						<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Private Locum Name</th>
						<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freName . '</td>
						</tr>
						<tr>
						<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Private Locum Email</th>
						<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freEmail . '</td>
						</tr>
					</table>
		';
        $empEmail   = $employer['email'];
        $empName  = $employer['firstname'] . ' ' . $employer['lastname'];
        $cancelJobMsg = $header;
        $cancelJobMsg .= '<div style="padding: 25px 50px 5px;text-align: left; font-family: sans-serif;">
			<p>Hi <b>' . $empName . '</b>,</p>';
        //$cancelJobMsg .= '<p>You have just cancelled the following booking:</p>';
        $cancelJobMsg .= '<p>This email is a confirmation for the following booking being cancelled by you:</p>';
        $cancelJobMsg .= $jobDetails;
        //$cancelJobMsg .= '<h5>Your cancellation percentage now is : '.$cancelationPercent.' </h5>';
        $cancelJobMsg .= '<p>We have notified the locum of the cancellation.</p>';
        $cancelJobMsg .= '<div style="margin: 0; margin-top: 20px;border: 1px solid #ff0000;">';
        $cancelJobMsg .= '<h3 style="background: #ff0000;margin: 0; padding: 5px 10px;  color: #fff;">Your reason for cancellation was:</h3><div style="padding: 10px;"> ' . $cancel_reason . '</div>';
        $cancelJobMsg .= '</div>';
        $cancelJobMsg .= '</div>';
        $cancelJobMsg .= $footer;
        try {
            Mail::html($cancelJobMsg, function (Message $message) use ($empEmail) {
                $message->to($empEmail)->subject('Locumkit: Cancellation of job');
            });
            $this->jobsmsController->cancelJobByEmpNotificationToEmployerSms($employer, $job);
        } catch (Exception $e) {
        }
    }

    /* Cancel Emp Job notifiction to Employer if job accepct by private locum*/
    public function cancelJobByEmpNotifyToEmployerIFPrivatefreelancer(User $employer, PrivateUser $user, JobPost $job, $cancel_reason)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $freEmail   = $user['email'];
        $freName  = $user['name'];
        $jobDetails = '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;">
				<tr style="background-color: #f2f2f2;">
				<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
				<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . date('d-m-Y') . '</td>
				</tr>
				<tr>
				<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Private Locum Name</th>
				<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freName . '</td>
				</tr>
				<tr>
				<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Private Locum Email</th>
				<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freEmail . '</td>
				</tr>
			</table>
		';
        $empEmail = $employer['email'];
        $empName  = $employer['firstname'] . ' ' . $employer['lastname'];
        $cancelJobMsg = $header;
        $cancelJobMsg .= '<div style="padding: 25px 50px 5px;text-align: left; font-family: sans-serif;">
			<p>Hi ' . $empName . ',</p>';
        $cancelJobMsg .= '<p>You have just cancelled the following booking:</p>';

        $cancelJobMsg .= $jobDetails;
        //$cancelJobMsg .= '<h5>Your cancellation percentage now is : '.$cancelationPercent.' </h5>';
        $cancelJobMsg .= '<p>We have notified the locum of the cancellation.</p>';
        $cancelJobMsg .= '<div style="margin: 0; margin-top: 20px;border: 1px solid #ff0000;">';
        $cancelJobMsg .= '<h3 style="background: #ff0000;margin: 0; padding: 5px 10px;  color: #fff;">Your reason for cancellation was:</h3><div style="padding: 10px;"> ' . $cancel_reason . '</div>';
        $cancelJobMsg .= '</div>';

        $cancelJobMsg .= '</div>';
        $cancelJobMsg .= $footer;
        try {
            Mail::html($cancelJobMsg, function (Message $message) use ($empEmail) {
                $message->to($empEmail)->subject('Locumkit: Cancellation of job');
            });
            $this->jobsmsController->cancelJobByEmpNotificationToEmployerSms($employer, $job);
        } catch (Exception $e) {
        }
    }

    /* Cancel Emp Job notifiction to Private user */
    public function cancelJobByEmpNotificationToPrivateFreelancer(PrivateUser $freelancer, JobPost $job, $cancel_reason)
    {
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $jobDetails = $this->getJobInfo($job);
        $freEmail   = $freelancer['p_email'];
        $freName    = $freelancer['p_name'];
        $cancelJobMsg = $header;
        $cancelJobMsg .= '<div style="padding: 25px 50px 5px;text-align: left; font-family: sans-serif;">
			<p>Hi ' . $freName . ',</p>';
        $cancelJobMsg .= '<p>We are sorry to inform you that the following job has just been cancelled by the employer.</p>';

        $cancelJobMsg .= $jobDetails;
        $cancelJobMsg .= '<div style="margin: 0; margin-top: 20px;border: 1px solid #ff0000;">';
        $cancelJobMsg .= '<h3 style="background: #ff0000;margin: 0; padding: 5px 10px;  color: #fff;">Reason for cancellation:</h3><div style="padding: 10px;"> ' . $cancel_reason . '</div>';
        $cancelJobMsg .= '</div>';
        $cancelJobMsg .= '</div>';
        $cancelJobMsg .= $footer;
        //echo $closeJobMsg;
        try {
            Mail::html($cancelJobMsg, function (Message $message) use ($freEmail) {
                $message->to($freEmail)->subject('Locumkit Job cancelled');
            });
        } catch (Exception $e) {
        }
    }
}
