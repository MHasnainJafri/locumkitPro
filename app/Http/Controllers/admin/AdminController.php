<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Models\JobPost;
use Illuminate\Http\Request;
use App\Models\FinanceIncome;
use App\Http\Controllers\Controller;
use App\Models\UserPaymentInfo;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // Get the current year and month
        $currentYear = date('Y');
        $currentMonth = date('n');
    
        // Initialize arrays for months and counters
        $months = [];
        $locumCounts = [];
        $employeeCounts = [];
    
        // Generate months and initialize counters
        for ($i = 1; $i <= $currentMonth; $i++) {
            $months[] = date('M', mktime(0, 0, 0, $i, 1, $currentYear));
            $locumCounts[] = 0;
            $employeeCounts[] = 0;
        }
    
        // Query the database to count locum and employee registrations
        $data = User::selectRaw('MONTH(created_at) as month, user_acl_role_id, count(*) as count')
            ->whereYear('created_at', $currentYear)
            ->whereIn('user_acl_role_id', [2, 3]) // Assuming 2 is locum and 3 is employee
            ->groupBy('month', 'user_acl_role_id')
            ->get();
    
        // Populate the counters with the count data
        foreach ($data as $item) {
            $monthIndex = $item->month - 1; // Adjust the month index
            if ($item->user_acl_role_id == 2) {
                $locumCounts[$monthIndex] = $item->count;
            } elseif ($item->user_acl_role_id == 3) {
                $employeeCounts[$monthIndex] = $item->count;
            }
        }

        $allusersCount = User::whereYear('created_at',  date('Y'))->count();
        $alljobPost =  JobPost::whereYear('created_at',  date('Y'))->count();
        $yearturnover=  UserPaymentInfo::whereYear('created_at',  date('Y'))->sum('price');
    
        // Pass the data to your view
        return view('admin.dashboard', compact('months', 'locumCounts', 'employeeCounts','allusersCount','alljobPost','yearturnover'));
    }
    
}
