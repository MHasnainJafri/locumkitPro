<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\FinanceHelper;
use App\Http\Resources\EmployerStoreListResource;
use App\Http\Resources\EmployerTransactionResource;
use App\Models\EmployerStoreList;
use App\Models\FinanceEmployer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployerStoreController extends Controller
{
    public function multiStore(Request $request)
    {
        $uid = $request['user_id'];
        $store_lists = EmployerStoreList::where("employer_id", $uid)->where("status", 0)->orderBy("store_name")->get();
        $data = EmployerStoreListResource::collection($store_lists)->jsonSerialize();
        return response()->success($data);
    }

    public function manageStores(Request $request)
    {
        $user_data = $request->all();
        $uid = isset($user_data['uid']) ? $user_data['uid'] : '';
        $request_type = $request->input("type");
        match ($request_type) {
            'add-new' => $this->add_new_stores($user_data),
            'edit' => $this->update_user_stores($user_data),
            'delete' => $this->delete_user_stores($user_data),
            default => null
        };
        $getvalues = EmployerStoreList::query();
        if ($request->input('type') == 'getByID') {
            $sid = isset($user_data['sid']) ? $user_data['sid'] : '';
            $getvalues = $getvalues->where("id", $sid);
        }
        $getvalues = $getvalues->where("employer_id", $uid)->where("status", 0)->select(DB::raw("id as emp_st_id, store_name as emp_store_name,store_address as emp_store_address,store_region as emp_store_region,store_zip as emp_store_zip,store_start_time,store_end_time,store_lunch_time"))->get()->toArray();
        $results = array();
        if (!empty($uid)) {
            foreach ($getvalues as $key => $value) {
                if ($value['store_start_time'] != '' && json_decode($value['store_start_time'], true)) {
                    $store_start_time = array();
                    foreach (json_decode($value['store_start_time'], true) as $day => $day_val) {
                        $store_start_time[] = [
                            $day => $day_val
                        ];
                    }
                    $value['store_start_time'] = $store_start_time;
                } else {
                    $value['store_start_time'] = null;
                }
                if ($value['store_end_time'] != '' && json_decode($value['store_end_time'], true)) {
                    $store_end_time = array();
                    foreach (json_decode($value['store_end_time'], true) as $day => $day_val) {
                        $store_end_time[] = [
                            $day => $day_val
                        ];
                    }
                    $value['store_end_time'] = $store_end_time;
                } else {
                    $value['store_end_time'] = null;
                }
                if ($value['store_lunch_time'] != '' && json_decode($value['store_lunch_time'], true)) {
                    $store_lunch_time = array();
                    foreach (json_decode($value['store_lunch_time'], true) as $day => $day_val) {
                        $store_lunch_time[] = [
                            $day => $day_val
                        ];
                    }
                    $value['store_lunch_time'] = $store_lunch_time;
                } else {
                    $value['store_lunch_time'] = null;
                }
                $results['storelist'][$key] = $value;
                $results['storelist'][$key]['timelist'] = array();
                if (isset($store_start_time) && isset($store_end_time) && isset($store_lunch_time)) {

                    $results['storelist'][$key]['timelist'][0]['day'] = 'Mon';
                    $results['storelist'][$key]['timelist'][1]['day'] = 'Tue';
                    $results['storelist'][$key]['timelist'][2]['day'] = 'Wed';
                    $results['storelist'][$key]['timelist'][3]['day'] = 'Thu';
                    $results['storelist'][$key]['timelist'][4]['day'] = 'Fri';
                    $results['storelist'][$key]['timelist'][5]['day'] = 'Sat';
                    $results['storelist'][$key]['timelist'][6]['day'] = 'Sun';
                    $results['storelist'][$key]['timelist'][0]['start'] = $value['store_start_time'] ? $value['store_start_time'][0]['Monday'] : "9:00";
                    $results['storelist'][$key]['timelist'][1]['start'] = $value['store_start_time'] ? $value['store_start_time'][1]['Tuesday'] : "9:00";
                    $results['storelist'][$key]['timelist'][2]['start'] = $value['store_start_time'] ? $value['store_start_time'][2]['Wednesday'] : "9:00";
                    $results['storelist'][$key]['timelist'][3]['start'] = $value['store_start_time'] ? $value['store_start_time'][3]['Thursday'] : "9:00";
                    $results['storelist'][$key]['timelist'][4]['start'] = $value['store_start_time'] ? $value['store_start_time'][4]['Friday'] : "9:00";
                    $results['storelist'][$key]['timelist'][5]['start'] = $value['store_start_time'] ? $value['store_start_time'][5]['Saturday'] : "9:00";
                    $results['storelist'][$key]['timelist'][6]['start'] = $value['store_start_time'] ? $value['store_start_time'][6]['Sunday'] : "9:00";
                    $results['storelist'][$key]['timelist'][0]['lunch'] = $value['store_lunch_time'] ? $value['store_lunch_time'][0]['Monday'] : "30";
                    $results['storelist'][$key]['timelist'][1]['lunch'] = $value['store_lunch_time'] ? $value['store_lunch_time'][1]['Tuesday'] : "30";
                    $results['storelist'][$key]['timelist'][2]['lunch'] = $value['store_lunch_time'] ? $value['store_lunch_time'][2]['Wednesday'] : "30";
                    $results['storelist'][$key]['timelist'][3]['lunch'] = $value['store_lunch_time'] ? $value['store_lunch_time'][3]['Thursday'] : "30";
                    $results['storelist'][$key]['timelist'][4]['lunch'] = $value['store_lunch_time'] ? $value['store_lunch_time'][4]['Friday'] : "30";
                    $results['storelist'][$key]['timelist'][5]['lunch'] = $value['store_lunch_time'] ? $value['store_lunch_time'][5]['Saturday'] : "30";
                    $results['storelist'][$key]['timelist'][6]['lunch'] = $value['store_lunch_time'] ? $value['store_lunch_time'][6]['Sunday'] : "30";
                    $results['storelist'][$key]['timelist'][0]['end'] = $value['store_end_time'] ? $value['store_end_time'][0]['Monday'] : "17:00";
                    $results['storelist'][$key]['timelist'][1]['end'] = $value['store_end_time'] ? $value['store_end_time'][1]['Tuesday'] : "17:00";
                    $results['storelist'][$key]['timelist'][2]['end'] = $value['store_end_time'] ? $value['store_end_time'][2]['Wednesday'] : "17:00";
                    $results['storelist'][$key]['timelist'][3]['end'] = $value['store_end_time'] ? $value['store_end_time'][3]['Thursday'] : "17:00";
                    $results['storelist'][$key]['timelist'][4]['end'] = $value['store_end_time'] ? $value['store_end_time'][4]['Friday'] : "17:00";
                    $results['storelist'][$key]['timelist'][5]['end'] = $value['store_end_time'] ? $value['store_end_time'][5]['Saturday'] : "17:00";
                    $results['storelist'][$key]['timelist'][6]['end'] = $value['store_end_time'] ? $value['store_end_time'][6]['Sunday'] : "17:00";
                }
            }
        }

        return response(json_encode($results));
    }

    public function add_new_stores($storedata)
    {
        $store_info = isset($storedata['store_info']) ? $storedata['store_info'] : '';
        //Save store info
        $uid = isset($store_info['id']) ? $store_info['id'] : '';
        $store_name = isset($store_info['name']) ? $store_info['name'] : '';
        $store_address = isset($store_info['address']) ? $store_info['address'] : '';
        $store_town = isset($store_info['town']) ? $store_info['town'] : '';
        $store_postcode = isset($store_info['postcode']) ? $store_info['postcode'] : '';
        $store_start_time = array();
        $store_end_time = array();
        $store_lunch_time = array();
        foreach ($store_info as $key => $store_time) {
            if (strpos($key, '_start_time') !== false) {
                $day = ucwords(ucfirst(str_replace('_start_time', '', $key)));
                $store_start_time[$day] =  $store_time;
            }
            if (strpos($key, '_end_time') !== false) {
                $day = ucwords(ucfirst(str_replace('_end_time', '', $key)));
                $store_end_time[$day] = $store_time;
            }
            if (strpos($key, '_lunch_time') !== false) {
                $day = ucwords(ucfirst(str_replace('_lunch_time', '', $key)));
                $store_lunch_time[$day] = str_replace('00:', '', $store_time);
            }
        }
        if (sizeof($store_start_time) < 7 || sizeof($store_end_time) < 7 || sizeof($store_lunch_time) < 7) {
            return;
        }

        $store_start_time = json_encode($store_start_time);
        $store_end_time = json_encode($store_end_time);
        $store_lunch_time = json_encode($store_lunch_time);
        $store = new EmployerStoreList();
        $store->employer_id = $uid;
        $store->store_name = $store_name;
        $store->store_address = $store_address;
        $store->store_region = $store_town;
        $store->store_zip = $store_postcode;
        $store->store_start_time = $store_start_time;
        $store->store_end_time = $store_end_time;
        $store->store_lunch_time = $store_lunch_time;
        $store->save();
    }

    public function update_user_stores($storedata)
    {
        $store_info = isset($storedata['store_info']) ? $storedata['store_info'] : '';
        //Save store info
        $uid = isset($store_info['id']) ? $store_info['id'] : '';
        $sid = isset($store_info['sid']) ? $store_info['sid'] : '';
        $store_name = isset($store_info['name']) ? $store_info['name'] : '';
        $store_address = isset($store_info['address']) ? $store_info['address'] : '';
        $store_town = isset($store_info['town']) ? $store_info['town'] : '';
        $store_postcode = isset($store_info['postcode']) ? $store_info['postcode'] : '';
        $store_start_time = array();
        $store_end_time = array();
        $store_lunch_time = array();
        foreach ($store_info as $key => $store_time) {
            if (strpos($key, '_start_time') !== false) {
                $day = ucwords(ucfirst(str_replace('_start_time', '', $key)));
                $store_start_time[$day] =  $store_time;
            }
            if (strpos($key, '_end_time') !== false) {
                $day = ucwords(ucfirst(str_replace('_end_time', '', $key)));
                $store_end_time[$day] = $store_time;
            }
            if (strpos($key, '_lunch_time') !== false) {
                $day = ucwords(ucfirst(str_replace('_lunch_time', '', $key)));
                $store_lunch_time[$day] = str_replace('00:', '', $store_time);
            }
        }
        if (sizeof($store_start_time) < 7 || sizeof($store_end_time) < 7 || sizeof($store_lunch_time) < 7) {
            return;
        }
        $store_start_time = json_encode($store_start_time);
        $store_end_time = json_encode($store_end_time);
        $store_lunch_time = json_encode($store_lunch_time);
        $store = EmployerStoreList::where("id", $sid)->where("employer_id", $uid)->first();
        if ($store) {
            $store->store_name = $store_name;
            $store->store_address = $store_address;
            $store->store_region = $store_town;
            $store->store_zip = $store_postcode;
            $store->store_start_time = $store_start_time;
            $store->store_end_time = $store_end_time;
            $store->store_lunch_time = $store_lunch_time;
            $store->save();
        }
    }
    public function delete_user_stores($storedata)
    {
        $uid = isset($storedata['uid']) ? $storedata['uid'] : '';
        $sid = isset($storedata['sid']) ? $storedata['sid'] : '';
        EmployerStoreList::where("id", $sid)->where("employer_id", $uid)->update([
            "status" => 1
        ]);
    }
    
    public function saveTransaction(Request $request)
    {
        $request->validate([
            "fre_type" => "required|in:1,2",
            "job_id" => "required",
            "fre_id" => "required_if:fre_type,1",
            "in_date" => "required",
            "rate" => "required",
            "paid" => "nullable",
            "paid_date" => "required_if:paid,1",
        ]);
        
        $paid = $request->input("paid") == 1 ? true : false;
        $fre_type = $request->input("fre_type");
        $job_id = $request->input("job_id");
        $fre_id = $request->input("fre_id");
        $in_date = parse_date_from_default_format($request->input("in_date"));
        $rate = $request->input("rate");
        $bonus = $request->input("bonus");
        $paid_date = parse_date_from_default_format($request->input("paid_date"));

        $transaction = new FinanceEmployer();
        $transaction->employer_id = auth()->user()->id;
        $transaction->job_id = $job_id;
        $transaction->freelancer_id = $fre_id;
        $transaction->freelancer_type = $fre_type;
        $transaction->job_date = $in_date->format("Y-m-d");
        $transaction->job_rate = $rate;
        $transaction->bonus = $bonus;
        $transaction->is_paid = $paid;
        $transaction->paid_date = $paid_date ? $paid_date->format("Y-m-d") : null;

        $transaction->save();
        return response()->json([
            'success' => true,
            'message' => "transaction added successfully"
            ]);
    }
    
    public function getTransactionChartData()
{
    $finance_helper = new FinanceHelper(auth()->user());

    $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
    $current_year = now()->format('Y');
    $filter_year = intval($current_year);

    $year_range = get_financial_year_range($finance_year_start_month, $filter_year);
    $year_start = $year_range['year_start'];
    $year_end = $year_range['year_end'];

    $employer_finance_cost = $finance_helper->get_employer_finance_cost_chart_data($year_start, $year_end);
    $employer_finance_job = $finance_helper->get_employer_finance_jobs_chart_data($year_start, $year_end);

    // Prepare chart data
    $cost_labels = array_keys($employer_finance_cost);
    $cost_values = array_values($employer_finance_cost);
    
    $job_labels = array_keys($employer_finance_job);
    $job_values = array_values($employer_finance_job);
    
    // Common colors for the chart
    $colors = array_fill(0, count($cost_labels), 'rgba(164, 68, 66, 0.5)');
    $borderColors = array_fill(0, count($cost_labels), 'rgba(164, 68, 66, 1)');
    

    // Format the data for the charts
    $response = [
        'cost' => [
            'labels' => $cost_labels,
            'x' => $cost_labels,
            'values' => $cost_values,
            'y' => $cost_values,
            'color' => $colors,
            'border_color' => $borderColors,
        ],
        'job' => [
            'labels' => $job_labels,
            'x' => $job_labels,
            'values' => $job_values,
            'y' => $job_values,
            'color' => $colors,
            'border_color' => $borderColors,
        ],
        'transactions' => EmployerTransactionResource::collection(
            FinanceEmployer::query()
                ->where('employer_id', auth()->user()->id)
                ->whereBetween('job_date', [$year_start, $year_end])
                ->get()
        )->jsonSerialize(),
    ];

    return response()->json($response);
}


    
    public function editTransaction(Request $request)
    {
        $transaction = FinanceEmployer::query()->where("employer_id", Auth()->user()->id)->where("id", $request->transaction_id)->first();
        // return [Auth()->user()->id , $request->transaction_id, $transaction];
        // return [$request->all() , $transaction, Auth()->user()->id];
        if (is_null($transaction)) {
            return response([
                'success' => false,
                'message' => 'No Transaction Found.'
                ]);
        }
        $request->validate([
            "transaction_id" => "required",
            "paid" => "nullable",
            "fre_type" => "required|in:1,2",
            "job_id" => "required",
            "fre_id" => "required_if:fre_type,1",
            "in_date" => "required",
            "rate" => "required",
            "paid_date" => "required_if:paid,1",
        ]);

        $paid = $request->input("paid") == 1 ? true : false;
        $fre_type = $request->input("fre_type");
        $job_id = $request->input("job_id");
        $fre_id = $request->input("fre_id");
        $in_date = parse_date_from_default_format($request->input("in_date"));
        $rate = $request->input("rate");
        $bonus = $request->input("bonus");
        $paid_date = parse_date_from_default_format($request->input("paid_date"));

        $transaction->job_id = $job_id;
        $transaction->freelancer_id = $fre_id;
        $transaction->freelancer_type = $fre_type;
        $transaction->job_date = $in_date->format("Y-m-d");
        $transaction->job_rate = $rate;
        $transaction->bonus = $bonus;
        $transaction->is_paid = $paid;
        $transaction->paid_date = $paid_date ? $paid_date->format("Y-m-d") : null;

        $transaction->save();
        return response([
            'success' => true,
            'message' => 'Transaction Updated Successfully.'
            ]);
    }
    
    public function DeleteTranactions($id)
    {
        $transaction = FinanceEmployer::query()->where("employer_id", Auth()->user()->id)->where("id", $id)->first();
        if (is_null($transaction)) {
            return response([
                'success' => false,
                'message' => 'No Transaction Found.'
                ]);
        }
        $transaction->delete();
        
        return response([
            'success' => true,
            'message' => "Transaction Deleted Successfully"
            ]);
        
    }
    
}
