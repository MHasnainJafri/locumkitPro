<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class MobileWebServiceController extends Controller
{
    private Request $client_request;
    private Response|ResponseFactory|null $server_response;

    const SERVER_RESPONSE_HEADERS = [
        "Content-Type" => "application/json",
        "Access-Control-Allow-Origin" => "*",
        "Access-Control-Allow-Methods" => "GET, HEAD, POST, PUT, DELETE, CONNECT, OPTIONS, TRACE, PATCH",
        "Access-Control-Allow-Headers" => "Accept, Accept-Language, Content-Language, Content-Type, DPR, Downlink, Save-Data, Viewport-Width, Width"
    ];

    public function index(Request $request)
    {

        $this->client_request = $request;
        $app_page_type = $request->input("page");
        $this->client_request = $request;
        $this->server_response = null;
        if (in_array($app_page_type, config("approutes"))) {
            $this->server_response = match ($app_page_type) {
                "login" => $this->callSpecifiedMethod(LoginController::class, "login"), //CHECKED
                "register-form" => $this->callSpecifiedMethod(RegisterController::class, "registerForm"), //CHECKED
                "logout" => $this->callSpecifiedMethod(LoginController::class, "logout"), //CHECKED
                "update-session" => $this->callSpecifiedMethod(LoginController::class, "updateSession"), //CHECKED
                "is-profile-completed" => $this->callSpecifiedMethod(LoginController::class, "isProfileCompleted"), //CHECKED
                "block-date" => $this->callSpecifiedMethod(UserController::class, "blockDate"), //CHECKED
                "check-user-availability" => $this->callSpecifiedMethod(UserController::class, "checkUserAvailability"), //CHECKED
                "finance-summary" => $this->callSpecifiedMethod(UserController::class, "financeSummary"), //CHECKED
                "finance-summary-chart" => $this->callSpecifiedMethod(UserController::class, "financeSummaryChart"), //CHECKED
                "manage-incomes" => $this->callSpecifiedMethod(TransactionsController::class, "manageTransactions"), //CHECKED
                "user-cancellation-rate" => $this->callSpecifiedMethod(UserController::class, "userCancellationRate"), //CHECKED
                "user-permission" => $this->callSpecifiedMethod(UserController::class, "userPermission"), //CHECKED
                "update-paasword" => $this->callSpecifiedMethod(UserController::class, "updatePasword"), //CHECKED
                "user-feedback-action" => $this->callSpecifiedMethod(FeedbackController::class, "userFeedbackAction"),
                "get-feedback-by-id" => $this->callSpecifiedMethod(FeedbackController::class, "getFeedbackById"),
                "current-month-booking" => $this->callSpecifiedMethod(JobController::class, "currentMonthBooking"), //CHECKED
                "finance" => $this->callSpecifiedMethod(FinanceController::class, "finance"), //CHECKED
                "manage-calendar" => $this->callSpecifiedMethod(UserController::class, "manageCalendar"), //CHECKED
                "get-min-rate-date" => $this->callSpecifiedMethod(UserController::class, "getMinRateDate"), //CHECKED
                "feedback-summary" => $this->callSpecifiedMethod(FeedbackController::class, "getFeedbackSummary"), //CHECKED
                "edit-profile" => $this->callSpecifiedMethod(ProfileController::class, "editProfile"), //CHECKED
                "update-profile" => $this->callSpecifiedMethod(ProfileController::class, "updateProfile"), //CHECKED
                "edit-questions" => $this->callSpecifiedMethod(ProfileController::class, "editQuestions"), //CHECKED
                "update-questions" => $this->callSpecifiedMethod(ProfileController::class, "updateQuestions"), //CHECKED
                "manage-package" => $this->callSpecifiedMethod(PackageController::class, "managePackage"),
                "store-list" => $this->callSpecifiedMethod(UserController::class, "storeList"), //CHECKED
                "manage-blocked-user" => $this->callSpecifiedMethod(UserController::class, "manageBlockedUser"),
                "search-town" => $this->callSpecifiedMethod(UserController::class, "searchTown"), //CHECKED
                "multi-store" => $this->callSpecifiedMethod(EmployerStoreController::class, "multiStore"), //CHECKED
                "manage-stores" => $this->callSpecifiedMethod(EmployerStoreController::class, "manageStores"), //CHECKED
                "post-job" => $this->callSpecifiedMethod(JobController::class, "postJob"), //CHECKED
                "search-freelancer" => $this->callSpecifiedMethod(JobController::class, "searchFreelancer"), //CHECKED
                "send-job-invitation" => $this->callSpecifiedMethod(JobController::class, "sendJobInvitation"), //CHECKED
                "add-private-freelancer" => $this->callSpecifiedMethod(PrivateFreelancerController::class, "addPrivateFreelancer"), //CHECKED
                "delete-private-freelancer" => $this->callSpecifiedMethod(PrivateFreelancerController::class, "deletePrivateFreelancer"), //CHECKED
                "job-list" => $this->callSpecifiedMethod(JobController::class, "jobList"), //CHECKED
                "job-view" => $this->callSpecifiedMethod(JobController::class, "jobView"), //CHECKED
                "job-action" => $this->callSpecifiedMethod(JobController::class, "jobAction"),  //CHECKED Partialy
                "private-job" => $this->callSpecifiedMethod(PrivateJobController::class, "manage_private_job"), //CHECKED
                "private-job-view" => $this->callSpecifiedMethod(PrivateJobController::class, "view_private_job"), //CHECKED
                "private-job-attend" => $this->callSpecifiedMethod(PrivateJobController::class, "attend_private_job"),
                "user-job-action" => $this->callSpecifiedMethod(JobController::class, "jobActionHandler"),
                default => null
            };

            if ($this->server_response) {
                $this->saveRequestResponse();
                return $this->server_response->withHeaders($this::SERVER_RESPONSE_HEADERS);
            }
        }
        return response("Not found", 404, $this::SERVER_RESPONSE_HEADERS);
    }

    public function callSpecifiedMethod($controller, $method)
    {
        $classObject = App::make($controller);
        return call_user_func(array($classObject, $method), $this->client_request);
    }

    private function saveRequestResponse()
    {
        if ($this->client_request) {
            $file_name = "request-" . now()->timestamp . "-" . $this->client_request->input("page") . ".json";
            $response_json = $this->server_response && $this->server_response->content() && json_decode($this->server_response->content()) ? json_decode($this->server_response->content(), true) : $this->server_response->content();
            $json_content = [
                "request" => $this->client_request->all(),
                "response" => $response_json
            ];
            Storage::disk("api_logs")->put($file_name, json_encode($json_content, JSON_PRETTY_PRINT));
        }
    }
}
