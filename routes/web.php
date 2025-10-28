<?php

use App\Helpers\DistanceCalculateHelper;
use App\Http\Controllers\admin\paymentController;
use App\Http\Controllers\admin\pkgresourceController;
use App\Http\Controllers\admin\questionController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\admin\PackageController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\Api\MobileWebServiceController;
use App\Http\Controllers\EmailGroupingController;
use App\Http\Controllers\Employer\BlockUserController;
use App\Http\Controllers\Employer\JobsController as EmployerJobsController;
use App\Http\Controllers\Employer\PrivateUsersController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\Freelancer\LocumlogbookFollowupProcedureController;
use App\Http\Controllers\Freelancer\LocumlogbookPracticeInfoController;
use App\Http\Controllers\Freelancer\LocumlogbookReferralPathwayController;
use App\Http\Controllers\FreelancerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobManagementController;
use App\Http\Controllers\PackageMembershipController;
use App\Models\FinanceIncome;
use App\Models\SendNotification;
use App\Models\SiteTown;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Mail\TestMail;

Route::get('/send-email', function () {

    // Hardcoded data
    $email = 'ehsankhan0577@gmail.com';
    $username = 'Ihsaan';

    // Send email directly from route
    Mail::to($email)->send(new TestMail($username));

    return 'Email sent successfully!';
});

use App\Mail\JobNegotiateMail;
use App\Models\JobPost;
use App\Models\User;

Route::get('/test-job-negotiate-mail', function () {
    try {
        $job = new JobPost();
        $job->id = 1;
        $job->title = "Sample Job Post";
        $job->description = "This is a test job post.";

        $freelancer = new User();
        $freelancer->id = 2;
        $freelancer->name = "John Doe";
        $freelancer->email = "freelancer@example.com";
        $freelancer->mobile = "987-654-3210";
        $freelancer->user_answers = [/* ... */];

        $employer = new User();
        $employer->id = 1;
        $employer->name = "Jane Smith";
        $employer->email = "ehsankhan0577@gmail.com";
        $employer->mobile = "123-456-7890";

        $job_expected_rate = 50.00;
        $freelancer_message = "Hi, I’d like to negotiate the rate for this job.";

        $mail = new JobNegotiateMail($job, $freelancer, $employer, $job_expected_rate, $freelancer_message);

        Mail::to($employer->email)->send($mail);

        return "Test email sent successfully!";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});


Route::get('login', function() {
    return redirect()->route('index');
});

Route::post('/save-news-letter', [HomeController::class, 'newsLetter'])->name('subscribed-news-letter');

Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/how-to-answer-question-fre', [HomeController::class, 'questionFreelancer'])->name('how-to-answer-question-fre');
Route::get('/how-to-answer-question-emp', [HomeController::class, 'questionEmployer'])->name('how-to-answer-question-emp');
Route::get('/edit/{name}', [HomeController::class, 'editpages'])->name('pages.edit');
Route::get('/thank-you', [HomeController::class, 'thankYou'])->name('thank-you');
Route::get('/locums', [HomeController::class, 'locums'])->name('locums');
Route::get('/employer', [HomeController::class, 'employer'])->name('employer');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/benefits', [HomeController::class, 'benefits'])->name('benefits');
Route::get('/dbs', [HomeController::class, 'dbs'])->name('dbs');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contact'])->name('post.contact');
Route::get('/accountancy', [HomeController::class, 'accountancy'])->name('accountancy');
Route::get('/term-condition', [HomeController::class, 'termAndCondition'])->name('term-condition');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/sitemap', [HomeController::class, 'showSitemap'])->name('sitemap');
Route::get('/maps', [HomeController::class, 'showMaps'])->name('maps');
Route::get('/package', [HomeController::class, 'showPackage'])->name('package');
Route::get('/blogs', [HomeController::class, 'blogs'])->name('blogs');
Route::get('/blogs/recent-posts', [HomeController::class, 'blogsRecentPosts'])->name('blogs-recent-posts');
Route::get('/blog/{slug}', [HomeController::class, 'showBlogPost'])->name('show-blog-post');
Route::get('/news/{slug}', [HomeController::class, 'showNewsPost'])->name('show-news-post');
Route::get("freelancer/single-job/{id}", [JobManagementController::class, 'viewJobFreelancer'])->name('single-job')->middleware('auth');
Auth::routes(['verify' => true]);


// Route::group(["middleware" => ["auth", "verified", "can:is_freelancer"], "prefix" => "freelancer", "as" => "freelancer."], function () {
Route::group(["middleware" => ["auth", "verified", "check.freelancer"], "prefix" => "freelancer", "as" => "freelancer."], function () {
    Route::get("/", [FreelancerController::class, 'index'])->name('dashboard');
    Route::get("/dashboard", [FreelancerController::class, 'index'])->name('dashboard');
    Route::get('/update-status-yes/{id}',[FreelancerController::class,'update_notification_yes'])->name('update-status-yes');
    Route::get('/update-status-no/{id}',[FreelancerController::class,'update_notification_no'])->name('update-status-no');
    Route::get('/final-update-status/{id}',[FreelancerController::class,'Final_update_notification'])->name('final-update-status');
    Route::post('/add/feedback', [FreelancerController::class, 'AddFeedBack'])->name('add.feedback');
    Route::get("/edit-profile", [FreelancerController::class, 'editProfile'])->name('edit-profile');
    Route::post("/update-profile", [FreelancerController::class, 'updateProfile'])->name('update-profile');
    Route::delete("/profile/delete", [FreelancerController::class, 'deleteProfile'])->name('delete-profile');
    Route::get("/edit-questions", [FreelancerController::class, 'editQuestions'])->name('edit-questions');
    Route::put("/edit-questions", [FreelancerController::class, 'updateQuestions'])->name('update-questions');
    Route::get("/job-listing", [FreelancerController::class, 'jobListing'])->name('job-listing');
    
    // Route::get("/single-job/{id}", function(){
    //     return true;
    // })->name('single-job'); 
    Route::get("/cancel-job/{id}", [JobManagementController::class, 'cancelJobFreelancer'])->name('cancel-job');
    Route::post("/cancel-job/{id}", [JobManagementController::class, 'cancelJobPostFreelancer'])->name('cancel-job-post');
    Route::get("/private-job", [FreelancerController::class, 'privateJob'])->name('private-job');
    Route::post("/private-job", [FreelancerController::class, 'storePrivateJobs'])->name('store-private-job');
    Route::put("/private-job", [FreelancerController::class, 'updatePrivateJobs'])->name('update-private-job');
    Route::get("/help/job-booking-freelancer", [FreelancerController::class, 'showHelpJobBooking'])->name('help.job-booking-freelancer');
    Route::get("/help/finance-model-freelancer", [FreelancerController::class, 'showHelpFinanceModel'])->name('help.finance-model-freelancer');
    Route::post("/update-employment-status", [FreelancerController::class, 'updateEmploymentStatus'])->name('update-employment-status');

    Route::group(["middleware" => ["can:manage_finance"]], function () {
        Route::get("/finance", [FreelancerController::class, "financeDetail"])->name("finance");
        Route::get("/add-income", [FreelancerController::class, "showAddIncome"])->name("add-income");
        Route::post("/add-income", [FreelancerController::class, "saveIncome"])->name("add-income-save");
        Route::get("/add-expense", [FreelancerController::class, "showAddExpense"])->name("add-expense");
        Route::post("/add-expense", [FreelancerController::class, "saveExpense"])->name("add-expense-save");
        Route::get("/manage-supplier", [FreelancerController::class, "showSupplierList"])->name("manage-supplier");
        Route::get("/add-supplier", [FreelancerController::class, "showAddSupplier"])->name("add-supplier");
        Route::post("/save-supplier", [FreelancerController::class, "saveSupplier"])->name("save-supplier");
        Route::get("/edit-supplier/{id}", [FreelancerController::class, "showEditSupplier"])->name("edit-supplier");
        Route::put("/update-supplier/{id}", [FreelancerController::class, "updateSupplier"])->name("update-supplier");
        Route::get("/income-by-supplier", [FreelancerController::class, "incomeBySupplier"])->name("income-by-supplier");
        Route::get("/bank-details", [FreelancerController::class, "bankDetails"])->name("bank-details");
        Route::post("/save-bank-details", [FreelancerController::class, "saveBankDetails"])->name("save-bank-details");
        Route::get("/open-invoices", [FreelancerController::class, "showOpenInvoices"])->name("open-invoices");
        Route::post("/update-invoice", [FreelancerController::class, "updateInvoice"])->name("update-invoices");
        Route::get("/send-invoice/{id}", [FreelancerController::class, "sendInvoice"])->name("send-invoices");
        Route::post("/save-send-invoice", [FreelancerController::class, "sendAndSaveInvoice"])->name("save-send-invoice");
        Route::get("/reports", [FreelancerController::class, 'showReports'])->name('reports');
        Route::get("/cash-movement-report", [FreelancerController::class, 'showCashMovementReport'])->name('cash-movement-report');
        Route::get("/weekly-report", [FreelancerController::class, 'showWeeklyReport'])->name('weekly-report');
        Route::get("/all-transaction", [FreelancerController::class, 'showAllTransactions'])->name('all-transaction');
        Route::post("/all-transactions/export", [FreelancerController::class, 'exportAllTransactions'])->name('export-all-transaction');
        //update bank transactions
        Route::put("/income/update-bank-detail", [FreelancerController::class, 'updateBankTransaction'])->name('update-bank-detail');
        Route::put("/expense/update-bank-detail", [FreelancerController::class, 'updateBankTransactionExpense'])->name('update-bank-detail-expense');
        //expense, income edit
        Route::get("/edit-income/{id}", [FreelancerController::class, "showEditIncome"])->name("edit-income");
        Route::put("/edit-income/{id}", [FreelancerController::class, "updateIncome"])->name("edit-income-update");
        Route::get("/edit-expense/{id}", [FreelancerController::class, "showEditExpense"])->name("edit-expense");
        Route::put("/edit-expense/{id}", [FreelancerController::class, "updateExpense"])->name("edit-expense-update");
        //reports
        Route::get("/income-by-area", [FreelancerController::class, "incomeByArea"])->name("income-by-area");
        Route::get("/income-filter", [FreelancerController::class, "incomeByCategory"])->name("income-filter");
        Route::get("/expenses-type-filter", [FreelancerController::class, "expensesTypeFilter"])->name("expenses-type-filter");
        Route::get("/net-income", [FreelancerController::class, "netIncome"])->name("net-income");
        //expense, income delete
        Route::delete("/delete-income/{id}", [FreelancerController::class, "deleteIncome"])->name("finance-income.delete");
        Route::delete("/delete-expense/{id}", [FreelancerController::class, "deleteExpense"])->name("finance-expense.delete");
    });
    Route::group(["middleware" => ["can:manage_feedback"]], function () {
        Route::get("/feedback-detail", [FreelancerController::class, "feedbackDetails"])->name("feedback-detail");
    });

    Route::group(['as' => 'locumlogbook.'], function () {
        Route::resource('locumlogbook/follow-up-procedures', LocumlogbookFollowupProcedureController::class);
        Route::resource('locumlogbook/referral-pathways', LocumlogbookReferralPathwayController::class);
        Route::resource('locumlogbook/practice-info', LocumlogbookPracticeInfoController::class);
    });
});

Route::group(["prefix" => "ajax", "as" => "ajax."], function () {
    Route::post('/registration-info-check', [AjaxController::class, 'registrationInfoCheck'])->name('registration-info-check');
    Route::post('/question-by-role', [AjaxController::class, 'questionByRole'])->name('question-by-role');
    Route::post('/validate-save-questions', [AjaxController::class, 'saveQuestionByRole'])->name('save-question-by-role');
    Route::post('/mutli-store-time', [AjaxController::class, 'multiStoreTime'])->name('multi-store-time');
    Route::post('/open-benefits-form', [AjaxController::class, 'openBenefitsForm'])->name('open-benefits-form');
    Route::post('/get-town-list', [AjaxController::class, 'getTownList'])->name('get-town-list');
    Route::post('/save-town-list', [AjaxController::class, 'prepareTownListToSave'])->name('save-town-list');
    Route::post('/get-job-info', [AjaxController::class, 'getJobInfo'])->name('get-job-info');
    Route::post('/get-invoice-template', [AjaxController::class, 'getInvoiceTemplate'])->name('get-invoice-template');
    Route::post('/get-info-about-date', [AjaxController::class, 'getDateInfo'])->name('get-info-about-date');
    Route::post('/get-booked-date-info', [AjaxController::class, 'getBookedDateInfo'])->name('get-booked-date-info');
    Route::post('/update-calender', [AjaxController::class, 'updateUserCalender'])->name('update-calender');
    Route::delete('/private-job/{id}/delete', [AjaxController::class, 'deletePrivateJob'])->name('delete-private-job');
    Route::delete('/manage-block-freelancer', [AjaxController::class, 'unblockBlockedFreelancer'])->name('manage-block-freelancer');

    Route::group(["middleware" => ["auth", "verified", "can:is_employer", "is_employer_active"], "prefix" => "employer", "as" => "employer."], function () {
        Route::delete('delete-job-listing/{job_id}', [EmployerJobsController::class, "deleteJobListing"])->name("delete-job-listing");
        Route::delete('manage-store/{store_id}', [EmployerController::class, "deleteStore"])->name("delete-store");
        Route::put('update-financial-year', [EmployerController::class, "updateFinancialYear"])->name("update-financial-year");
        Route::post('/view-applicant-information/{id}', [AjaxController::class, 'getApplicantInformation'])->name('view-applicant-information');
    });
});

Route::group(["middleware" => ["auth", "verified", "can:is_employer", "is_employer_active"], "prefix" => "employer", "as" => "employer."], function () {
    Route::get("/dashboard", [EmployerController::class, "index"])->name("dashboard");
    Route::get("/edit-profile", [EmployerController::class, 'editProfile'])->name('edit-profile');
    Route::post("/update-profile", [EmployerController::class, 'updateProfile'])->name('update-profile');
    Route::delete("/profile/delete", [EmployerController::class, 'deleteProfile'])->name('delete-profile');
    Route::get("/edit-questions", [EmployerController::class, 'editQuestions'])->name('edit-questions');
    Route::put("/edit-questions", [EmployerController::class, 'updateQuestions'])->name('update-questions');
    Route::get("/help/job-booking-employer", [EmployerController::class, 'showHelpJobBooking'])->name('help.job-booking-employer');
    Route::get("/help/finance-model-employer", [EmployerController::class, 'showHelpFinanceModel'])->name('help.finance-model-employer');
    
    Route::get("/single-job/", [JobManagementController::class, 'viewJobEmployer'])->name('single-job');
 
    Route::get("/job-listing", [EmployerJobsController::class, 'jobListing'])->name('job-listing');
    Route::get("/managejob/{job_id?}", [EmployerJobsController::class, 'manageJob'])->name('managejob');
    Route::post("/managejob", [EmployerJobsController::class, 'saveManageJob'])->name('save-managejob');
    Route::put("/managejob/{job_id}", [EmployerJobsController::class, 'updateManageJob'])->name('update-managejob');
    Route::get("/cancel-job/{id}", [JobManagementController::class, 'cancelJobEmployer'])->name('cancel-job');
    Route::post("/cancel-job/{id}", [JobManagementController::class, 'cancelJobPostEmployer'])->name('cancel-job-post');

    Route::get("/manage-block-freelancer", [BlockUserController::class, "index"])->name("manage-block-freelancer");

    Route::get("/job-search/{id}", [EmployerJobsController::class, 'jobSearch'])->name('job-search');
    Route::post("/invite-for-job/{id}", [JobManagementController::class, 'sendJobInvitation'])->name('invite-for-job');
    Route::get("/view-job/{id}", [EmployerJobsController::class, 'viewJob'])->name('view-job');

    Route::get("/manage-store", [EmployerController::class, 'showManageStore'])->name('manage-store.index');
    Route::put("/manage-store", [EmployerController::class, 'updateStoreList'])->name('manage-store.update');
    Route::post("/manage-store", [EmployerController::class, 'saveNewStore'])->name('manage-store.store');

    Route::group(["prefix" => "finance", "as" => "finance.", "middleware" => ["can:manage_finance"]], function () {
        Route::get("/", [EmployerController::class, 'financeHome'])->name('index');
        Route::put("/update-transaction-bank-date", [EmployerController::class, 'updateBankTransactionDate'])->name("update-transaction-bank-date");
        Route::delete("/delete-finance-transaction/{id}", [EmployerController::class, 'deleteFinanceTransaction'])->name("delete-finance-transaction");
        Route::get("/manage-finance-transaction/{id?}", [EmployerController::class, 'manageFinanceTransaction'])->name("manage-finance-transaction.index");
        Route::post("/manage-finance-transaction", [EmployerController::class, 'saveFinanceTransaction'])->name("manage-finance-transaction.store");
        Route::put("/manage-finance-transaction/{id}", [EmployerController::class, 'updateFinanceTransaction'])->name("manage-finance-transaction.update");
    });

    Route::group(["middleware" => ["can:manage_feedback"]], function () {
        Route::get("/feedback-detail", [EmployerController::class, "feedbackDetails"])->name("feedback-detail");
    });

    Route::post("/store-private-users", [PrivateUsersController::class, "storePrivateUsers"])->name("private-users.store");
    Route::delete("/delete-private-user/{id}", [PrivateUsersController::class, "deletePrivateUsers"])->name("private-users.delete");
});

//shared auth routes for employer, freelancer
Route::group(["middleware" => ["auth", "verified"]], function () {
    Route::get("/{for_user_role}/feedback-report/{user_id}", [HomeController::class, "showFeedbackReport"])->middleware("can:manage_feedback")->name("user.feedback-report");
    Route::get("/attendance", [HomeController::class, "attendance"])->name("user.attendance");
    Route::get("/feedback", [HomeController::class, "feedback"])->name("user.feedback");
    Route::post("/post-feedback", [HomeController::class, "postFeedback"])->name("user.post-feedback");

    //negotiate
    Route::group(["prefix" => "negotiate", "as" => "negotiate."], function () {
        Route::post("/freelancer-negotiate-on-job/{job_id}", [JobManagementController::class, 'negotiateOnJobPost'])->name('freelancer-negotiate-on-job-post')->middleware("can:is_freelancer");
        Route::get("/freelancer-negotiate-on-job", [JobManagementController::class, 'negotiateOnJob'])->name('freelancer-negotiate-on-job');
        Route::get("/employer-accept-negotiation", [JobManagementController::class, 'acceptJobNegotiate'])->name('employer-accept-negotiation')->middleware(["can:is_employer", "is_employer_active"]);
    });
    
    
    
    
});
    //mixed routes for job
    Route::get("/accept-job", [JobManagementController::class, 'acceptJob'])
    ->middleware('auth')
    ->name('accept-job');

    Route::get("/freeze-job", [JobManagementController::class, 'freezeJob'])->name('freeze-job');
    Route::get("/cancel-job", [JobManagementController::class, 'cancelJob'])->name('cancel-job');
    Route::get("/block-user", [BlockUserController::class, 'blockUser'])->name('block-user');
    Route::post("/employer/block-user/{id}", [BlockUserController::class, 'blockUserPost'])->name('block-user.post');
    Route::any("/expense-cost-form", [HomeController::class, 'expenseCostForm'])->middleware(["auth", "verified", "can:is_freelancer"])->name('expense-cost-form');
    Route::any("/feedback-dispute", [FeedbackController::class, 'feedbackDispute'])->middleware(["auth", "verified"])->name('feedback-dispute');
    Route::any("/upgrade-package", [PackageMembershipController::class, "index"]);
    Route::get("/private-job-cancel", [HomeController::class, "privateJobCancel"]);
     


//Locumform route
Route::prefix('locumform')->name('locumform.')->group(function () {
    Route::get('/', [HomeController::class, "locumform"]);
    Route::post('/', [HomeController::class, "locumform"]);
    Route::view('/thanks', "locumform.thanks")->name('thanks');
});

Route::prefix('email-grouping')->name('email-grouping.')->group(function () {
    Route::get('/login', [EmailGroupingController::class, "login"])->name('login');
    Route::post('/login', [EmailGroupingController::class, "login"]);

    Route::middleware('mail_group_auth')->group(function () {
        Route::get('/logout', [EmailGroupingController::class, "logout"])->name('logout');
        Route::get('/', [EmailGroupingController::class, "home"])->name('index');
        //users crud
        Route::middleware('mail_group_admin_auth')->prefix('users')->name('users.')->group(function () {
            Route::get('/', [EmailGroupingController::class, "users"])->name('index');
            Route::post('/save-user', [EmailGroupingController::class, "saveUser"])->name('save');
            Route::put('/update-user', [EmailGroupingController::class, "updateUser"])->name('update');
            Route::delete('/delete-user/{id}', [EmailGroupingController::class, "deleteUser"])->name('delete');
        });
        Route::middleware('mail_group_admin_auth')->prefix('mailists')->name('mailists.')->group(function () {
            Route::get('/', [EmailGroupingController::class, "mailists"])->name('index');
            Route::post('/save-mailist', [EmailGroupingController::class, "saveMailist"])->name('save');
            Route::put('/update-mailist', [EmailGroupingController::class, "updateMailist"])->name('update');
            Route::put('/update-mailist/mails', [EmailGroupingController::class, "updateMailistMails"])->name('update.mails');
            Route::delete('/delete-mailist/{id}', [EmailGroupingController::class, "deleteMailist"])->name('delete');
        });
        Route::get('/mailing', [EmailGroupingController::class, "mailing"])->name('mailing');
        Route::post('/mailing/send', [EmailGroupingController::class, "mailSend"])->name('mailing.send');
    });
});
// Route::post('/category/create',[CategoryController::class,'categoryCreate'])->name('category.create');
// Route::post('/category/update/{id}',[CategoryController::class,'categoryUpdate'])->name('categories.update');
// Route::post('/admin/pkgresource/destroy/{id}',[pkgresourceController::class,'packageDestroy'])->name('admin.pkgresource.destroy');

// Route::get('/viewQuestionindex',[questionController::class,'index'])->name('viewQuestionindex');
// Route::get('/viewQuestioncreate',[questionController::class,'create'])->name('viewQuestioncreate');

// Route::controller(PackageController::class)->group(function () {
//     Route::get('/categories.edit/{id}', 'edit')->name('categories.edit');
//     Route::post('/categories.update/{id}', 'update')->name('categories.update'); 
// });
// Route::controller(PackageController::class)->group(function () {
//     Route::get('/package.edit/{id}', 'edit')->name('package.edit');
//     Route::post('/package.update/{id}', 'update')->name('package.update');
//     Route::post('/package.destroy/{id}', 'destroy')->name('package.delete');
// });


//Mobile app route
//Route::any("/fudugo-app-api", [MobileWebServiceController::class, "index"])->middleware('auth.approutes');

//Email service route


// Route::get('/payment.History',[paymentController::class,'index'])->name('payment.History');
Route::view('test', 'admin.users.index');
Route::get('/testinglogs', function(){
    Log::channel('queue-worker')->info('same same is working');
    Log::channel('queue-worker')->info('calling from web');
    return true;
});
Route::get('email_is_veridifed', function(){
    return view('auth.verified_message');
})->name('verify_messges');






require __DIR__ . '/admin.php';
