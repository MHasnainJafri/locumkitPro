<?php

namespace App\Models;

use Beta\Microsoft\Graph\Model\LastSignIn;
use Beta\Microsoft\Graph\Networkaccess\Model\TransactionSummary;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use PhpParser\Node\Expr\Cast\Bool_;
use Nette\Utils\Random;
use App\Notifications\SendResetTokenNotification;
use Illuminate\Support\Facades\Hash;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'login',
        'password',
        'active',
        'user_acl_role_id',
        'user_acl_profession_id',
        'user_acl_package_id',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    const USER_STATUS_DISABLE  = 0;
    const USER_STATUS_ACTIVE  = 1;
    const USER_STATUS_BLOCK  = 2;
    const USER_STATUS_GUESTUSER  = 3;
    const USER_STATUS_EXPIRED  = 4;
    const USER_STATUS_DELETED = 5;

    const USER_ROLE_ADMIN = 1;
    const USER_ROLE_LOCUM = 2;
    const USER_ROLE_EMPLOYER = 3;

    const USER_PROFESSION_DEFAULT = 3;

    public function paymentInfo()
    {
        return $this->hasOne(UserPaymentInfo::class);
    }
    public function role()
    {
        return $this->belongsTo(UserAclRole::class, "user_acl_role_id", "id");
    }

    public function user_extra_info()
    {
        return $this->hasOne(UserExtraInfo::class);
    }

    public function user_acl_package()
    {
        return $this->belongsTo(UserAclPackage::class, "user_acl_package_id", "id");
    }

    public function user_package_detail()
    {
        return $this->hasOne(UserPackageDetail::class, "user_id", "id");
    }

    public function user_acl_profession()
    {
        return $this->belongsTo(UserAclProfession::class, "user_acl_profession_id", "id");
    }

    public function user_answers()
    {
        return $this->hasMany(UserAnswer::class);
    }
    public function private_jobs()
    {
        return $this->hasMany(FreelancerPrivateJob::class, "freelancer_id", "id");
    }

    public function user_bank_detail()
    {
        return $this->hasOne(UserBankDetail::class);
    }

    public function user_work_calender()
    {
        return $this->hasOne(UsersWorkCalender::class, "user_id", "id");
    }
    public function financial_year()
    {
        return $this->hasOne(FinancialYear::class, "user_id", "id");
    }

    public function isUserProfileCompleted()
    {
        if ($this->user_acl_role_id == 2) {
            $question_accessor = "freelancer_question";
        } else {
            $question_accessor = "employer_question";
        }
        $query_string = "SELECT CASE WHEN (SELECT count(*) FROM user_questions WHERE is_required = 1 AND {$question_accessor} != '' AND {$question_accessor} IS NOT NULL AND user_acl_profession_id = '{$this->user_acl_profession_id}') = (SELECT count(*) FROM user_answers WHERE user_id = '{$this->id}' AND type_value != '' AND user_question_id IN (SELECT id FROM user_questions WHERE is_required = 1)) THEN 1 ELSE 0 END AS result";
        $result = DB::selectOne($query_string)->result;
        return $result == 1;
    }

    public function job_invitations()
    {
        return $this->morphMany(JobInvitedUser::class, 'invited_user');
    }

    public function get_freelancer_rate_on_date($date): float
    {
        $available_dates = json_decode($this->user_work_calender?->available_dates, true);
        if ($available_dates && sizeof($available_dates) > 0) {
            $min_rate = 0;
            foreach ($available_dates as $available_date) {
                if (Carbon::parse($date)->equalTo(Carbon::parse($available_date["date"]))) {
                    return $available_date["min_rate"];
                }
            }
        }
        $minimum_rate = json_decode($this->user_extra_info->minimum_rate, true);
        if ($minimum_rate && key_exists(Carbon::parse($date)->dayName, $minimum_rate)) {
            return $minimum_rate[Carbon::parse($date)->dayName];
        }
        return 0;
    }

    public function is_available_on_date($date): bool
    {
        $date = Carbon::parse($date);
        $block_dates = json_decode($this->user_work_calender?->block_dates, true);
        
        if ($block_dates && sizeof($block_dates) > 0) {
            foreach ($block_dates as $block_date) {
                if ($date->equalTo(Carbon::parse($block_date))) {
                    return false;
                }
            }
        }
        $freelancer_private_jobs_count = FreelancerPrivateJob::where("freelancer_id", $this->id)->whereDate("job_date", $date)->count();
        if ($freelancer_private_jobs_count > 0) {
            return false;
        }

        $allLiveJobs = JobPost::whereIn("job_status", [JobPost::JOB_STATUS_ACCEPTED])
            ->whereDate("job_date", $date)
            ->whereHas("job_actions", function ($query) {
                $query->where("freelancer_id", $this->id);
                $query->whereIn("action", [JobAction::ACTION_ACCEPT]);
            })->count();
           
        if ($allLiveJobs > 0) {
            return false;
        }
        return true;
    }

    public function can_freelancer_get_job_invitation(): bool
    {
        $package_resorce_ids = json_decode($this->user_acl_package->user_acl_package_resources_ids_list) ?? [];
        $resource_count = UserAclPackageResource::where("resource_key", "job_invitation")->whereIn("id", $package_resorce_ids)->count();
        return $resource_count > 0;
    }
    public function can_freelancer_get_job_reminders(): bool
    {
        $package_resorce_ids = json_decode($this->user_acl_package->user_acl_package_resources_ids_list) ?? [];
        $resource_count = UserAclPackageResource::where("resource_key", "job_reminders")->whereIn("id", $package_resorce_ids)->count();
        return $resource_count > 0;
    }
    public function can_freelancer_get_feedback(): bool
    {
        $package_resorce_ids = json_decode($this->user_acl_package->user_acl_package_resources_ids_list) ?? [];
        $resource_count = UserAclPackageResource::where("resource_key", "feedback")->whereIn("id", $package_resorce_ids)->count();
        return $resource_count > 0;
    }

    public function get_user_block_dates(): array|null
    {
        $user_block_dates = $this->user_work_calender()->select("block_dates")->first();
        if ($user_block_dates && $user_block_dates->block_dates && json_decode($user_block_dates->block_dates)) {
            return json_decode($user_block_dates->block_dates, true);
        }
        return null;
    }


    // User.php

public function feedbackGiven()
{
    return $this->hasMany(JobFeedback::class, 'employer_id');
}

public function feedbackReceived()
{
    return $this->hasMany(JobFeedback::class, 'freelancer_id');
}



//  aded for admin apnel


public function financeIncomes()
{
    return $this->hasMany(FinanceIncome::class, 'freelancer_id', 'id');
}
    public function income_sum_price($year, $id)
    {
        $current_year = $year;
        $previous_year = $year - 1 ;
        $current_income_finance = FinanceIncome::where('freelancer_id', $id->id)->whereYear('job_date', $current_year)->get()->toArray();
        $previous_income_finance = FinanceIncome::where('freelancer_id', $id->id)->whereYear('job_date', $previous_year)->get()->toArray();
        $financial_year = FinancialYear::where('user_id', $id->id)->first();
        $totalincome = 0 ;
        if($previous_income_finance != null){
            foreach($previous_income_finance as $key => $finance){
                $carbonMonth = Carbon::parse($finance['job_date']);
                $month = $carbonMonth->format('m');
                if($month >= $financial_year->month_start){
                    $totalincome = $totalincome + $finance['job_rate'];
                }
            }
        }
        if($current_income_finance){
            foreach($current_income_finance as $key => $finance){
                $carbonMonth = Carbon::parse($finance['job_date']);
                $month = $carbonMonth->format('m');
                if($month <= $financial_year->month_end){
                    $totalincome = $totalincome + $finance['job_rate'];
                }
            }
        }

        return $totalincome;
    }
    public function expense_sum_price($year, $id)
    {
        $current_year = $year;
        $previous_year = $year - 1 ;
        $current_expense_finance = FinanceExpense::where('freelancer_id', $id)->whereYear('job_date', $current_year)->get()->toArray();
        $previous_expense_finance = FinanceExpense::where('freelancer_id', $id)->whereYear('job_date', $previous_year)->get()->toArray();
        $fin_years = FinancialYear::where('user_id', $id)->first();
        $totalincome = 0 ;

        
        $datas = FinanceExpense::where('freelancer_id', $id)->whereyear('job_date',$year)->get();

        $cos = 0;
        $adm_exp = 0;
        if($current_expense_finance != null){
            foreach($current_expense_finance as $key => $data){
                $carbonMonth = Carbon::parse($data['job_date']);

                $month = $carbonMonth->format('m');
                if($month <= $fin_years['month_end']){
                    
                    if($data['expense_type_id'] == 1 || $data['expense_type_id'] == 2 || $data['expense_type_id'] == 3){
                        $cos = $cos + $data['job_rate'];
                    }
                    else{
                        $adm_exp = $adm_exp + $data['job_rate'];
                    }
                }
            }
        }
        if($previous_expense_finance != null){
            foreach($previous_expense_finance as $key => $data){
                $carbonMonth = Carbon::parse($data['job_date']);

                $month = $carbonMonth->format('m');
                if($month >= $fin_years['month_end']){

                    if($data['expense_type_id'] == 1 || $data['expense_type_id'] == 2 || $data['expense_type_id'] == 3){
                        $cos = $cos + $data['job_rate'];
                    }
                    else{
                        $adm_exp = $adm_exp + $data['job_rate'];
                    }
                }
            }
        }
        $data = ['adm_exp' => $adm_exp, 'cos' => $cos];
        return $data;
    }

    public function financeExpanses()
    {
        return $this->hasMany(FinanceExpense::class, 'freelancer_id', 'id');
    }
    public function supplier_list()
    {
        return $this->hasMany(Supplier::class, 'created_by_user_id', 'id');
    }
    public function transactions_list(User $id){
        $transactioins = $this->hasMany(TransactionSummary::class,);
    }
    public function is_freelancer(){
        return $this->hasone(BlockUser::class, 'freelancer_id', 'id');
    }
    public function is_employer(){
        return $this->hasOne(BlockUser::class, 'employer_id', 'id');
    }
    public function GetLeaveReport(){
        return $this->hasone(Leavers::class,'uid','id');
    }
    public function GetLastloginUsers(){
        return $this->hasone(LastLoginUser::class, 'user_id', 'id');
    }
    public function getJobPosts(){
        return $this->hasMany(JobPost::class,'employer_id', 'id');
    }
    public function getJobCancelRate(){
       return $this->hasMany(JobCancelation::class, 'user_id', 'id');
    }
    public function getlocumjobs(){
        return $this->hasMany(JobAction::class, 'freelancer_id', 'id');
    }
    public function PrivateUser(){
        return $this->hasMany(PrivateUser::class, 'employer_id', 'id');
    }
    public function employerStores(){
        return $this->hasMany(EmployerStoreList::class, 'employer_id', 'id');
    }
    public function sendOtp()
    {
        $otp = Random::generate(4, '0-9');
        try {
            $this->notify(new SendResetTokenNotification($otp));
            DB::table('password_resets')->updateOrInsert(['email' => $this->email], ['token' => Hash::make($otp), 'created_at' => now()]);
            return $otp;
        } catch (\Exception $e) {
            return false;
        }
    }
}
