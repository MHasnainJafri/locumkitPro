<?php

namespace App\Http\Controllers\admin;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BlockUsersExport;
use App\Exports\LeaverUsersExport;
use App\Exports\LastLoginUsersExport;
use App\Exports\LocumPrivateJobs;
use App\Exports\NewUserExport;
use App\Exports\EmployerJobReportExport;
use App\Exports\getLocumReportExport;
use Illuminate\Support\Facades\Hash;
use App\Exports\LocumPrivateJobReport;
use App\Notifications\UserAccountActiveNotification;
use App\Exports\LocumPrivateJobsExport;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserExtraInfo;
use App\Models\UserPackageDetail;
use App\Models\UserPaymentInfo;
use App\Models\Leavers;
use App\Models\EmployerStoreList;
use App\Models\FinancialYear;
use Illuminate\Http\Request;
use App\Models\UserAclPackage;
use App\Models\UserAclProfession;
use App\Http\Controllers\Controller;
use App\Models\BlockUser;
use App\Models\FreelancerPrivateJob;
use App\Models\JobAction;
use App\Models\JobPost;
use App\Models\LastLoginUser;
use App\Models\UserAclRole;
use App\Notifications\AccountActiveNotification;
use App\Notifications\PasswordChangeNotification;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Invoice;
use App\Models\FinanceIncome;

class UserController extends Controller
{

    public $role, $profession, $professionslist;

    public function __construct(Request $request)
    {
        $this->role = $request->input('q', 'Locum');
        $this->profession = $request->input('c', null);
        $this->professionslist = UserAclProfession::where('is_active', 0)->get();
    }

    public function index(Request $request)
    {
        $usersQuery = User::query();
    
        if ($this->role) {
            $usersQuery->whereHas('role', function ($query) {
                $query->where('name', $this->role);
            });
        }
        if ($this->profession) {
            $usersQuery->where('user_acl_profession_id', $this->profession);
        }
    
        // **Adding Filters**
        if ($request->filled('username')) {
            $usersQuery->where('login', 'like', '%' . $request->username . '%');
        }
    
        if ($request->filled('email')) {
            $usersQuery->where('email', 'like', '%' . $request->email . '%');
        }
    
        if ($request->filled('status')) {
            $usersQuery->where('active', $request->status);
        }
    
        $users = $usersQuery->orderBy('created_at', 'desc')->paginate(15);
    
        $professions = UserAclProfession::where('is_active', '1')->get();
        // dd($users,'here');
        return view('admin.users.index', compact('users', 'professions'));
    }


    public function edit($id)
    {
        //dd(auth()->user());
        $user = User::find($id);
        $packages = UserAclPackage::latest()->get();
        $roles = UserAclRole::get();
        $professionslist = UserAclProfession::get();


        return view('admin.users.edit', compact('user', 'packages', 'roles', 'professionslist'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        //dd($user);
        $request->validate([
            'lastname' => 'required|string|max:100',
            'firstname' => 'required|string|max:100',
            'active' => 'required|boolean',
        ]);
        
        $forgotPasswordActive = !is_null($user->retrieve_password_key) 
                            && $user->retrieve_updated_at;
        
       //dd($forgotPasswordActive);

        // Add conditional validation for the password
        if ($request->filled('password')) {
             if ($forgotPasswordActive) {
            return redirect()->back()
                ->withErrors(['password' => 'Cannot update password manually while the user has initiated a password reset process.'])
                ->withInput();
        }
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ], [
                'password.required' => 'The password field is required.',
                'password.string' => 'The password must be a string.',
                'password.min' => 'The password must be at least 8 characters.',
                'password.confirmed' => 'The password confirmation does not match.',
            ]);
        }

        $block_user = BlockUser::orwhere('freelancer_id', $id)->orwhere('employer_id', $id)->first();
        $model_user = User::find($id);
        if ($model_user->user_acl_role_id == 2 && ($request->active == 1 || $request->active == 0)) {
            $delte_block = $model_user->is_freelancer;
            if ($delte_block != null) {
                $delte_block->delete();
            }
        } else if ($model_user->user_acl_role_id == 3 && ($request->active == 1 || $request->active == 0)) {
            $delte_block = $model_user->is_employer;
            if ($delte_block != null) {
                $delte_block->delete();
            }
        } else if ($request->active == 2 && $model_user->user_acl_role_id == 2 && $block_user == null) {
            $new_user = new BlockUser();
            $new_user->freelancer_id = $model_user->id;
            $new_user->save();
        } else if ($request->active == 2 && $model_user->user_acl_role_id == 3 && $block_user == null) {
            $new_user = new BlockUser();
            $new_user->employer_id = $model_user->id;
            $new_user->save();
        }
        $user = User::find($id);
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->login = $request->login;
        $user->user_acl_package_id = $request->user_acl_package_id;
        if ($request->filled('password')) {
            $newpassword  = $request->password;
            $user->password = Hash::make($request->password);
            
            if($user->password){
                $user->notify(new PasswordChangeNotification($newpassword));
            }
        }


        $previousActiveStatus = $user->active;
        $user->active = $request->active;
        
        $user->update();
        
        if ($previousActiveStatus !== $request->active) {
            $user->notify(new UserAccountActiveNotification($request->active == 1));
        }
        
        if ($request->active  == 1) {
            $user = User::where('email', $request->email)->first();
            $user->notify(new AccountActiveNotification());
        }

        if ($user) {
            return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
        } else {
            abort(500);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        
        if ($user) {

            if ($user->user_answers) {
                UserAnswer::where('user_id', $id)->delete();
            }
            if ($user->user_extra_info) {
                UserExtraInfo::where('user_id', $id)->delete();
            }
            if ($user->employerStores) {
                EmployerStoreList::where('employer_id', $id)->delete();
            }
            if ($user->user_package_detail) {
                UserPackageDetail::where('user_id', $id)->delete();
            }
            if ($user->paymentInfo) {
                UserPaymentInfo::where('user_id', $id)->delete();
            }
            if ($user->financial_year) {
                FinancialYear::where('user_id', $id)->delete();
            }
            if ($user->GetLastloginUsers) {
                LastLoginUser::where('user_id', $id)->delete();
            }
            
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
        } else {
            abort(404);
        }
    }


    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'login' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'lastname' => 'required|string',
            'firstname' => 'required|string',
            'active' => 'required|in:1,2,3',
            'user_acl_role_id' => 'required|numeric',
            'user_acl_profession_id' => 'nullable|numeric',
            'user_acl_package_id' => 'nullable|required|numeric',

        ];


        $validatedData = $request->validate($rules);
        $user = User::create($validatedData);
        if ($request->active == 2 && $request->user_acl_role_id == 2) {
            $block_user = new BlockUser();
            $block_user->freelancer_id = $user->id;
            $block_user->save();
        } else if ($request->active == 2 && $request->user_acl_role_id == 3) {
            $block_user = new BlockUser();
            $block_user->employer_id = $user->id;
            $block_user->save();
        }
        if ($user) {
            if ($request->submit == "save") {
                return redirect()->back()->with("success", "added successfully");
            } else if ($request->submit == "Save & add new") {
                return redirect()->route('admin.users.create')->with("success", "added successfully");
            }
            return redirect()->route('admin.users.index');
        }

        abort(500);
    }


    public function getBlockUsers(Request $request)
    {

        $arr_ids = [];
        if ($request->startdate && $request->enddate) {
            $BlockUsers = [];
            $Users = User::all()->toArray();
            foreach ($Users as $key => $user) {
                if ($user['user_acl_role_id'] == 2 && $user['active'] == 2) {
                    $filterdate = $user['updated_at'];
                    $getResponse = $this->isDateinRange($request->startdate, $request->enddate, $filterdate);
                    if ($getResponse == TRUE) {
                        $BlockUsers[$key] = $user;
                        $arr_ids[] = $user['id'];
                    }
                } else if ($user['user_acl_role_id'] == 3 && $user['active'] == 2) {
                    $filterdate = $user['updated_at'];
                    $getResponse = $this->isDateinRange($request->startdate, $request->enddate, $filterdate);
                    if ($getResponse == TRUE) {
                        $BlockUsers[$key] = $user;
                        $arr_ids[] = $user['id'];
                    }
                }
            }

            $html = view('admin.reports.block-partial', compact('BlockUsers', 'arr_ids'))->render();
            return response()->json([
                'html' => $html,
            ], 200);
        }

        $BlockUsers = [];
        $Users = User::all()->toArray();
        foreach ($Users as $key => $user) {
            if ($user['user_acl_role_id'] == 2 && $user['active'] == 2) {
                $BlockUsers[$key] = $user;
                $arr_ids[] = $user['id'];
            } else if ($user['user_acl_role_id'] == 3 && $user['active'] == 2) {
                $BlockUsers[$key] = $user;
                $arr_ids[] = $user['id'];
            }
        }
        return view('admin.reports.block-users', compact('BlockUsers', 'arr_ids'));
    }
    public function getNewUsers(Request $request)
    {
        // $users = User::paginate(10)->slice(1);
        // dd($users);
        $users = User::latest()->get(); 
        $arr_ids = [];

        if ($request->startdate && $request->enddate) {
            
            foreach ($users as $key => $value) {
                $filterdate = $value->created_at;
                $getResponse = $this->isDateinRange($request->startdate, $request->enddate, $filterdate);
                if ($getResponse == TRUE) {
                    $arr_ids[] = $value->id;
                }
            }
            $html = view('admin.reports.new-user-partial', compact('users', 'arr_ids'))->render();
            return response()->json([
                'html' => $html,
            ], 200);
        }

        if ($users != null) {
            foreach ($users as $key => $value) {
                $arr_ids[] = $value->id;
            }
        }


        return view('admin.reports.new-users-reports', compact('users', 'arr_ids'));
    }
    public function getLeaverUsers(Request $request)
    {
        $users = User::where('active', 5)->latest()->get();
        //dd($users);
        // $users = Leavers::all();
        $arr_ids = [];
        if ($request->startdate && $request->enddate) {
            if ($users != null) {
                foreach ($users as $keys => $value) {
                    $filterdate = $value->updated_at;
                    $getResponse = $this->isDateinRange($request->startdate, $request->enddate, $filterdate);
                    if ($getResponse == TRUE) {
                        $arr_ids[] = $value->id;
                    }
                }
            }
            $html = view('admin.reports.leaver-user-partials', compact('users', 'arr_ids'))->render();
            return response()->json([
                'html' => $html,
            ], 200);
        }

        if ($users != null) {
            foreach ($users as $key => $value) {
                $arr_ids[] = $value->id;
            }
        }
        //dd($users,$arr_ids);
        return view('admin.reports.leave-user', compact('users', 'arr_ids'));
    }

    public function getLastLogin(Request $request)
    {
        $perPage = 5;
        $usersQuery = User::query()->latest();
        
        $usersQuery->whereHas('GetLastloginUsers');
       
        if ($request->startdate && $request->enddate) {
            $usersQuery->whereHas('GetLastloginUsers', function ($query) use ($request) {
                $query->whereBetween('login_time', [$request->startdate, $request->enddate]);
            });
        }

        $users = $usersQuery->get();

        $totalUsers = $users->count(); // Get total count after filtering

        if ($totalUsers > $perPage) {
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentItems = $users->slice(($currentPage - 1) * $perPage, $perPage)->all();
        
            $users = new LengthAwarePaginator($currentItems, $totalUsers, $perPage, $currentPage, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => request()->query(), // Ensure query params are retained
            ]);
        } else {
            // If records are fewer than perPage, return them all without pagination
            $users = new LengthAwarePaginator($users, $totalUsers, $perPage, 1, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => request()->query(),
            ]);
        }
        
        
                $arr_ids = $users->pluck('id')->toArray();
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.reports.last-login-partial', compact('users', 'arr_ids'))->render(),
                'pagination' => $users->links()->toHtml(),
            ], 200);
        }

       
        return view('admin.reports.last-login', compact('users', 'arr_ids','perPage'));
    }


    public function getEmployerJobReport(Request $request)
    {
        $users = User::where('user_acl_role_id', 3)->where('active', 1)->latest()->get();
        // dd($users);
        $first_arr = [];
        $arr_ids = [];

        // Filter Data according to the Start and End Dates
        if ($request->startdate && $request->enddate) {
            if ($users != null) {
                foreach ($users as $key => $value) {

                    $filterdate = $value->created_at;
                    $getResponse = $this->isDateinRange($request->startdate, $request->enddate, $filterdate);
                    if ($getResponse == TRUE) {

                        $arr_ids[] = $value->id;
                        $job_accept = 0;
                        $job_completed = 0;
                        $JOB_STATUS_CANCELED = 0;
                        $invitation = 0;
                        $accept_job_iterator = $value->getJobPosts;
                        $job_counting = count($value->getJobPosts);

                        if ($accept_job_iterator != null && $job_counting != null) {
                            foreach ($accept_job_iterator as $keys => $values) {
                                $filterdate = $values->created_at;
                                $getResponse = $this->isDateinRange($request->startdate, $request->enddate, $filterdate);
                                $arr_ids[] = $value->id;
                                if ($values->getPrivateInviteUser != null) {
                                    if ($values->getPrivateInviteUser->invited_user_type == 'private_user') {
                                        $invitation = $invitation + 1;
                                    }
                                }
                                if ($values->getJobStatus) {
                                    if ($values->getJobStatus->action == 3) {
                                        $job_accept = $job_accept + 1;
                                    } else if ($values->job_status == 5) {
                                        $job_completed = $job_completed + 1;
                                    }
                                }
                            }
                            $job_success_rate = round(($job_accept / $job_counting) * 100, 2);
                            // dd($job_success_rate);
                            $job_cancel_rate = round(count($value->getJobCancelRate) * 100 / $job_counting);
                            $first_arr[$value->id] = [
                                'user_id' => $value->id,
                                'accept_job' => $job_accept,
                                'job_completed' => $job_completed,
                                'job_cancel' => $JOB_STATUS_CANCELED,
                                'job_suucess_rate' => $job_success_rate,
                                'job_cancel_rate' => $job_cancel_rate,
                                'job_listing' => $job_counting,
                                'invitation' => $invitation
                            ];
                        } else {
                            $first_arr[$value->id] = [
                                'user_id' => $value->id,
                                'accept_job' => 0,
                                'job_completed' => 0,
                                'job_cancel' => 0,
                                'job_suucess_rate' => 0,
                                'job_cancel_rate' => 0,
                                'job_listing' => 0,
                                'invitation' => 0
                            ];
                        }
                    }
                }
            }
            $html = view('admin.reports.employerJobReportPartials', compact('users', 'first_arr', 'arr_ids'))->render();
            return response()->json([
                'html' => $html,
            ], 200);
        }

        if ($users != null) {
            foreach ($users as $key => $value) {
                $arr_ids[] = $value->id;
                $job_accept = 0;
                $job_completed = 0;
                $JOB_STATUS_CANCELED = 0;
                $invitation = 0;
                $accept_job_iterator = $value->getJobPosts;
                $job_counting = count($value->getJobPosts);

                if ($accept_job_iterator != null && $job_counting != null) {
                    foreach ($accept_job_iterator as $keys => $values) {
                        if ($values->getPrivateInviteUser != null) {
                            if ($values->getPrivateInviteUser->invited_user_type == 'private_user') {
                                $invitation = $invitation + 1;
                            }
                        }
                        if ($values->getJobStatus) {
                            if ($values->getJobStatus->action == 3) {
                                $job_accept = $job_accept + 1;
                            } else if ($values->job_status == 5) {
                                $job_completed = $job_completed + 1;
                            }
                        }
                    }
                    // dd($job_accept);
                    $job_success_rate = round($job_accept * 100 / $job_counting, 2);
                    $job_cancel_rate = round(count($value->getJobCancelRate) * 100 / $job_counting);
                    $first_arr[$value->id] = [
                        'user_id' => $value->id,
                        'accept_job' => $job_accept,
                        'job_completed' => $job_completed,
                        'job_cancel' => $JOB_STATUS_CANCELED,
                        'job_suucess_rate' => $job_success_rate,
                        'job_cancel_rate' => $job_cancel_rate,
                        'job_listing' => $job_counting,
                        'invitation' => $invitation
                    ];
                } else {
                    $first_arr[$value->id] = [
                        'user_id' => $value->id,
                        'accept_job' => 0,
                        'job_completed' => 0,
                        'job_cancel' => 0,
                        'job_suucess_rate' => 0,
                        'job_cancel_rate' => 0,
                        'job_listing' => 0,
                        'invitation' => 0
                    ];
                }
            }
        }
        return view('admin.reports.getEmployerJobReport', compact('users', 'first_arr', 'arr_ids'));
    }
    public function getLocumReport(Request $request)
    {
        $users = User::where('user_acl_role_id', 2)->where('active', 1)->latest()->get();
        $data_arr = [];
        $arr_ids = [];

        if ($request->startdate && $request->enddate) {
            if ($users != null) {
                foreach ($users as $key => $value) {
                    $filterdate = $value->created_at;
                    $getResponse = $this->isDateinRange($request->startdate, $request->enddate, $filterdate);
                    if ($getResponse == TRUE) {
                        $arr_ids[] = $value->id;
                        $jobs_accept = 0;
                        $jobs_complete = 0;
                        $jobs_freeze = 0;
                        $job_freeze_accept = 0;
                        $private_job_added = count($value->private_jobs);
                        foreach ($value->getlocumjobs as $keys => $values) {
                            if ($values->action == 3) {
                                $jobs_accept = $jobs_accept + 1;
                            }
                            if ($values->action == 4) {
                                $jobs_complete = $jobs_complete + 1;
                            }
                            if ($values->freeze_notification_count > 0) {
                                $jobs_freeze = $jobs_freeze + 1;
                            }
                            if ($values->freeze_notification_count > 0 && $values->action == 3) {
                                $job_freeze_accept = $job_freeze_accept + 1;
                            }
                        }
                        if ($jobs_accept > 0) {
                            $success_rate = round($jobs_complete * 100 / $jobs_accept);
                            $cancel_rate = round(count($value->getJobCancelRate) * 100 / count($value->getlocumjobs));
                        } else {
                            $success_rate = 0;
                            $cancel_rate = 0;
                        }
                        if ($jobs_freeze > 0) {
                            $jobs_frozen_success_rate = round($job_freeze_accept * 100 / $jobs_freeze);
                        } else {
                            $jobs_frozen_success_rate = 0;
                        }
                        $data_arr[$value->id] = [
                            'user_id' => $value->id,
                            'jobs_applied' => count($value->getlocumjobs),
                            'jobs_accept' => $jobs_accept,
                            'jobs_complete' => $jobs_complete,
                            'success_rate' => $success_rate,
                            'cancel_rate' => $cancel_rate,
                            'jobs_freeze' => $jobs_freeze,
                            'job_freeze_accept' => $job_freeze_accept,
                            'jobs_frozen_success_rate' => $jobs_frozen_success_rate,
                            'private_job_added' => $private_job_added,
                        ];
                    }
                }
            }
            $html = view('admin.reports.getLocumReportPartials', compact('users', 'data_arr', 'arr_ids'))->render();
            return response()->json([
                'html' => $html,
            ], 200);
        }

        if ($users != null) {
            $arr_ids = [];
            foreach ($users as $key => $value) {
                $arr_ids[] = $value->id;
                $jobs_accept = 0;
                $jobs_complete = 0;
                $jobs_freeze = 0;
                $job_freeze_accept = 0;
                $private_job_added = count($value->private_jobs);
                foreach ($value->getlocumjobs as $keys => $values) {
                    if ($values->action == 3) {
                        $jobs_accept = $jobs_accept + 1;
                    }
                    if ($values->action == 4) {
                        $jobs_complete = $jobs_complete + 1;
                    }
                    if ($values->freeze_notification_count > 0) {
                        $jobs_freeze = $jobs_freeze + 1;
                    }
                    if ($values->freeze_notification_count > 0 && $values->action == 3) {
                        $job_freeze_accept = $job_freeze_accept + 1;
                    }
                }
                if ($jobs_accept > 0) {
                    $success_rate = round($jobs_complete * 100 / $jobs_accept);
                    $cancel_rate = round(count($value->getJobCancelRate) * 100 / count($value->getlocumjobs));
                } else {
                    $success_rate = 0;
                    $cancel_rate = 0;
                }
                if ($jobs_freeze > 0) {
                    $jobs_frozen_success_rate = round($job_freeze_accept * 100 / $jobs_freeze);
                } else {
                    $jobs_frozen_success_rate = 0;
                }
                $data_arr[$value->id] = [
                    'user_id' => $value->id,
                    'jobs_applied' => count($value->getlocumjobs),
                    'jobs_accept' => $jobs_accept,
                    'jobs_complete' => $jobs_complete,
                    'success_rate' => $success_rate,
                    'cancel_rate' => $cancel_rate,
                    'jobs_freeze' => $jobs_freeze,
                    'job_freeze_accept' => $job_freeze_accept,
                    'jobs_frozen_success_rate' => $jobs_frozen_success_rate,
                    'private_job_added' => $private_job_added,
                ];
            }
        }
        return view('admin.reports.getLocumJobReport', compact('users', 'data_arr', 'arr_ids'));
    }
    public function privatelocumReport(Request $request)
    {
        $users = User::where('user_acl_role_id', 3)->latest()->get();
        $arr_ids = [];

        if ($request->startdate && $request->enddate) {
            if ($users != null) {
                foreach ($users as $key => $value) {
                    if (count($value->PrivateUser) > 0) {
                        foreach ($value->PrivateUser as $keys => $values) {
                            $filterdate = $values->created_at;
                            $getResponse = $this->isDateinRange($request->startdate, $request->enddate, $filterdate);
                            if ($getResponse == TRUE) {
                                $arr_ids[] = $values->id;
                            }
                        }
                    }
                }
            }
            
            $arr_ids = array_reverse($arr_ids);

            // Reverse the users collection
            $users = $users->reverse();
            
            $html = view('admin.reports.getPrivateLocumsPartials', compact('users', 'arr_ids'))->render();
            return response()->json([
                'html' => $html,
            ], 200);
        }

        if ($users != null) {
            foreach ($users as $key => $value) {
                if (count($value->PrivateUser) > 0) {
                    foreach ($value->PrivateUser as $keys => $values) {
                        $arr_ids[] = $values->id;
                    }
                }
            }
        }
        
        $arr_ids = array_reverse($arr_ids);

        // Reverse the users collection
        $users = $users->reverse();
        // dd($users,$arr_ids);
        return view('admin.reports.getPrivateLocums', compact('users', 'arr_ids'));
    }

    // Function To check the Start and End Dates of User Provided Dates
    public function isDateinRange($startdate, $enddate, $filterdate)
    {
        $startdate = Carbon::parse($startdate);
        $enddate = Carbon::parse($enddate);
        $filterdate = Carbon::parse($filterdate);

        return $filterdate->between($startdate, $enddate);
    }

    public function getLocumPraivateJobs(Request $request)
    {
        $users = User::where('user_acl_role_id', 2)->latest()->get();
        $arr_ids = [];
        // Filter Data according to the Start and End Dates
        if ($request->startdate && $request->enddate) {
            foreach ($users as $key => $value) {
                if (count($value->private_jobs) > 0) {
                    foreach ($value->private_jobs as $keys => $values) {
                        $filterdate = $values->job_date;
                        $getResponse = $this->isDateinRange($request->startdate, $request->enddate, $filterdate);
                        if ($getResponse == TRUE) {
                            $arr_ids[] = $values->id;
                        }
                    }
                }
            }
            
            $arr_ids = array_reverse($arr_ids);

            // Reverse the users collection
            $users = $users->reverse();
            
            $html = view('admin.reports.render_locam_private_jobs', compact('users', 'arr_ids'))->render();
            return response()->json([
                'html' => $html,
            ], 200);
        } else {
            foreach ($users as $key => $value) {
                if (count($value->private_jobs) > 0) {
                    foreach ($value->private_jobs as $keys => $values) {
                        $arr_ids[] = $values->id;
                    }
                }
            }
        }

        // Reverse the order of arr_ids
        $arr_ids = array_reverse($arr_ids);
    
        // Reverse the users collection
        $users = $users->reverse();
        return view('admin.reports.getLocumPraivateJobs', compact('users', 'arr_ids'));
    }

    public function getBlockUsersExport(Request $request)
    {
        
        $users = User::select('id', 'firstname', 'lastname', 'email', 'created_at', 'active','user_acl_profession_id', 'user_acl_role_id')
            ->with(['user_acl_profession', 'role'])
            ->where('active', 2)
            ->whereIn('user_acl_role_id', [2, 3])
            
            ->get()
            ->map(function ($user) {
                $user->active = 'Block';
                return $user;
        });
    
        
        return Excel::download(new BlockUsersExport($users), 'BlockUsers.csv');
    }
    public function getNewUsersExport(Request $request)
    {
        $users = User::select('id', 'firstname', 'lastname', 'email', 'created_at', 'user_acl_profession_id','user_acl_role_id')
        ->with(['user_acl_profession','role']) 
        ->get();
        return Excel::download(new NewUserExport($users), 'NewUsers.csv');
    }
    public function getLeaverUsersExport(Request $request)
    {
        $users = User::where('active', 5)
        ->select('id', 'firstname', 'lastname', 'email', 'created_at', 'user_acl_profession_id','user_acl_role_id')
        ->with(['user_acl_profession','role','GetLeaveReport'])
        ->get();
       
        return Excel::download(new LeaverUsersExport($users), 'LeaveUsers.csv');
    }
    public function getLastLoginUsersExport(Request $request)
    {
        $users = User::select('id', 'firstname', 'lastname', 'email', 'created_at', 'user_acl_profession_id','user_acl_role_id')
        ->with(['user_acl_profession','role','GetLastloginUsers'])
        ->get();
        
        return Excel::download(new LastLoginUsersExport($users), 'LastLoginUsers.csv');
    }
   
    public function getEmployerJobReportExport(Request $request)
    {
        $users = User::where('user_acl_role_id', 3)
            ->where('active', 1)
            ->with(['getJobPosts.getPrivateInviteUser', 'getJobPosts.getJobStatus', 'getJobCancelRate'])
            ->get();
    
        $filtered_users = $users->map(function ($user) {
            $job_accept = 0;
            $job_completed = 0;
            $invitation = 0;
            $job_posts = $user->getJobPosts ?? [];
            $job_count = count($job_posts);
    
            foreach ($job_posts as $job) {
                // Count private job invitations
                if ($job->getPrivateInviteUser?->invited_user_type === 'private_user') {
                    $invitation++;
                }
    
                // Count job status
                if ($job->getJobStatus) {
                    if ($job->getJobStatus->action === 3) {
                        $job_accept++;
                    } elseif ($job->job_status === 5) {
                        $job_completed++;
                    }
                }
            }
    
            
            $job_success_rate = ($job_count > 0) ? round(($job_accept / $job_count) * 100, 2) : "0";
            $job_cancel_rate = ($job_count > 0) ? round((count($user->getJobCancelRate ?? []) * 100) / $job_count, 2) : "0";
    
            // Return only required fields, ensuring all values default to 0 if missing
            return [
                'User Id'                           => $user->id,
                'Employer'                          => (!empty($user->firstname) && !empty($user->lastname)) ? "{$user->firstname} {$user->lastname}" : 'Unknown',
                'Jobs Listed'                       => $job_count > 0 ? $job_count : "0",
                'Jobs Accepted'                     => $job_accept > 0 ? $job_accept : "0",
                'Success Rate'                      => $job_success_rate > 0 ? $job_success_rate : "0",
                'Cancel Rate'                       => $job_cancel_rate > 0 ? $job_cancel_rate : "0",
                'Number of Private job requests sent' => $invitation > 0 ? $invitation : "0",
            ];
        });
    
        return Excel::download(new EmployerJobReportExport($filtered_users), 'EmployerJobReport.csv');
    }

    public function getLocumReportExport(Request $request)
    {
        $users = User::where('user_acl_role_id', 2)->where('active', 1)->get();
    
        $data_arr = [];
    
        foreach ($users as $user) {
            $jobs_accept = 0;
            $jobs_complete = 0;
            $jobs_freeze = 0;
            $job_freeze_accept = 0;
            $private_job_added = $user->private_jobs->count();
    
            foreach ($user->getlocumjobs as $job) {
                if ($job->action == 3) {
                    $jobs_accept++;
                }
                if ($job->action == 4) {
                    $jobs_complete++;
                }
                if ($job->freeze_notification_count > 0) {
                    $jobs_freeze++;
                }
                if ($job->freeze_notification_count > 0 && $job->action == 3) {
                    $job_freeze_accept++;
                }
            }
    
            $total_jobs = $user->getlocumjobs->count();
            $cancel_rate = ($total_jobs > 0) ? round($user->getJobCancelRate->count() * 100 / $total_jobs) : "0";
            $success_rate = ($jobs_accept > 0) ? round($jobs_complete * 100 / $jobs_accept) : "0";
            $jobs_frozen_success_rate = ($jobs_freeze > 0) ? round($job_freeze_accept * 100 / $jobs_freeze) : "0";
    
            $data_arr[] = [
                'user_id' => $user->id,
                'Locum' => $user->firstname . ' ' . $user->lastname,
                'jobs_applied' => $total_jobs,
                'jobs_accept' => strval($jobs_accept),
                'jobs_complete' => strval($jobs_complete),
                'success_rate' => strval($success_rate),
                'cancel_rate' => strval($cancel_rate),
                'jobs_freeze' => strval($jobs_freeze),
                'job_freeze_accept' => strval($job_freeze_accept),
                'jobs_frozen_success_rate' => strval($jobs_frozen_success_rate),
                'private_job_added' => strval($private_job_added),
    
            ];
        }
    
        return Excel::download(new getLocumReportExport($data_arr), 'LocumJobReport.csv');
    }
    
    public function privatelocumReportExport(Request $request)
    {
        $arr_ids = [];
    
        $users = User::where('user_acl_role_id', 3)
            ->with(['user_acl_profession', 'PrivateUser']) 
            ->get();
            
        if ($users != null) {
            foreach ($users as $key => $value) {
                if (count($value->PrivateUser) > 0) {
                    foreach ($value->PrivateUser as $keys => $values) {
                        $arr_ids[] = $values->id;
                    }
                }
            }
        }

        return Excel::download(new LocumPrivateJobReport($users, $arr_ids), 'LocumPrivateJobReport.csv');
    }

    public function getLocumPraivateJobsExport(Request $request)
    {
     
        $users = User::where('user_acl_role_id', 2)->get();
        $arr_ids = [];
        
        foreach ($users as $key => $value) {
            if (count($value->private_jobs) > 0) {
                foreach ($value->private_jobs as $keys => $values) {
                    $arr_ids[] = $values->id;
                }
            }
        }
        
        return Excel::download(new LocumPrivateJobsExport($users, $arr_ids), 'LocumPrivateJobs.csv');
    }
    public function BlogPost()
    {
        return view('admin.blog.test');
    }
    public function singleEmployerJobReport($id)
    {
        $jobs = JobPost::where('employer_id', $id)->get();
        return view('admin.reports.singleEmployerJobReport', compact('jobs'));
    }

    public function singleLocumJobReport($id)
    {
        $jobs = JobAction::where('freelancer_id', $id)->get();
        $freelancerJobs = FreelancerPrivateJob::where('freelancer_id', $id)->get();
        return view('admin.reports.singleLocumJobReport', compact('jobs', 'freelancerJobs'));
    }
}
