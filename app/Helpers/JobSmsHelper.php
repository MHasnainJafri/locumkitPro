<?php

namespace App\Helpers;

use App\Models\JobPost;
use App\Models\ManageSms;
use App\Models\PrivateUser;
use App\Models\User;
use Exception;

class JobSmsHelper
{
    function send_sms($to, $message)
    {
        return false;
        $username = '6PdvjK';
        $password = 'apM7G6';
        $originator = 'locumkit';
        $URL = 'https://www.textmarketer.biz/gateway?username=' . $username . '&password=' . $password . '&message=' . $message . '&orig=' . $originator . '&number=' . $to;
        $fp = fopen($URL, 'r');
        return fread($fp, 1024);

        /* $username = '6PdvjK';
        $password = 'apM7G6';
        $originator = 'test';
        $URL = 'https://www.textmarketer.biz/gateway/?username=' . $username . '&password=' . $password . '&message=' . $message . '&orig=' . $originator . '&number=' . $to;
        return Http::post($URL)->body(); */
    }



    public function insertSms($data)
    {
        $sms = new ManageSms();
        $sms->userid = $data['uid'];
        $sms->contactno = $data['mobile'];
        $sms->sendfor = $data['sendfor'];
        $sms->save();
    }

    public function sendReminderSms(User $user, JobPost $job, $smsLinksArray)
    {
        $mobile =  $user->user_extra_info->mobile;
        $content = 'This is reminder that you have a booking coming up of Jobno.' . $job->id . ' click here for detail ' . make_short_url($smsLinksArray['detail']);
        if ($mobile != '') {
            $smsData = array('uid' => $user->id, 'mobile' => $mobile, 'sendfor' => 'JobReminderSms');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                // save sms in table.
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }


    // use in template/script  cron-on-day.phtml
    public function sendOnDayNotificationToFreelancerSms(User $freelancer, JobPost $job, $smsLinksArray)
    {
        $content = 'Plz confirm your arrival to work today for Job no.' . $job->id . ' click here for Yes ' . make_short_url($smsLinksArray['yes']) . ' & for No ' . make_short_url($smsLinksArray['no']) . ' LocumKit';
        $mobile = $freelancer->user_extra_info()->mobile;
        if ($mobile != '') {
            $smsData = array('uid' => $freelancer->id, 'mobile' => $mobile, 'sendfor' => 'OndayNotificationFreelancer');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }


    // use in template/script  job-search-progress.phtml
    public function jobInvitationFreeSms(User|PrivateUser $freelancer, JobPost $job, $smsLinks)
    {
        if (is_a($freelancer, User::class)) {
            $mobile = $freelancer?->user_extra_info?->mobile;
        } else {
            $mobile = $freelancer->mobile;
        }

        //  $content = 'A new job has been posted. Job no.'.$jobId.' Login here for Accept '.$shorturlController->strurl($smsLinks). ' LocumKit';
        $content = 'A new job has been posted which matches your requirement. Job no.' . $job->id . ' Login From here for Accept Job https://goo.gl/VeUcSz ';

        if ($mobile != '') {
            $smsData = array('uid' => $freelancer->id, 'mobile' => $mobile, 'sendfor' => 'jobInvitationFreelancer');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }


    // use in template/script  job-search-progress.phtml
    public function jobInvitationemployerSms(User $employer, $jobId, $smsLinks)
    {
        $mobile = $employer->user_extra_info->mobile ?? '';
        $content = 'Your job posting has been confirmed and is now live. Jobno.' . $jobId;

        if ($mobile != '') {
            $smsData = array('uid' => $employer->id, 'mobile' => $mobile, 'sendfor' => 'jobInvitationEmployer');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                // save sms in table.
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }


    public function bookingConfirmationfre(User $user, JobPost $job, $smsLinks)
    {
        $mobile = $user->user_extra_info->mobile;
        $content = 'Booking has been confirmed for you. Job no. ' . $job->id . ' For more detail click here https://goo.gl/VeUcSz';

        if ($mobile != '') {
            $smsData = array('uid' => $user->id, 'mobile' => $mobile, 'sendfor' => 'bookingConfirmFreelancer');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }


    public function bookingConfirmationemp(User $user, JobPost $job, $smsLinks)
    {
        $mobile = $user->user_extra_info->mobile;
        $content = 'Someone applies for your job. Job no. ' . $job->id . ' For more detail click here https://goo.gl/VeUcSz';

        if ($mobile != '') {
            $smsData = array('uid' => $user->id, 'mobile' => $mobile, 'sendfor' => 'bookingConfirmEmployer');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
            } catch (Exception $e) {
            }
        }
    }

    public function afterRegisterdEmpSms($mobile, $fname)
    {
        $content = 'Hello ' . $fname . ', Welcome to LocumKit.  Click here https://goo.gl/sYbmQS';
        if ($mobile != '') {
            try {
                $this->send_sms($mobile, $content);
            } catch (Exception $e) {
            }
        }
    }


    public function cancelJobByEmpNotificationToFreelancerSms(User $freelancer, JobPost $job)
    {
        $mobile = $freelancer->user_extra_info->mobile;

        $content = 'Employer has cancelled a job. Job no. ' . $job->id . '  Click here to login https://goo.gl/sYbmQS';

        if ($mobile != '') {
            $smsData = array('uid' => $freelancer->id, 'mobile' => $mobile, 'sendfor' => 'cancelJob');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }

    public function cancelJobByEmpNotificationToEmployerSms(User $employer, JobPost $job)
    {
        $mobile = $employer->user_extra_info->mobile;
        $content = 'You have cancelled a job. Job no. ' . $job->id . '  Click here to login https://goo.gl/sYbmQS';

        if ($mobile != '') {
            $smsData = array('uid' => $employer->id, 'mobile' => $mobile, 'sendfor' => 'cancelJob');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }

    public function cancelJobByFreNotificationToFreelancerSms(User $freelancer, JobPost $job)
    {
        $mobile = $freelancer->user_extra_info->mobile;

        $content = 'You have cancelled a job. Job no. ' . $job->id . '  Click here to login https://goo.gl/sYbmQS';

        if ($mobile != '') {
            $smsData = array('uid' => $freelancer->id, 'mobile' => $mobile, 'sendfor' => 'cancelJob');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }

    public function cancelJobByFreNotificationToEmployerSms(User $employer, JobPost $job)
    {
        $mobile = $employer->user_extra_info->mobile;
        $content = 'Freelancer has cancelled a job. Job no. ' . $job->id . '  Click here to login https://goo.gl/sYbmQS';
        if ($mobile != '') {
            $smsData = array('uid' => $employer->id, 'mobile' => $mobile, 'sendfor' => 'cancelJob');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }


    public function sendweeklyReminderToFreelancerSms(User $user, $smsContent)
    {
        $mobile = $user->user_extra_info->mobile;
        $content = $smsContent . '  Click here to login https://goo.gl/sYbmQS';

        if ($mobile != '') {
            $smsData = array('uid' => $user->id, 'mobile' => $mobile, 'sendfor' => 'weeklyReminderToFreelancer');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                // save sms in table.
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }
    public function sendweeklyReminderToEmployerSms(User $user, $smsContent)
    {
        $mobile = $user->user_extra_info->mobile;
        $content = $smsContent . '  Click here to login https://goo.gl/sYbmQS';

        if ($mobile != '') {
            $smsData = array('uid' => $user->id, 'mobile' => $mobile, 'sendfor' => 'weeklyReminderToEmployer');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                // save sms in table.
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }


    public function sendOnDayNotificationToEmployerSms(User $employer, $jobId)
    {
        $mobile = $employer->user_extra_info->mobile;
        $content = 'One of your freelancer just attend work today for Job no. ' . $jobId . ".";
        if ($mobile != '') {
            $smsData = array('uid' => $employer->id, 'mobile' => $mobile, 'sendfor' => 'OndayNotificationEmployer');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                // save sms in table.
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }

    // use in template/script  cron-feedback.phtml
    public function sendFeedbackNotificationFreSms(User $user, $jobId, $smsfeedback_link_fre)
    {
        $mobile = $user->user_extra_info->mobile;
        $content = 'Would now like you to leave feedback to the employer for Job no. ' . $jobId . ". click here " . make_short_url($smsfeedback_link_fre);
        if ($mobile != '') {
            $smsData = array('uid' => $user->id, 'mobile' => $mobile, 'sendfor' => 'FeedbackNotificationEmployer');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                // save sms in table.
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }

    // use in template/script  cron-feedback.phtml
    public function sendFeedbackNotificationEmpSms(User $user, $jobId, $smsfeedback_link_emp)
    {
        $mobile = $user->user_extra_info->mobile;
        $content = 'Would now like you to leave feedback to the freelancer for Job no. ' . $jobId . ". click here " . make_short_url($smsfeedback_link_emp);
        if ($mobile != '') {
            $smsData = array('uid' => $user->id, 'mobile' => $mobile, 'sendfor' => 'FeedbackNotificationFreelancer');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }

    // use in template/script  cron-feedback.phtml
    public function sendFeedbackNotificationOneWeekAlertSms(User $user, $jobId, $smsfeedback_link, $role)
    {
        $mobile = $user->user_extra_info->mobile;
        if ($role == 2) {
            $content = 'This is a reminder mail to inform you that you left to submit feedback on Job no. ' . $jobId . ". click here " . make_short_url($smsfeedback_link);
        }
        if ($role == 3) {
            $content = 'This is a reminder mail to inform you that you left to submit feedback on Job no. ' . $jobId . ". click here " . make_short_url($smsfeedback_link);
        }

        if ($mobile != '') {
            $smsData = array('uid' => $user->id, 'mobile' => $mobile, 'sendfor' => 'FeedbackNotificationFreelancer');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                // save sms in table.
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }



    public function recievedFeedbackFreelancerNotificationSms(User $freelancer, $jobId, $link)
    {
        $mobile = $freelancer->user_extra_info->mobile;
        $content = 'You have received feedback from employer on Job no. ' . $jobId . ". Feedback will publish against your profile in the next 48 hours.";
        if ($mobile != '') {
            $smsData = array('uid' => $freelancer->id, 'mobile' => $mobile, 'sendfor' => 'recievedFeedbackFreelancer');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                // save sms in table.
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }


    public function recievedFeedbackEmployerNotificationSms(User $employer, $jobId, $link)
    {
        $mobile = $employer->user_extra_info->mobile;
        $content = 'You have received feedback from Freelancer on Job no. ' . $jobId . ". Feedback will publish against your profile in the next 48 hours.";
        if ($mobile != '') {
            $smsData = array('uid' => $employer->id, 'mobile' => $mobile, 'sendfor' => 'recievedFeedbackEmployer');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                // save sms in table.
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }

    public function sendPackageExpiredMailSms(User $user)
    {
        $mobile = $user->user_extra_info->mobile;
        $content = 'Your freelancer account is going to be expired in 7 days, please upgrade it and enjoy the freelancing at Locumkit.';
        if ($mobile != '') {
            $smsData = array('uid' => $user->id, 'mobile' => $mobile, 'sendfor' => 'PackageExpired');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                // save sms in table.
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }


    public function sendProfileSuspendNotificationToFreelancerSms(User $user)
    {
        $mobile = $user->user_extra_info->mobile;
        $content = 'Your guest profile has beed suspended from Locumkit.';
        if ($mobile != '') {
            $smsData = array('uid' => $user->id, 'mobile' => $mobile, 'sendfor' => 'SuspendNotificationToFreelancer');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                // save sms in table.
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }


    public function sendExpireFreezeNotificationSms(User $freelancer, $job_id)
    {
        $mobile = $freelancer->user_extra_info->mobile;
        $content = 'Job no.' . $job_id . ' is locked just for another 5 minuted before it is available to all other applicable freelancer. Please apply now to confirm your booking from click here https://goo.gl/VeUcSz .';
        if ($mobile != '') {
            $smsData = array('uid' => $freelancer->id, 'mobile' => $mobile, 'sendfor' => 'ExpireFreeze');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                // save sms in table.
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }

    // use in template/script  cron-onday-expense.phtml
    public function sendExpenseNotificationSms(User $freelancer, $jobId, $smsLinks)
    {
        $mobile = $freelancer->user_extra_info->mobile;
        $content = 'Please can you enter the amount you have spent today . Click here to Add' . make_short_url($smsLinks) . ' LocumKit';
        if ($mobile != '') {
            $smsData = array('uid' => $freelancer->id, 'mobile' => $mobile, 'sendfor' => 'addExpense');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                // save sms in table.
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }

    public function sendDisputeSubmitNotificationSms(User $user, $jobId, $to, $from)
    {
        $mobile = $user->user_extra_info->mobile;
        $content = 'Hi ' . $to . ' , ' . $from . ' submit dispute on feedback you submitted on job ' . $jobId . ' Please contact admin as soon as possible. LocumKit';
        if ($mobile != '') {
            $smsData = array('uid' => $user->id, 'mobile' => $mobile, 'sendfor' => 'DisputeSubmit');
            try {
                $sts =  $this->send_sms($mobile, $content);
                $smsData['msgstatus'] = $sts;
                // save sms in table.
                $this->insertSms($smsData);
            } catch (Exception $e) {
            }
        }
    }
}