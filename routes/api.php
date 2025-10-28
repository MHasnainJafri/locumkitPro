<?php

use App\Http\Controllers\Api\EmployerStoreController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\MobileWebServiceController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\PrivateFreelancerController;
use App\Http\Controllers\Api\PrivateJobController;
use App\Http\Controllers\Api\ChangePasswordController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\TransactionsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/ 

Route::post("login", [LoginController::class, "login"]);
Route::post("register-form", [RegisterController::class, "registerForm"]);
Route::post("store-list", [UserController::class, "storeList"]);
Route::post("search-town", [UserController::class, "searchTown"]);
Route::post("password-reset", [ChangePasswordController::class, "changePassword"]);
Route::post("forgot-password", [ChangePasswordController::class, "forgetPassword"]);
Route::post('/verify-email-otp', [ChangePasswordController::class, 'verifyEmail']);
Route::post('/resend-email-otp', [ChangePasswordController::class, 'resendEmailOtp']);
Route::post('reset-password', [ChangePasswordController::class, 'resetPassword']);
Route::get('/verify-app-side-email', [UserController::class,'verify'])->name('email-verify-appside');


Route::group(["middleware" => ["auth:sanctum"]], function () {
    /* //CHECKED: These are documented in postman. Send nice JSON response with 'success' & true, false, message and data */
    Route::post("logout", [LoginController::class, "logout"]);
    Route::post("is-profile-completed", [LoginController::class, "isProfileCompleted"]);
    Route::post("block-date", [UserController::class, "blockDate"]);
    Route::post("check-user-availability", [UserController::class, "checkUserAvailability"]);
    Route::post("finance-summary", [UserController::class, "financeSummary"]);
    Route::post("finance-summary-chart", [UserController::class, "financeSummaryChart"]);
    Route::post("update-password", [UserController::class, "updatePassword"]);
    Route::post("user-cancellation-rate", [UserController::class, "userCancellationRate"]);
    Route::post("user-permission", [UserController::class, "userPermission"]);
    Route::post("get-feedback-by-id", [FeedbackController::class, "getFeedbackById"]);
    Route::post("current-month-booking", [JobController::class, "currentMonthBooking"]);
    Route::post("manage-calendar", [UserController::class, "manageCalendar"]);
    Route::post("get-min-rate-date", [UserController::class, "getMinRateDate"]);
    Route::post("edit-profile", [ProfileController::class, "editProfile"]);
    Route::post("update-profile", [ProfileController::class, "updateProfile"]);
    Route::post("edit-questions", [ProfileController::class, "editQuestions"]);
    Route::post("update-questions", [ProfileController::class, "updateQuestions"]);
    Route::post("add-private-freelancer", [PrivateFreelancerController::class, "addPrivateFreelancer"]);
    Route::post("delete-private-freelancer", [PrivateFreelancerController::class, "deletePrivateFreelancer"]);
    Route::post("finance", [FinanceController::class, "finance"]);
    Route::post("user-feedback-action", [FeedbackController::class, "userFeedbackAction"]);
    Route::post("feedback-summary", [FeedbackController::class, "getFeedbackSummary"]);
    
    Route::post("employer-add-Transactions", [EmployerStoreController::class, "saveTransaction"]);
    Route::post("employer-update-transactions", [EmployerStoreController::class, "editTransaction"]);
    Route::get("employer-delete-transaction/{id}", [EmployerStoreController::class , "DeleteTranactions"]);
    
    Route::get("get-transaction-chart-data", [EmployerStoreController::class, "getTransactionChartData"]);

    Route::post("manage-package", [PackageController::class, "managePackage"]);
    Route::group(['prefix' => 'suppliers'], function () {
        Route::post("list", [TransactionsController::class, "allSupplier"]);
        Route::post("save", [TransactionsController::class, "insertSupplier"]);
        Route::post("update", [TransactionsController::class, "updateSupplier"]);
        Route::post("delete", [TransactionsController::class, "deleteSupplier"]);
        Route::post("get", [TransactionsController::class, "getSupplierById"]);
    });
    Route::post("post-job", [JobController::class, "postJob"]); 

    /* //CHECK: Document in postman but response are random and not include(may be include) success, error, message etc. */
    Route::post("manage-incomes", [TransactionsController::class, "manageTransactions"]);
    Route::post("multi-store", [EmployerStoreController::class, "multiStore"]);
    Route::post("manage-stores", [EmployerStoreController::class, "manageStores"]);
    Route::post("manage-blocked-user", [UserController::class, "manageBlockedUser"]);
    Route::post("search-freelancer", [JobController::class, "searchFreelancer"]);
    Route::post("send-job-invitation", [JobController::class, "sendJobInvitation"]);
    Route::post("job-list", [JobController::class, "jobList"]);
    Route::post("job-view", [JobController::class, "jobView"]);
    Route::post("job-action", [JobController::class, "jobAction"]);
    Route::post("private-job", [PrivateJobController::class, "manage_private_job"]);
    Route::post("private-job-view", [PrivateJobController::class, "view_private_job"]);
    Route::post("private-job-attend", [PrivateJobController::class, "attend_private_job"]);
    Route::post("user-job-action", [JobController::class, "jobActionHandler"]);
});

Route::post("/fcm/test", [UserController::class, "fcmTest"]);

Route::post("/email/service/send", [HomeController::class, "emailServiceHandle"]);

Route::fallback(function () {
    return response()->error('Not found');
});
