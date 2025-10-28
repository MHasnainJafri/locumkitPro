<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\UserAclProfession;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Calculation\Category;
use App\Models\User;
use App\Models\FinancialYear;
use App\Models\Invoice;
use App\Models\FinanceIncome;
use App\Models\JobCancelation;
use App\Models\JobFeedback;
use App\Models\LastLoginUser;
use App\Models\LocumlogbookFollowupProcedure;
use App\Models\LocumlogbookPracticeInfo;
use App\Models\LocumlogbookReferralPathways;
use App\Models\UserExtraInfo;
use App\Models\UserPackageDetail;
use App\Models\UserPaymentInfo;
use Illuminate\Validation\Rule;



class CategoryController extends Controller
{
    public function index()
    {
        $categories = UserAclProfession::latest()->get();
        return view('admin.category.index', compact('categories'));
    }
    public function edit($id)
{
    $category = UserAclProfession::find($id);
    return response()
        ->view('admin.category.edit', compact('category'))
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
}

    public function create()
    {
        return view('admin.category.create');
    }
    public function categoryUpdate(Request $request, $id)
    {
        $validatedData = $request->validate([
    'name' => [
        'required',
        Rule::unique('user_acl_professions', 'name')->ignore($id),
    ],
    'status' => 'required',
    'description' => [
        'required',
        'regex:/^[A-Za-z0-9\s.,?\-]+$/'
    ],
]);

        $userAclProfession = UserAclProfession::find($id);
        $userAclProfession->update([
            'name' => $validatedData['name'],
            'is_active' => $validatedData['status'],
            'description' => $validatedData['description'],

        ]);
        if($request->submit == 'Save'){
            return redirect()->route('admin.category.index')->with('success', 'Data has been updated');
        }
        elseif($request->submit == 'Save & add new'){
            return redirect()->route('admin.category.create')->with('Success', 'Data has been updated');
        }
    }
    public function categoryCreate(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:user_acl_professions,name',
            'status' => 'required',
            'description' => [
    'required',
    'regex:/^[A-Za-z0-9\s.,?-]+$/'
],
        ]);
        UserAclProfession::create([
            'name' => $validatedData['name'],
            'is_active' => $validatedData['status'],
            'description' => $validatedData['description'],
        ]);
        if($request->submit == 'Save'){
            return redirect()->route('admin.category.index')->with('success', 'Data has been Added Successfully.');
        }
        elseif($request->submit == 'Save & add new'){
            return redirect()->route('admin.category.create')->with('Success', 'Data has been Added Successfully.');
        }
        return redirect()->back()->with('success','category created successfully');
    }

    public function destroy($id) {
    $category = UserAclProfession::find($id);

    if (!$category) {
        return redirect()->route('admin.category.index')->with('error', 'Category not found.');
    }

    $userIds = User::where('user_acl_profession_id', $id)->pluck('id');

    // 1. Delete related financial years:
    FinancialYear::whereIn('user_id', $userIds)->delete();

    // 2. Delete related invoices and their associated financial incomes:
    $invoiceIds = Invoice::whereIn('user_id', $userIds)->pluck('id'); // Get invoice IDs
    FinanceIncome::whereIn('invoice_id', $invoiceIds)->delete(); // Use invoice IDs
    Invoice::whereIn('user_id', $userIds)->delete();
    JobCancelation::whereIn('user_id', $userIds)->delete();
    JobFeedback::whereIn('freelancer_id', $userIds)->delete();
    LastLoginUser::whereIn('user_id', $userIds)->delete();
    LocumlogbookFollowupProcedure::whereIn('user_id', $userIds)->delete();
    LocumlogbookPracticeInfo::whereIn('user_id', $userIds)->delete();
    LocumlogbookReferralPathways::whereIn('user_id', $userIds)->delete();
    UserExtraInfo::whereIn('user_id', $userIds)->delete();
    UserPackageDetail::whereIn('user_id', $userIds)->delete();
    UserPaymentInfo::whereIn('user_id', $userIds)->delete();
    // 3. Delete the users:
    User::where('user_acl_profession_id', $id)->delete();

    // 4. Delete the category:
    $category->delete();

    return redirect()->route('admin.category.index')->with('success', 'Category and associated data deleted successfully.');
}
    
    public function toggleStatus($id)
    {
       
        $category = UserAclProfession::find($id);
        
        if ($category) {
            $category->is_active = !$category->is_active;
            $category->save();
        }

        return redirect()->back()->with('success', 'Category status updated successfully!');
    }
}
