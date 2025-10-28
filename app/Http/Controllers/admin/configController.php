<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
// use App\Mail\SendNewsLetterEmail;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendNewsLetterEmail as sendMail;
use App\Mail\SendMailJobManager;
use App\Mail\SendEmailManager;
use App\Models\coreConfigData;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserAclProfession;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\Exception\NoFileException;
use Illuminate\Support\Facades\Validator;

class configController extends Controller
{
    public function index(){
        $coreConfigData=coreConfigData::all();
        return view('admin.config.index',compact('coreConfigData'));
    }
    public function store(Request $request){
      //validator here

      $requestData = $request->except(['_token', 'submit']);
    
      foreach ($requestData as $identifier => $value) {
          coreConfigData::updateOrInsert(
              ['identifier' => $identifier],
              ['value' => $value]
          );
      }
  
      return redirect()->back()->with('success', 'Data saved successfully.');
      
    }
    public function EmailNewsletter(Request $request){
        if($request->ajax() && $request -> filter_id != 'all'){
            $prof = UserAclProfession::where('id', $request -> filter_id)->first();
            $users = $prof -> getPrfessionUsers -> pluck('email');
            return response()->json([
                'result' => $users
            ], 200);
        }
        else if($request -> ajax() && $request -> filter_id == 'all'){
            $users = User::all()->slice(1)->pluck('email');
            return response()->json([
                'result' => $users
            ], 200);
        }
        $users = User::all()->slice(1);
        $profession = UserAclProfession::all();
        return view('admin.config.emailNewsLetter', compact('users', 'profession'));
    }
    
    public function EmailNewsletterEmail(Request $request){
        
        // if($request -> email == 'seperate_email'){
        //     dd($request->all());
        //     $email = explode(';',$request -> user_marketing_mail_ids);
        //     $request->merge(['email' => $email]);
        //     $request->merge(['mail_subject' => $request -> user_marketing_mail_subjectss]);
        //     $request->merge(['mail_message' => $request -> user_marketing_mail_message]);
        //     foreach($request -> email as $key => $email){
        //         Mail::to($email)->send(new sendMail($request->mail_subject, $request -> mail_subject, $request -> mail_message));
        //         // dispatch(new sendMail($email, $request -> mail_subject, $request -> mail_message)); 
        //     }
        //     return redirect()->back()->with('success', 'Send successfully');
        // }
        if ($request->email == 'seperate_email') {
           
            $emails = explode(';', $request->user_marketing_mail_ids);
    
            // Validate email addresses
            $validEmails = [];
            $invalidEmails = [];
            foreach ($emails as $email) {
                $email = trim($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL) && $this->checkDomain($email)) {
                    $validEmails[] = $email;
                } else {
                    $invalidEmails[] = $email;
                }
            }
    
            if (count($invalidEmails) > 0) {
                // return redirect()->back()->withErrors([
                //     'email' => 'Some email addresses are invalid: ' . implode(', ', $invalidEmails)
                // ])->withInput();
                return redirect()->back()->withErrors([
                    'email' => 'Some email addresses are invalid: ' . implode(', ', $invalidEmails)
                ])->withInput()->with('activeAccordion', 2);
            }
    
            $request->merge(['email' => $validEmails]);
            $request->merge(['mail_subject' => $request->user_marketing_mail_subjectss]);
            $request->merge(['mail_message' => $request->user_marketing_mail_message]);
    
            foreach ($validEmails as $email) {
                try {
                    Mail::to($email)->send(new sendMail($request->mail_subject, $request->mail_subject, $request->mail_message));
                } catch (\Exception $e) {
                    Log::error("Failed to send email to {$email}: " . $e->getMessage());
                }
            }
    
            // return redirect()->back()->with('success', 'Emails sent successfully!');
            return redirect()->back()->with('success', 'Emails sent successfully!')->with('activeAccordion', 2);
        }
        elseif($request->sendmails='1'){
            // dd($request->all());
            $request->validate([
                'email' => 'required|array|min:1',
                'mail_message' => 'required|string|min:10',
                'mail_subject' => 'required',
            ], [
                'email.required' => 'The email field is required.',
                'email.*.email' => 'One or more email addresses are invalid.',
                'mail_message.required' => 'The message field is required.',
                'mail_message.string' => 'The message must be a valid string.',
                'mail_message.min' => 'The message must be at least 10 characters.',
                'mail_subject.required' => 'The subject field is required.',
            ]);
            foreach($request -> email as $key => $email){
                Mail::to($email)->send(new sendMail($request->mail_subject, $request -> mail_subject, $request -> mail_message));
                // dispatch(new sendMail($email, $request -> mail_subject, $request -> mail_message));
            }
            return redirect()->back()->with('success', 'Send successfully');
        }
    }
    
    private function checkDomain($email)
    {
        $domain = substr(strrchr($email, "@"), 1);
        return checkdnsrr($domain, 'MX');
    }
    
    public function EmailNewsletterEmailManager(Request $request){
        foreach($request -> email as $key => $email){
            Mail::to($email)->send(new SendEmailManager($request['user_mail_subject'], $request['user_mail_message']));
            // dispatch(new SendMailJobManager('faizi12570@gmail.com', $request -> user_mail_subject, $request -> user_mail_message));
        }
        return redirect()->back()->with('success', 'Send Successfully');
    }
    public function NotificationSettings(){
        $notification = Notification::first();
        return view('admin.config.notificatioSettings', compact('notification'));
    }
    public function NotiSetting(Request $request){
    $update = [
        'attend_job_notification'   => $request->attend_job_notification,
        'expenses_job_notification' => $request->expenses_job_notification,
        'feedback_job_notification' => $request->feedback_job_notification,
    ];
    $notification = Notification::first();
    $notification->update($update);
        return redirect()->back()->with('success', 'Updated Successfully');
    }
}
