<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\FinanceEmployerResource;
use App\Http\Resources\Api\FinanceExpenseResource;
use App\Http\Resources\Api\FinanceIncomeResource;
use App\Http\Resources\Api\FinanceTransactionResource;
use App\Http\Resources\Api\SupplierResource;
use App\Models\FinanceEmployer;
use App\Models\FinanceExpense;
use App\Models\FinanceIncome;
use App\Models\JobPost;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TransactionsController extends Controller
{
    public function manageTransactions(Request $request)
    {
        $operation_type = $request->input("page_id");
        $formInfo = $request->input("formInfo");
        $response_data = array();
        switch ($operation_type) {
            case "getJobbyId":
                $job_id = $formInfo['job_id'];
                $response_data = $this->get_job_details_by_id($job_id);
                break;
            case "update-transaction":
                $trans_id     = $request->input('trans_id');
                $trans_date = $request->input('trans_date');
                $trans_type = $request->input('trans_type');
                $parsed_date = parse_date_from_default_format(str_replace("/", "-", $trans_date));
                if ($parsed_date) {
                    $parsed_date = $parsed_date->format('Y-m-d');
                } else {
                    $parsed_date = today()->format('Y-m-d');
                }
                $this->updateTransaction($trans_type, $trans_id, $parsed_date);
                break;

            case "insert-income":
                
                $tran_id = $request->input('tran_id');
                $trans_type = $request->input('trans_type');
                $response_data = $this->insertTransaction($formInfo, $trans_type, $tran_id);
                break;

            case "insert-cost":
                $tran_id = $request->input('tran_id');
                $trans_type = $request->input('trans_type');
                $response_data = $this->insertCostTransaction($formInfo, $tran_id);
                break;

            case "edit-transaction":
                $trans_id     = $request->input('tra_id');
                $trans_type = $request->input('tra_type');
                $user_type     = $request->input('user_type');
                $user_id     = $request->input('user_id');
                $response_data = $this->getTransactionDetails($trans_id, $trans_type, $user_id, $user_type);
                break;
            case "delete-transaction":
                $trans_id     = $request->input('trans_id');
                $trans_type = $request->input('trans_type');
                $user_id     = $request->input('user_id');
                $response_data = $this->deleteTransaction($trans_id, $trans_type, $user_id);
                break;
        }

        return response(json_encode($response_data));
    }

    private function get_job_details_by_id($job_id)
    {
        $job = JobPost::find($job_id);
        if ($job) {
            $user = $job->employer;
            $user["store_details"] = $job->job_store->store_name;
            $user["job_date"] = get_date_with_default_format($job->job_date);
            $user["job_rate"] = $job->job_rate;
            $user["locations"] = $job->job_address;
            return $user;
        }
        return [];
    }

    private function updateTransaction($trans_type, $trans_id, $parsed_date)
    {
        if ($trans_type == 1 || $trans_type == 'Income') {
            $income = FinanceIncome::where("id", $trans_id)->first();
            if ($income) {
                $income->is_bank_transaction_completed = true;
                $income->bank_transaction_date = $parsed_date;
                $income->save();
            }
        }
        if ($trans_type == 2 || $trans_type == 'Expense') {
            $expense = FinanceExpense::where("id", $trans_id)->first();
            if ($expense) {
                $expense->is_bank_transaction_completed = true;
                $expense->bank_transaction_date = $parsed_date;
                $expense->save();
            }
        }
    }

    private function insertTransaction($income_data, $trans_type, $trans_id = null)
    {
        if ($trans_type == 1 || $trans_type == 'Income') {
            $income_array = array(
                'job_id'     =>     trim($income_data['in_jobno']),
                'job_type'     =>     $income_data['in_job_type'],
                'freelancer_id'    =>     $income_data['uid'],
                'employer_id'     =>  $income_data['in_emp_id'],
                'job_rate'     =>  trim($income_data['in_rate']),
                'job_date'     =>     $income_data['in_date'] ? date('Y-m-d', strtotime(str_replace('/', '-', $income_data['in_date'])))  : null,
                'income_type'     => trim($income_data['in_category']),
                'is_bank_transaction_completed' => isset($income_data['in_bank']) && $income_data['in_bank'] ? true : false,
                'bank_transaction_date'     =>  isset($income_data['in_bankdate']) && $income_data['in_bankdate'] ? date('Y-m-d', strtotime(str_replace('/', '-', $income_data['in_bankdate']))) : null,
                'store'     =>     trim(isset($income_data['in_store']) ? $income_data['in_store'] : ""),
                'location'     =>     trim(isset($income_data['in_location']) ? $income_data['in_location'] : ""),
                'supplier'     =>     trim(isset($income_data['in_supplier']) ? $income_data['in_supplier'] : ""),
                'status'     =>  1,
            );

            if ($trans_id) {
                $income = FinanceIncome::find($trans_id);
                if ($income) {
                    $income->update($income_array);
                }
            } else {
                $income = FinanceIncome::create($income_array);
            }
            return [
                "success" => true,
                "message" => "Income Added Successfully",
                "income_id" => $income->id,
                ];
        } elseif ($trans_type == 2 || $trans_type == 'Expense') {
            $expense_array = array(
                'job_id'             => trim(isset($income_data['in_jobno']) ? $income_data['in_jobno'] : ""),
                'job_type'             => $income_data['in_job_type'],
                'freelancer_id'        =>  $income_data['uid'],
                'job_rate'                 =>  trim(isset($income_data['in_rate']) ? $income_data['in_rate'] : ""),
                'job_date'             =>     isset($income_data['in_date']) && $income_data['in_date'] ? date('Y-m-d', strtotime(str_replace('/', '-', $income_data['in_date'])))  : null,
                'expense_type_id'     => trim(isset($income_data['in_category']) ? $income_data['in_category'] : ""),
                'is_bank_transaction_completed' => isset($income_data['in_bank']) && $income_data['in_bank'] ? true : false,
                'bank_transaction_date'         =>  isset($income_data['in_bankdate']) && $income_data['in_bankdate'] ? date('Y-m-d', strtotime(str_replace('/', '-', $income_data['in_bankdate']))) : null,
                'description'         =>     trim(isset($income_data['in_description']) ? $income_data['in_description'] : "")
            );
            if (isset($income_data['in_receipt']) && $income_data['in_receipt']) {
                $base64Receipt = $income_data['in_receipt'];
                @list($type, $file_data) = explode(';', $base64Receipt);
                @list(, $file_data) = explode(',', $file_data);
                @list(, $type) = explode('/', $type);

                $fileName = "receipt-" . time() . "-" . $type;
                $filePath = public_path("/media/receipt");
                file_put_contents($filePath . "/" . $fileName, base64_decode($file_data));
                $expense_array["receipt"] = "/media/receipt/" . $fileName;
            }

            if ($trans_id) {
                $expense = FinanceExpense::find($trans_id);
                if ($expense) {
                    $expense->update($expense_array);
                }
            } else {
                $expense = FinanceExpense::create($expense_array);
            }
            return $expense;
        }
        return null;
    }

    private function insertCostTransaction($user_data, $tran_id = null)
    {
        $employer_finance_record = array(
            'job_id'     =>     isset($user_data['in_jobno']) ? $user_data['in_jobno'] : 0,
            'employer_id'     =>     $user_data['uid'],
            'freelancer_id'    =>  isset($user_data['in_locum']) ? trim($user_data['in_locum']) : 0,
            'freelancer_type'     =>  isset($user_data['locum_type']) ? $user_data['locum_type'] : "",
            'job_date'     =>  $user_data['in_date'] ? date('Y-m-d', strtotime(str_replace('/', '-', $user_data['in_date']))) : null,
            'job_rate'     =>  trim(isset($user_data['in_rate']) ? $user_data['in_rate'] : 0),
            'bonus'     =>     isset($user_data['in_bonus']) ? trim($user_data['in_bonus']) : null,
            'is_paid'         => (isset($user_data['in_paid']) && $user_data['in_paid'] == 1) ? true : false,
            'paid_date' =>  isset($user_data['in_paiddate']) ? date('Y-m-d', strtotime(str_replace('/', '-', $user_data['in_paiddate']))) : null,
        );

        if ($tran_id) {
            $finance_record = FinanceEmployer::find($tran_id);
            if ($finance_record) {
                $finance_record->update($employer_finance_record);
            }
        } else {
            FinanceEmployer::create($employer_finance_record);
        }
        return "1";
    }

    private function getTransactionDetails($trans_id, $trans_type, $user_id = null, $user_type = null): array|string|null
    {
        $trans_records = "";
        if ($user_type == 2) {
            if ($trans_type == 1) {
                $finance_income = FinanceIncome::find($trans_id);
                if ($finance_income) {
                    $trans_records = (new FinanceTransactionResource($finance_income))->jsonSerialize();
                }
            } elseif ($trans_type == 2) {
                $finance_expense = FinanceExpense::find($trans_id);
                if ($finance_expense) {
                    $trans_records = (new FinanceTransactionResource($finance_expense))->jsonSerialize();
                }
            }
        } elseif ($user_type == 3) {
            $finance_expense = FinanceEmployer::find($trans_id);
            if ($finance_expense) {
                $trans_records = (new FinanceEmployerResource($finance_expense))->jsonSerialize();
            }
        }

        return $trans_records;
    }

    private function deleteTransaction($trans_id, $trans_type, $user_id): int
    {
        $delete_status     = 0;
        if ($trans_type == 1) {
            $delete_status = FinanceIncome::where("freelancer_id", $user_id)->where("id", $trans_id)->delete();
        } elseif ($trans_type == 2) {
            $delete_status = FinanceExpense::where("freelancer_id", $user_id)->where("id", $trans_id)->delete();
        } elseif ($trans_type == 3) {
            $delete_status = FinanceEmployer::where("employer_id", $user_id)->where("id", $trans_id)->delete();
        }
        return strval($delete_status);
    }

    public function insertSupplier(Request $request)
    {
        $user_id = $request->user()->id;
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', Rule::unique('suppliers', 'email')],
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->error("Wrong inputs are given", 400, $validator->messages()->toArray());
        }

        $name = $request->input('name', '');
        $store_name = $request->input('store_name', '');
        $address = $request->input('address', '');
        $second_address = $request->input('second_address', '');
        $town = $request->input('town', '');
        $county = $request->input('county', '');
        $postcode = $request->input('postcode', '');
        $email = $request->input('email', '');
        $contact_no = $request->input('contact_no', '');

        $supplier = Supplier::create([
            "name" => $name,
            "store_name" => $store_name,
            "address" => $address,
            "second_address" => $second_address,
            "town" => $town,
            "country" => $county,
            "postcode" => $postcode,
            "email" => $email,
            "contact_no" => $contact_no,
            "created_by_user_id" => $user_id
        ]);

        return response()->success((new SupplierResource($supplier))->jsonSerialize(), 'Supplier created successfully');
    }

    public function updateSupplier(Request $request)
    {
        $supplier_id = $request->input('id');
        $user_id = $request->user()->id;
        $supplier = Supplier::where("created_by_user_id", $user_id)->where("id", $supplier_id)->firstOrFail();
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', Rule::unique('suppliers', 'email')],
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->error("Wrong inputs are given", 400, $validator->messages()->toArray());
        }
        $supplier->update([
            "name" => $request->input('name', ''),
            "store_name" => $request->input('store_name', ''),
            "address" => $request->input('address', ''),
            "second_address" => $request->input('second_address', ''),
            "town" => $request->input('town', ''),
            "country" => $request->input('county', ''),
            "postcode" => $request->input('postcode', ''),
            "email" => $request->input('email', ''),
            "contact_no" => $request->input('contact_no', ''),
        ]);

        return response()->success([], 'Supplier updated successfully');
    }

    public function deleteSupplier(Request $request)
    {
        $user_id = $request->user()->id;
        $supplier_id = $request->input("supplier_id");
        Supplier::where("id", $supplier_id)->where("created_by_user_id", $user_id)->delete();
        return response()->success([], 'Supplier deleted successfully');
    }

    public function allSupplier(Request $request)
    {
        $user_id = $request->user()->id;
        $records = Supplier::where("created_by_user_id", $user_id)->get();
        return response()->success(SupplierResource::collection($records)->jsonSerialize());
    }
    public function getSupplierById(Request $request)
    {
        $user_id = $request->user()->id;
        $supplier_id = $request->input("supplier_id");
        $record = Supplier::where("id", $supplier_id)->where("created_by_user_id", $user_id)->firstOrFail();
        return response()->success((new SupplierResource($record))->jsonSerialize());
    }
}
