<?php

namespace App\Http\Controllers;

use App\Helpers\JobMailHelper;
use App\Models\Blog;
use App\Models\ExpenseType;
use App\Models\FeedbackQuestion;
use App\Models\FinanceExpense;
use App\Models\FinanceIncome;
use App\Models\FreelancerPrivateFinance;
use App\Models\FreelancerPrivateJob;
use App\Models\IndustryNews;
use App\Models\JobFeedback;
use App\Models\JobOnDay;
use App\Models\JobPost;
use App\Models\PkgPrivilegeInfo;
use App\Models\User;
use App\Models\UserAclPackage;
use App\Models\UserQuestion;
use App\Models\SubscribeUser;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        return view('index');
    }
    public function thankYou(Request $request)
    {
        $msg = "";
        if ($request->has('package') && $request->input('package') == "change") {
            $msg = "Package has been successfully changed, now you can enjoy the services of locumkit.";
        } else {
            if ($request->has('type') && $request->input('type') == 'freelancer') {
                $msg = "Thank you for registering. <br/> You are now one step away from benefiting all that LocumKit has. <br/>Please check your email to verify your account.";
            }
            if ($request->has('type') && $request->input('type') == 'employer') {
                $msg = "<p>Thank you for joining Locumkit.</p><p> We have received your account application and your details are currently being verified by our team. Verification can take up to 48 hours and once this process is complete we will notify you.</p><p> Please note that during this process you will be unable to access any of the features of the site</p>";
            }
        }
        return view('thanks', ["msg" => $msg]);
    }

    public function editpages($name)
    {
        $viewsDirectory = resource_path('views');
        $filesWithParents = $this->getFilesWithParents($viewsDirectory);

        $bladeFilePath = resource_path('views/' . str_replace('.', '/', $name) . '.blade.php');
        if (File::exists($bladeFilePath)) {
            $fileContent = file_get_contents($bladeFilePath);

            $startPosition = strpos($fileContent, "@section('content')");
            $endPosition = strpos($fileContent, "@endsection", $startPosition);

            $content = substr($fileContent, $startPosition, $endPosition - $startPosition);
            $content = preg_replace('/@.*?(\n|$)/', '', $content);
            foreach ($filesWithParents as &$file) {
                $file = str_replace('.blade.php', '', $file);
            }


            return view('admin.pages.edit', compact('name', 'content', 'filesWithParents'));
        } else {
            abort(404);
        }

    }


    public function getFilesWithParents($directory)
    {
        $filesByParents = [];

        $allFiles = File::allFiles($directory);
        // dd($allFiles);

        foreach ($allFiles as $file) {
            $relativePath = $file->getRelativePath();
            $filename = $file->getFilename();

            $parentKey = $relativePath ?: 'root';
            if (!isset($filesByParents[$parentKey])) {
                $filesByParents[$parentKey] = [];
            }

            $filesByParents[$parentKey][] = $filename;
        }
        //  dd($filesByParents);
        return $filesByParents;
    }

    public function locums()
    {
        return view('locum');
    }

    public function employer()
    {
        return view('employer');
    }
    public function about()
    {
        return view('about');
    }
    public function benefits()
    {
        return view('benefits');
    }
    public function dbs()
    {
        return view('dbs');
    }

    public function contact(Request $request)
    {
        if ($request->isMethod("GET")) {
            return view('contact');
        } else if ($request->isMethod("POST")) {

            $request->validate([
                "name" => ["required", "string", "min:3", "max:50"],
                "email" => ["required", "email", "regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.com$/"], 
                "message" => ["required", "string", "min:10", "max:500"],
                "g-recaptcha-response" => ["required"] 
            ]);

            $recaptcha = isset($data["g-recaptcha-response"]) ? $data["g-recaptcha-response"] : "";
            $url = "https://www.google.com/recaptcha/api/siteverify?secret=" . config('app.google_recaptcha_secret_key') . '&response=' . $recaptcha;
            $response = file_get_contents($url);
            $response = json_decode($response);
            $name = $request->input('name');
            $email = $request->input('email');
            $message = $request->input('message');
            try {
                    Mail::send("mail.contact-mail-admin", [
    "name" => $name,
    "email" => $email,
    "userMessage" => $message, // Renamed to avoid conflict
], function ($mailable) {
    $mailable->subject(config("app.name") . " new contact message");
    $mailable->to(config('app.admin_mail'));
});
            } catch (Exception $ignore) {
               // dd($ignore);
            }
            try {
                Mail::send("mail.contact-mail-sender", compact("name"), function ($mailable) use ($email) {
                    $mailable->subject(config("app.name") . " Contact Confirmation");
                    $mailable->to($email);
                });
            } catch (Exception $ignore) {
            }

            return back()->with("success", "Contact mail send successfully");
        } else {
            return abort(404);
        }
    }

    public function accountancy()
    {
        return view('accountancy');
    }

    public function showNewsPost($slug)
    {
        $news_post = IndustryNews::where("slug", $slug)->first();
        if (!$news_post) {
            return abort(404);
        }
        $meta = [
            'title' => $news_post->metatitle,
            'description' => $news_post->metadescription,
            'keywords' => $news_post->metakeywords
        ];

        return view('freelancer.blog-post', ["title" => $news_post->title, "image_path" => $news_post->image_path, "date" => $news_post->created_at->format("d-m-Y"), "content" => $news_post->description, "meta" => $meta]);
    }
    public function showBlogPost($slug)
    {
        $news_post = Blog::where("slug", $slug)->first();
        if (!$news_post) {
            return abort(404);
        }
        $meta = [
            'title' => $news_post->metatitle,
            'description' => $news_post->metadescription,
            'keywords' => $news_post->metakeywords
        ];

        return view('freelancer.blog-post', ["title" => $news_post->title, "image_path" => $news_post->image_path, "date" => $news_post->created_at->format("d-m-Y"), "content" => $news_post->description, "meta" => $meta]);
    }

    public function termAndCondition()
    {
        return view('term-condition');
    }
    public function privacyPolicy()
    {
        return view('privacy-policy');
    }
    public function showSitemap()
    {
        return view('sitemap');
    }
    public function showMaps()
    {
        return view('maps');
    }
    public function showPackage()
    {
        $paid_packages = UserAclPackage::getPaidPackagePrices();
        $packages_info = PkgPrivilegeInfo::all();
        // dd($paid_packages,$packages_info);
        return view('package', compact('paid_packages', 'packages_info'));
    }

    public function blogs()
    {
        $blogs = Blog::query()->orderBy("created_at", "DESC")->paginate(10);
        return view('blogs', compact('blogs'));
    }
    public function blogsRecentPosts()
    {
        $blogs = Blog::query()->where("blog_category_id", 1)->orderBy("created_at", "DESC")->paginate(10);
        return view('blogs', compact('blogs'));
    }

    public function showFeedbackReport($for_user_role, $user_id)
    {
        if ($for_user_role != "employer" && $for_user_role != "freelancer") {
            return abort(404);
        }
        if (!$user_id) {
            return abort(404);
        }
        try {
            $user_id = decrypt($user_id);
        } catch (Exception) {
            return abort(404);
        }
        $feedbacks = array();
        if ($for_user_role == "employer") {
            $feedbacks = JobFeedback::with('employer')->where("freelancer_id", $user_id)->where("user_type", "employer")->where("status", 1)->whereDate("created_at", ">=", today()->subMonths(6)->startOfMonth())->get();
        } else if ($for_user_role == "freelancer") {
            $feedbacks = JobFeedback::with('freelancer')->where("employer_id", $user_id)->where("user_type", "freelancer")->where("status", 1)->whereDate("created_at", ">=", today()->subMonths(6)->startOfMonth())->get();
        }

        $qusdata = $qus = $quscount =  array();
        foreach ($feedbacks as $currentFeedback) {
            $feedback_data = json_decode($currentFeedback['feedback'], true);
            if ($feedback_data) {
                foreach ($feedback_data as $feedback) {
                    $queid = isset($feedback['qusId']) ? $feedback['qusId'] : "";
                    $qusdata[$queid] = isset($qusdata[$queid]) ? $qusdata[$queid] : 0;
                    $quscount[$queid] = isset($quscount[$queid]) ? $quscount[$queid] : 0;

                    $qusdata[$queid] += isset($feedback['qusRate']) ? $feedback['qusRate'] : 0;
                    $quscount[$queid] += 1;
                    $qus[$queid] = $feedback['qus'];
                }
            }
        }

        return view("feedback-report", compact("for_user_role", "user_id", "feedbacks", "qusdata", "qus", "quscount"));
    }

//1
    // public function attendance(Request $request)
    // {
    //     $mailController = new JobMailHelper();
    //     try {
    //         $job_id = decrypt($request->query("job_id"));
    //         $user_id = decrypt($request->query("user_id"));
    //         $action = decrypt($request->query("action"));
    //         $job_type = decrypt($request->query("job_type"));
    //     } catch (DecryptException $e) {
    //         return abort(404);
    //     }
    //     if ($user_id != Auth::user()->id) {
    //         abort(403);
    //     }
    //     $check_job_status = 0;
    //     $job_on_day = null;
    //     if (Auth::user()->user_acl_role_id == User::USER_ROLE_LOCUM && $action == "yes") {
    //         if ($job_type == "private" && $job_id) {
    //             $private_job = FreelancerPrivateJob::findOrFail($job_id);
    //             if ($private_job->status == FreelancerPrivateJob::STATUS_NOTIFIED_ON_JOB_DAY) {
    //                 FreelancerPrivateFinance::create([
    //                     "freelancer_id" => $user_id,
    //                     "freelancer_private_job_id" => $job_id,
    //                     "job_rate" => $private_job->job_rate,
    //                     "job_date" => $private_job->job_date,
    //                     "employer_name" => $private_job->emp_name,
    //                 ]);
    //                 FinanceIncome::create([
    //                     "job_id" => $job_id,
    //                     "job_type" => 2,
    //                     "freelancer_id" => $user_id,
    //                     "employer_id" => null,
    //                     "job_rate" => $private_job->job_rate,
    //                     "job_date" => $private_job->job_date,
    //                     "income_type" => 1,
    //                     "is_bank_transaction_completed" => false,
    //                     "bank_transaction_date" => null,
    //                     "store" => $private_job->emp_name,
    //                     "location" => $private_job->job_location,
    //                     "supplier" => $private_job->emp_name,
    //                     "status" => 1,
    //                 ]);

    //                 $private_job->status = FreelancerPrivateJob::STATUS_JOB_ATTENDED;
    //                 $private_job->save();
    //                 $check_job_status = 1;
    //             } else {
    //                 $check_job_status = 6;
    //             }
    //         } else {
    //             $job = JobPost::findOrFail($job_id);
    //             $job_on_day = JobOnDay::where("job_post_id", $job_id)->where("freelancer_id", $user_id)->whereDate("job_date", today())->where("status", JobOnDay::STATUS_NOT_ATTEND)->first();
    //             if ($job_on_day) {
    //                 $job_on_day->status = JobOnDay::STATUS_EMPLOYER_VERIFIED_ATTENDANCE;
    //                 $job_on_day->save();
    //                 $encrypted_job_id = encrypt($job_id);
    //                 $encrypted_employer_id = encrypt($job_on_day->employer_id);
    //                 $encrypted_yes = encrypt("yes");
    //                 $encrypted_no = encrypt("no");
    //                 $job_type_encrypted = encrypt("website");

    //                 $yesBtnLinkHref = url("/attendance?job_id={$encrypted_job_id}&user_id={$encrypted_employer_id}&action={$encrypted_yes}&job_type={$job_type_encrypted}");
    //                 $noBtnLinkHref = url("/attendance?job_id={$encrypted_job_id}&user_id={$encrypted_employer_id}&action={$encrypted_no}&job_type={$job_type_encrypted}");

    //                 $btnLinks = '<a href="' . $yesBtnLinkHref . '" style="padding: 8px 30px; font-size: 16px; font-weight: 700; background: #00A9E0; color: #fff; ">Yes</a> <a href="' . $noBtnLinkHref . '" style="padding: 8px 30px; font-size: 16px; font-weight: 700; background: #ff0000; color: #fff; ">No</a> ';
    //                 $mailController->sendOnDayNotificationToEmployer($job, $job_on_day->freelancer, $job->employer, $btnLinks);

    //                 FinanceIncome::create([
    //                     "job_id" => $job_id,
    //                     "job_type" => 1,
    //                     "freelancer_id" => $user_id,
    //                     "employer_id" => $job_on_day->employer_id,
    //                     "job_rate" => $job->job_rate,
    //                     "job_date" => $job->job_date,
    //                     "income_type" => 1,
    //                     "is_bank_transaction_completed" => false,
    //                     "bank_transaction_date" => null,
    //                     "store" => $job->job_store->store_name,
    //                     "location" => $job->job_region,
    //                     "supplier" => $job->employer->first_name . ' ' . $job->employer->last_name,
    //                     "status" => 1,
    //                 ]);
    //                 $check_job_status = 1;
    //             } else {
    //                 $check_job_status = 5;
    //             }
    //         }
    //     } elseif (Auth::user()->user_acl_role_id == User::USER_ROLE_EMPLOYER && $action == "yes") {
    //         $job = JobPost::findOrFail($job_id);
    //         $job_on_day = JobOnDay::where("job_post_id", $job_id)->where("employer_id", $user_id)->whereDate("job_date", today())->where("status", JobOnDay::STATUS_FREELANCER_ATTEND)->first();
    //         if ($job_on_day) {
    //             $job_on_day->status = JobOnDay::STATUS_EMPLOYER_VERIFIED_ATTENDANCE;
    //             $job_on_day->save();
    //             $check_job_status = 2;
    //         } else {
    //             $check_job_status = 4;
    //         }
    //     } elseif (Auth::user()->user_acl_role_id == User::USER_ROLE_LOCUM && $action == "no") {
    //         return redirect(route('freelancer.job-listing'))->with("success", "Please cancel job from here if not attending");
    //     } else {
    //         return abort(404);
    //     }
    //     return view('shared.attendance', compact('check_job_status', 'job_on_day'));
    // }
    
//2    
//     public function attendance(Request $request)
//     {
//         \Log::info("Attendance called: job_id={$request->query('job_id')}, user_id={$request->query('user_id')}, action={$request->query('action')}, job_type={$request->query('job_type')}");
    
//         $mailController = new JobMailHelper();
//         try {
//             $job_id = decrypt($request->query("job_id"));
//             $user_id = decrypt($request->query("user_id"));
//             $action = decrypt($request->query("action"));
//             $job_type = decrypt($request->query("job_type"));
//         } catch (DecryptException $e) {
//             \Log::error("Decryption failed: " . $e->getMessage());
//             return abort(404);
//         }
    
//         if ($user_id != Auth::user()->id) {
//             \Log::warning("Unauthorized: user_id=$user_id, auth_id=" . Auth::user()->id);
//             abort(403);
//         }
    
//         $check_job_status = 0;
//         $job_on_day = null;
    
//         if (Auth::user()->user_acl_role_id == User::USER_ROLE_LOCUM && $action == "yes") {
//             if ($job_type == "private" && $job_id) {
//                 $private_job = FreelancerPrivateJob::findOrFail($job_id);
//                 $is_canceled = PrivateUserJobAction::where("job_post_id", $job_id)
//                     ->where("private_user_id", $user_id)
//                     ->where("status", PrivateUserJobAction::ACTION_CANCEL)
//                     ->exists();
//                 if ($is_canceled) {
//                     \Log::info("Private job canceled: job_id=$job_id, user_id=$user_id");
//                     $check_job_status = 8;
//                 } elseif ($private_job->status == FreelancerPrivateJob::STATUS_NOTIFIED_ON_JOB_DAY) {
//                     FreelancerPrivateFinance::create([
//                         "freelancer_id" => $user_id,
//                         "freelancer_private_job_id" => $job_id,
//                         "job_rate" => $private_job->job_rate,
//                         "job_date" => $private_job->job_date,
//                         "employer_name" => $private_job->emp_name,
//                     ]);
//                     FinanceIncome::create([
//                         "job_id" => $job_id,
//                         "job_type" => 2,
//                         "freelancer_id" => $user_id,
//                         "employer_id" => null,
//                         "job_rate" => $private_job->job_rate,
//                         "job_date" => $private_job->job_date,
//                         "income_type" => 1,
//                         "is_bank_transaction_completed" => false,
//                         "bank_transaction_date" => null,
//                         "store" => $private_job->emp_name,
//                         "location" => $private_job->job_location,
//                         "supplier" => $private_job->emp_name,
//                         "status" => 1,
//                     ]);
//                     $private_job->status = FreelancerPrivateJob::STATUS_JOB_ATTENDED;
//                     $private_job->save();
//                     $check_job_status = 1;
//                     \Log::info("Private job attendance confirmed: job_id=$job_id, user_id=$user_id");
//                 } else {
//                     $check_job_status = 6;
//                     \Log::warning("Private job not in NOTIFIED_ON_JOB_DAY: job_id=$job_id, status={$private_job->status}");
//                 }
//             } else {
//                 $job = JobPost::findOrFail($job_id);
//                 if ($job->job_status == JobPost::JOB_STATUS_CANCELED) {
//                     \Log::info("Website job canceled: job_id=$job_id, user_id=$user_id");
//                     $check_job_status = 8;
//                 } else {
//                     $job_on_day = JobOnDay::where("job_post_id", $job_id)
//                         ->where("freelancer_id", $user_id)
//                         ->whereDate("job_date", today())
//                         ->where("status", JobOnDay::STATUS_NOT_ATTEND)
//                         ->first();
//                     if ($job_on_day) {
//                         $job_on_day->status = JobOnDay::STATUS_EMPLOYER_VERIFIED_ATTENDANCE;
//                         $job_on_day->save();
//                         $encrypted_job_id = encrypt($job_id);
//                         $encrypted_employer_id = encrypt($job_on_day->employer_id);
//                         $encrypted_yes = encrypt("yes");
//                         $encrypted_no = encrypt("no");
//                         $job_type_encrypted = encrypt("website");
//                         $yesBtnLinkHref = url("/attendance?job_id={$encrypted_job_id}&user_id={$encrypted_employer_id}&action={$encrypted_yes}&job_type={$job_type_encrypted}");
//                         $noBtnLinkHref = url("/attendance?job_id={$encrypted_job_id}&user_id={$encrypted_employer_id}&action={$encrypted_no}&job_type={$job_type_encrypted}");
//                         $btnLinks = '<a href="' . $yesBtnLinkHref . '" style="padding: 8px 30px; font-size: 16px; font-weight: 700; background: #00A9E0; color: #fff; ">Yes</a> <a href="' . $noBtnLinkHref . '" style="padding: 8px 30px; font-size: 16px; font-weight: 700; background: #ff0000; color: #fff; ">No</a> ';
//                         $mailController->sendOnDayNotificationToEmployer($job, $job_on_day->freelancer, $job->employer, $btnLinks);
//                         FinanceIncome::create([
//                             "job_id" => $job_id,
//                             "job_type" => 1,
//                             "freelancer_id" => $user_id,
//                             "employer_id" => $job_on_day->employer_id,
//                             "job_rate" => $job->job_rate,
//                             "job_date" => $job->job_date,
//                             "income_type" => 1,
//                             "is_bank_transaction_completed" => false,
//                             "bank_transaction_date" => null,
//                             "store" => $job->job_store->store_name,
//                             "location" => $job->job_region,
//                             "supplier" => $job->employer->first_name . ' ' . $job->employer->last_name,
//                             "status" => 1,
//                         ]);
//                         $check_job_status = 1;
//                         \Log::info("Website job attendance confirmed: job_id=$job_id, user_id=$user_id");
//                     } else {
//                         $check_job_status = 5;
//                         \Log::warning("No valid JobOnDay: job_id=$job_id, user_id=$user_id");
//                     }
//                 }
//             }
//         } elseif (Auth::user()->user_acl_role_id == User::USER_ROLE_EMPLOYER && $action == "yes") {
//             $job = JobPost::findOrFail($job_id);
//             $job_on_day = JobOnDay::where("job_post_id", $job_id)
//                 ->where("employer_id", $user_id)
//                 ->whereDate("job_date", today())
//                 ->where("status", JobOnDay::STATUS_FREELANCER_ATTEND)
//                 ->first();
//             if ($job_on_day) {
//                 $job_on_day->status = JobOnDay::STATUS_EMPLOYER_VERIFIED_ATTENDANCE;
//                 $job_on_day->save();
//                 $check_job_status = 2;
//                 \Log::info("Employer verified attendance: job_id=$job_id, employer_id=$user_id");
//             } else {
//                 $check_job_status = 4;
//                 \Log::warning("No valid JobOnDay for employer: job_id=$job_id, employer_id=$user_id");
//             }
//         } elseif (Auth::user()->user_acl_role_id == User::USER_ROLE_LOCUM && $action == "no") {
//             $check_job_status = 3;
//             \Log::info("Redirecting for 'no' action: user_id=$user_id");
//         } else {
//             \Log::warning("Invalid role/action: user_id=$user_id, role=" . Auth::user()->user_acl_role_id . ", action=$action");
//             return abort(404);
//         }
    
//         \Log::info("Rendering shared.attendance: job_id=$job_id, check_job_status=$check_job_status");
//         return view('shared.attendance', compact('check_job_status', 'job_on_day'));
// }

//3
public function attendance(Request $request)
{
    \Log::info("Attendance called: job_id={$request->query('job_id')}, user_id={$request->query('user_id')}, action={$request->query('action')}, job_type={$request->query('job_type')}");

    $mailController = new JobMailHelper();
    try {
        $job_id = decrypt($request->query("job_id"));
        $user_id = decrypt($request->query("user_id"));
        $action = decrypt($request->query("action"));
        $job_type = decrypt($request->query("job_type"));
    } catch (DecryptException $e) {
        \Log::error("Decryption failed: " . $e->getMessage());
        return abort(404);
    }

    if ($user_id != Auth::user()->id) {
        \Log::warning("Unauthorized: user_id=$user_id, auth_id=" . Auth::user()->id);
        abort(403);
    }

    $check_job_status = 0;
    $job_on_day = null;

    if (Auth::user()->user_acl_role_id == User::USER_ROLE_LOCUM && $action == "yes") {
        if ($job_type == "private" && $job_id) {
            $private_job = FreelancerPrivateJob::find($job_id);
            if (!$private_job) {
                \Log::info("Private job deleted: job_id=$job_id, user_id=$user_id");
                $check_job_status = 9;
            } elseif (PrivateUserJobAction::where("job_post_id", $job_id)
                ->where("private_user_id", $user_id)
                ->where("status", PrivateUserJobAction::ACTION_CANCEL)
                ->exists()) {
                \Log::info("Private job canceled: job_id=$job_id, user_id=$user_id");
                $check_job_status = 8;
            } elseif ($private_job->status == FreelancerPrivateJob::STATUS_NOTIFIED_ON_JOB_DAY) {
                FreelancerPrivateFinance::create([
                    "freelancer_id" => $user_id,
                    "freelancer_private_job_id" => $job_id,
                    "job_rate" => $private_job->job_rate,
                    "job_date" => $private_job->job_date,
                    "employer_name" => $private_job->emp_name,
                ]);
                FinanceIncome::create([
                    "job_id" => $job_id,
                    "job_type" => 2,
                    "freelancer_id" => $user_id,
                    "employer_id" => null,
                    "job_rate" => $private_job->job_rate,
                    "job_date" => $private_job->job_date,
                    "income_type" => 1,
                    "is_bank_transaction_completed" => false,
                    "bank_transaction_date" => null,
                    "store" => $private_job->emp_name,
                    "location" => $private_job->job_location,
                    "supplier" => $private_job->emp_name,
                    "status" => 1,
                ]);
                $private_job->status = FreelancerPrivateJob::STATUS_JOB_ATTENDED;
                $private_job->save();
                $check_job_status = 1;
                \Log::info("Private job attendance confirmed: job_id=$job_id, user_id=$user_id");
            } else {
                $check_job_status = 6;
                \Log::warning("Private job not in NOTIFIED_ON_JOB_DAY: job_id=$job_id, status={$private_job->status}");
            }
        } else {
            $job = JobPost::find($job_id);
            if (!$job) {
                \Log::info("Website job deleted: job_id=$job_id, user_id=$user_id");
                $check_job_status = 9;
            } elseif ($job->job_status == JobPost::JOB_STATUS_CANCELED) {
                \Log::info("Website job canceled: job_id=$job_id, user_id=$user_id");
                $check_job_status = 8;
            } else {
                $job_on_day = JobOnDay::where("job_post_id", $job_id)
                    ->where("freelancer_id", $user_id)
                    ->whereDate("job_date", today())
                    ->where("status", JobOnDay::STATUS_NOT_ATTEND)
                    ->first();
                if ($job_on_day) {
                    $job_on_day->status = JobOnDay::STATUS_EMPLOYER_VERIFIED_ATTENDANCE;
                    $job_on_day->save();
                    $encrypted_job_id = encrypt($job_id);
                    $encrypted_employer_id = encrypt($job_on_day->employer_id);
                    $encrypted_yes = encrypt("yes");
                    $encrypted_no = encrypt("no");
                    $job_type_encrypted = encrypt("website");
                    $yesBtnLinkHref = url("/attendance?job_id={$encrypted_job_id}&user_id={$encrypted_employer_id}&action={$encrypted_yes}&job_type={$job_type_encrypted}");
                    $noBtnLinkHref = url("/attendance?job_id={$encrypted_job_id}&user_id={$encrypted_employer_id}&action={$encrypted_no}&job_type={$job_type_encrypted}");
                    $btnLinks = '<a href="' . $yesBtnLinkHref . '" style="padding: 8px 30px; font-size: 16px; font-weight: 700; background: #00A9E0; color: #fff; ">Yes</a> <a href="' . $noBtnLinkHref . '" style="padding: 8px 30px; font-size: 16px; font-weight: 700; background: #ff0000; color: #fff; ">No</a> ';
                    $mailController->sendOnDayNotificationToEmployer($job, $job_on_day->freelancer, $job->employer, $btnLinks);
                    FinanceIncome::create([
                        "job_id" => $job_id,
                        "job_type" => 1,
                        "freelancer_id" => $user_id,
                        "employer_id" => $job_on_day->employer_id,
                        "job_rate" => $job->job_rate,
                        "job_date" => $job->job_date,
                        "income_type" => 1,
                        "is_bank_transaction_completed" => false,
                        "bank_transaction_date" => null,
                        "store" => $job->job_store->store_name,
                        "location" => $job->job_region,
                        "supplier" => $job->employer->first_name . ' ' . $job->employer->last_name,
                        "status" => 1,
                    ]);
                    $check_job_status = 1;
                    \Log::info("Website job attendance confirmed: job_id=$job_id, user_id=$user_id");
                } else {
                    $check_job_status = 5;
                    \Log::warning("No valid JobOnDay: job_id=$job_id, user_id=$user_id");
                }
            }
        }
    } elseif (Auth::user()->user_acl_role_id == User::USER_ROLE_EMPLOYER && $action == "yes") {
        $job = JobPost::find($job_id);
        if (!$job) {
            \Log::info("Website job deleted: job_id=$job_id, employer_id=$user_id");
            $check_job_status = 9;
        } else {
            $job_on_day = JobOnDay::where("job_post_id", $job_id)
                ->where("employer_id", $user_id)
                ->whereDate("job_date", today())
                ->where("status", JobOnDay::STATUS_FREELANCER_ATTEND)
                ->first();
            if ($job_on_day) {
                $job_on_day->status = JobOnDay::STATUS_EMPLOYER_VERIFIED_ATTENDANCE;
                $job_on_day->save();
                $check_job_status = 2;
                \Log::info("Employer verified attendance: job_id=$job_id, employer_id=$user_id");
            } else {
                $check_job_status = 4;
                \Log::warning("No valid JobOnDay for employer: job_id=$job_id, employer_id=$user_id");
            }
        }
    } elseif (Auth::user()->user_acl_role_id == User::USER_ROLE_LOCUM && $action == "no") {
        $check_job_status = 3;
        \Log::info("No action selected: user_id=$user_id");
    } else {
        \Log::warning("Invalid role/action: user_id=$user_id, role=" . Auth::user()->user_acl_role_id . ", action=$action");
        return abort(404);
    }

    \Log::info("Rendering shared.attendance: job_id=$job_id, check_job_status=$check_job_status");
    return view('shared.attendance', compact('check_job_status', 'job_on_day'));
}

    public function feedback(Request $request)
    {
        try {
            $job_id = decrypt($request->query("job_id"));
            $user_id = decrypt($request->query("user_id"));
            $user_type = decrypt($request->query("user_type"));
        } catch (DecryptException $e) {
            return abort(404);
        }
        if (!in_array($user_type, ["freelancer", "employer"])) {
            return abort(404);
        }
        if ((Auth::user()->user_acl_role_id == User::USER_ROLE_LOCUM && $user_type == "employer") || (Auth::user()->user_acl_role_id == User::USER_ROLE_EMPLOYER && $user_type == "freelancer")) {
            return redirect("/")->with("error", "You are not correct role to give this feedback");
        }
        $job = JobPost::findOrFail($job_id);
        $job_on_day_count = JobOnDay::where("job_post_id", $job_id)->where($user_type . "_id", $user_id)->where("status", ">=", JobOnDay::STATUS_FEEDBACK_NOTIFICATION_SEND)->first();
        if (is_null($job_on_day_count)) {
            return abort(404);
        }
        $page_title = '';
        $dashboard_url = "";
        $allFeedbackQusArray = FeedbackQuestion::where("question_cat_id", Auth::user()->user_acl_profession_id)->where("question_status", 1)->where("question_" . $user_type, "!=", "")->get();
        if (Auth::user()->user_acl_role_id == User::USER_ROLE_LOCUM) {
            $page_title = 'Locum Feedback';
            $dashboard_url = "/freelancer/dashboard";

            $job_to_user = $job->employer;
        } else if (Auth::user()->user_acl_role_id == User::USER_ROLE_EMPLOYER) {
            $page_title = 'Employer Feedback';
            $dashboard_url = "/employer/dashboard";

            $job_to_user = $job_on_day_count->freelancer;
        } else {
            return abort(404);
        }


        $alreadyFeedbackCount = JobFeedback::where("job_id", $job_id)->where("user_type", $user_type)->where($user_type . "_id", $user_id)->count();

        return view('shared.feedback', compact('page_title', 'allFeedbackQusArray', 'job', 'dashboard_url', 'alreadyFeedbackCount', 'job_to_user', 'user_type', 'job_on_day_count'));
    }

    public function postFeedback(Request $request)
    {
        $mailController = new JobMailHelper();
        $request->validate([
            "employer_id" => "required",
            "freelancer_id" => "required",
            "job_id" => "required",
            "total-rating" => "required",
            "ratevalue" => "required|array",
            "fdqus" => "required|array",
            "fdqusid" => "required|array",
            "user_type" => "required",
            "cat_id" => "required",
            "comment" => "required"
        ]);
        $feedback_question_ids = $request->input("fdqusid");
        $feedback_questions = $request->input("fdqus");
        $feedback_question_rate = $request->input("ratevalue");
        $feedbackArray = [];
        foreach ($feedback_question_ids as $key => $value) {
            $feedbackArray[] = [
                'qusId'        => $value,
                'qus'        => $feedback_questions[$key],
                'qusRate'    => $feedback_question_rate[$key]
            ];
        }
        if (sizeof($feedbackArray) == 0) {
            return back()->with("error", "You must have to choose some stars for questions");
        }

        $employer_id = $request->input("employer_id");
        $freelancer_id = $request->input("freelancer_id");
        $job_id = $request->input("job_id");
        $rating = $request->input("total-rating");
        $comments = $request->input("comment");
        $user_type = $request->input("user_type");
        $cat_id = $request->input("cat_id");

        $job = JobPost::findOrFail($job_id);
        $employer = User::findOrFail($employer_id);
        $freelancer = User::findOrFail($freelancer_id);

        $feedback_string = json_encode($feedbackArray);
        $job_feedback = JobFeedback::create([
            "employer_id" => $employer_id,
            "freelancer_id" => $freelancer_id,
            "job_id" => $job_id,
            "rating" => $rating,
            "feedback" => $feedback_string,
            "comments" => $comments,
            "user_type" => $user_type,
            "cat_id" => $cat_id,
        ]);

        $dashboard = "/";
        if (Auth::user()->user_acl_role_id == User::USER_ROLE_LOCUM) {
            $mailController->recievedFeedbackEmployerNotification($job_feedback, $job, $freelancer, $employer);
            $dashboard = "/freelancer/dashboard";
        } else if (Auth::user()->user_acl_role_id == User::USER_ROLE_EMPLOYER) {
            $mailController->recievedFeedbackFreelancerNotification($job_feedback, $job, $freelancer);
            $dashboard = "/employer/dashboard";
        }

        return redirect($dashboard)->with("success", "Feedback added successfully");
    }

    public function expenseCostForm(Request $request)
    {
        try {
            $job_id = decrypt($request->query("job_id"));
            $freelancer_id = decrypt($request->query("freelancer_id"));
            $job_type = "live";
            if ($request->query('job_type')) {
                $job_type = decrypt($request->query("job_type"));
            }
        } catch (DecryptException $e) {
            return abort(404);
        }
        if (!in_array($job_type, ["live", "private"])) {
            return abort(404);
        }
        if ($freelancer_id != Auth::user()->id) {
            return abort(404);
        }
        $count = FinanceExpense::where("freelancer_id", $freelancer_id)->where("job_id", $job_id)->where("job_type", $job_type == "live" ? 1 : 2)->count();
        if ($request->isMethod("GET")) {
            $expense_types = ExpenseType::all();
            return view('freelancer.expense-cost-form', compact('expense_types', 'count'));
        } else if ($request->isMethod("POST")) {
            if ($count > 0) {
                return back()->with("error", "Finance transaction already added");
            }
            $freelancer = User::findOrFail($freelancer_id);
            $cats = $request->input("cat");
            $cost = $request->input("cost");
            if ($job_type == "private") {
                $job = FreelancerPrivateJob::findOrFail($job_id);
                $job_date = $job->job_date;
            } else {
                $job = JobPost::findOrFail($job_id);
                $job_date = $job->job_date;
            }
            $expense_array = [];
            foreach ($cats as $key => $cat) {
                $expense_array[] = [
                    "job_id" => $job->id,
                    "job_type" => $job_type == "private" ? 2 : 1,
                    "freelancer_id" => $freelancer->id,
                    "job_rate" => $cost[$key],
                    "job_date" => $job_date,
                    "expense_type_id" => $cat,
                    "description" => "",
                    "is_bank_transaction_completed" => 1,
                    "bank_transaction_date" => today(),
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
            }
            if (sizeof($expense_array) > 0) {
                FinanceExpense::insert($expense_array);
            }
        } else {
            return abort(404);
        }

        return redirect(route("freelancer.dashboard"))->with("success", "Expenses saved for the job");
    }

    public function privateJobCancel()
    {
        if (Auth::check() == false) {
            return redirect(route('login'))->with("error", "Please login with your account and visit the same link again");
        }

        return redirect(route("freelancer.private-job"))->with("success", "Please delete the job if you want to cancel it from here");
    }

    public function emailServiceHandle(Request $request)
    {
        $request->validate([
            "html" => "required|string",
            "from" => "required|email",
            "to" => "required|email",
            "subject" => "required|string"
        ]);
        if ($request->header("auth") != config("app.fudugo_app_key")) {
            return response()->json();
        }
        $html = $request->input("html");
        $from = $request->input("from");
        $to = $request->input("to");
        $subject = $request->input("subject");

        try {
            Mail::html($html, function (Message $message) use ($to, $subject, $from) {
                $message->from($from, config('app.name'))->to($to)->subject($subject);
            });
            Log::info("Email sent to {$from} from {$to} using email service endpoint");
        } catch (Exception $e) {
            Log::error("Email sent error to {$from} from {$to} using email service endpoint");
            Log::error($e->getMessage());
        }

        return response()->json();
    }

    public function locumform(Request $request)
    {
        if ($request->isMethod('GET')) {

            return view('locumform.locumform');
        } else {

            $request->validate([
                "contactname" => "required|string",
                "email" => "required|email",
                "intRef" => "required|string",
                "date" => "required|date",
                "rate" => "required|numeric",
                "store" => "required|string",
                "open" => "required|string",
                "close" => "required|string",
                "break" => "required|string",
                "testTime" => "required|string",
                "speReq" => "required|string",
            ]);

            $contactname = $request->input("contactname");
            $email = $request->input("email");
            $intRef = $request->input("intRef");
            $date = $request->input("date");
            $rate = $request->input("rate");
            $store = $request->input("store");
            $open = $request->input("open");
            $close = $request->input("close");
            $break = $request->input("break");
            $testTime = $request->input("testTime");
            $speReq = $request->input("speReq");

            $receiver = "bookings@locumkit.com";
            $subject = "Sightcare booking request";

            $message = "
                    <html>
                    <head>
                    <title>Locum Contact Details</title>
                    </head>
                    <body>
                    <table width='50%' border='0' align='center' cellpadding='0' cellspacing='0'>
                    <tr>
                        <td colspan='2' align='center' valign='top'><img src='http://demo.jmobiles.site/test/img/logo.png' width='150'></td>
                    </tr>
                    <tr>
                        <td colspan='2' align='center' valign='center' style='font-size: 30px'>$store</td>
                    </tr>
                    <tr>
                        <td width='50%' align='right' style='font-size: 20px'>&nbsp;</td>
                        <td align='left' style='font-size: 30px'>&nbsp;</td>
                    </tr>

                    <tr>
                        <td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Contact Name:</td>
                        <td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>" . $contactname . "</td>
                    </tr>
                    <tr>
                        <td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Email:</td>
                        <td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>" . $email . "</td>
                    </tr>
                    <tr>
                        <td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Internal Reference:</td>
                        <td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>" . $intRef . "</td>
                    </tr>
                    <tr>
                        <td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Date</td>
                        <td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>" . $date . "</td>
                    </tr>

                    <tr>
                        <td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Rate:</td>
                        <td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>£" . $rate . "</td>
                    </tr>
                    <tr>
                        <td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Store Name And Address:</td>
                        <td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>" . $store . "</td>
                    </tr>
                    <tr>
                        <td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Store Timing</td>
                        <td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>
                                Opening Time: <b>" . $open . "</b><hr>
                                Closing Time: <b>" . $close . "</b><hr>
                                Lunch Break: <b>" . $break . "</b>
                        </td>
                    </tr>
                    <tr>
                        <td align='right' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Testing Time:</td>
                        <td align='left' valign='top' style='border-top:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>" . $testTime . "</td>
                    </tr>
                    <tr>
                        <td align='right' valign='top' style='border-top:1px solid #dfdfdf; border-bottom:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 5px 7px 0;'>Special Request:</td>
                        <td align='left' valign='top' style='border-top:1px solid #dfdfdf; border-bottom:1px solid #dfdfdf; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000; padding:7px 0 7px 5px;'>" . $speReq . "</td>
                    </tr>
                    <tr>
                        <td colspan='2' align='center' valign='center' style='font-size: 10px'><center>E-Mail Powered By J-Solutions | All Rights Reserved " . date('Y') . " | <a href='http://usman.jmobiles.pk'>http://usman.jmobiles.pk</a> | +92 334 5266444</center></td>
                    </tr>
                    </table>

                    </body>
                    </html>
                ";

            // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            // More headers
            $headers .= 'From: <no-reply@locumkit.com>';

            try {
                Mail::html($message, function (Message $message) use ($receiver, $subject) {
                    $message->to($receiver)->subject($subject);
                });
                return redirect(route('locumform.thanks'))->with('script', '<script>swal("Request Sent!","Thank You for submitting your request! We will contact you soon.","success");</script>');
            } catch (Exception $ignore) {
                return back()->with('error', "The message could not been sent!");
            }
        }
    }
    
    public function questionFreelancer()
    {
        return view('questionFreelancer');
    }
    public function questionEmployer()
    {
        return view('questionEmployer');
    }
    
   public function newsLetter(Request $request)
    {
        try {
           $request->validate([
                'email' => 'required|email|unique:subscribe_users,email'
            ], [
                'email.required' => 'Enter the email address.',
                'email.email' => 'Please enter a valid email address.', // Custom message for invalid format
                'email.unique' => 'You are already subscribed with this email address.',
            ]);
    
            // Save the subscription
            $user = new SubscribeUser();
            $user->email = $request->email;
            $user->save();
    
            return redirect()->back()->with('success', 'Successfully Subscribed To NewsLetter');
        } catch (\Illuminate\Validation\ValidationException $e) {
            
            return redirect()->back()->with('error', $e->validator->errors()->first());
        }
    }

}
