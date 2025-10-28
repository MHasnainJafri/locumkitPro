<?php

namespace App\Http\Controllers\admin;
use Illuminate\Support\Facades\Validator;


use App\Http\Controllers\Controller;
use App\Models\UserAclPackage;
use App\Models\PkgPrivilegeInfo;
use App\Models\UserAclPackageResource;
use Illuminate\Http\Request;

class PackageController extends Controller
{

//  ### table name ------- user_acl_packages
    public function index()
    {
        $packages = UserAclPackage::latest()->get();
        return view('admin.package.index', compact('packages'));
    }
    // public function edit($id)
    // {
    //     $package = UserAclPackage::find($id);
    //     // dd($package);
    //     // $resources = UserAclPackageResource::all();
    //     if($package->name == 'Silver'){
    //         $resources = PkgPrivilegeInfo::select('id','label','silver')->get();
    //     }
        
    //     if($package->name == 'Bronze'){
    //         $resources = PkgPrivilegeInfo::select('id','label','bronze')->get();
    //     }
        
    //     if($package->name == 'Gold'){
    //         $resources = PkgPrivilegeInfo::select('id','label','gold')->get();
    //     }
        
    //     if($package->name == 'Free Subscription'){
    //         $resources = PkgPrivilegeInfo::select('id','label','gold')->get();
    //     }
    //     // dd($package,$resources);
    //     return view('admin.package.edit', compact('resources', 'package'));
    // }
    
    public function edit($id)
    {
        // Find the UserAclPackage by ID
        $package = UserAclPackage::findOrFail($id);
    $name = trim(strtolower($package->name));
        // Define the privilege column based on package name
        $privilegeColumn = match ($name) {
            'Silver' => 'silver',
            'Bronze' => 'bronze',
            'Gold', 'Free Subscription' => 'gold',
            default => null,
        };
//     $resources = PkgPrivilegeInfo::all();
// dd($resources);

        // Fetch resources only if a valid privilege column is found
        $resources = $privilegeColumn 
            ? PkgPrivilegeInfo::select('id', 'label', $privilegeColumn)->get() 
            : collect(); // Return empty collection if no matching column
   // dd($resources);
        // Return the view with data
        return view('admin.package.edit', compact('resources', 'package'));
    }


    public function update(Request $request, $id)
    {
        // Find the UserAclPackage by ID
        $package = UserAclPackage::find($id);
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric',
            'description' => 'required',
            //'user_acl_package_resources_ids_list' => 'required|array',
        ]);
        
        
        $package->update([
            'name' => $validatedData['name'],
            'price' => $validatedData['price'],
            'description' => $validatedData['description'],
            //'user_acl_package_resources_ids_list' => json_encode($validatedData['user_acl_package_resources_ids_list']),

        ]);
        
        // $selectedIds = $validatedData['user_acl_package_resources_ids_list']; 

        // Ensure $selectedIds is an array
        // if (!is_array($selectedIds)) {
        //     $selectedIds = [];
        // }
        
        if($request->name == 'Free Subscription'){
            return redirect()->route('admin.package.index');
        }
        
        // PkgPrivilegeInfo::whereIn('id', $selectedIds)->update([$request->name => 1]);
        // PkgPrivilegeInfo::whereNotIn('id', $selectedIds)->update([$request->name => 0]);

        return redirect()->route('admin.package.index')->with('success', 'Package updated successfully');
    }

    public function store(Request $request)
    {
         $rules = [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'user_acl_package_resources_ids_list' => 'required|array',
        ];

        $messages = [
            'name.required' => 'The name field is required.',
            'price.required' => 'The price field is required.',
            'price.min' => 'The price must be a positive number.',
            'user_acl_package_resources_ids_list.required' => 'At least one resource is required.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }



        UserAclPackage::create(array_merge($request->except(['user_acl_package_resources_ids_list', 'addNew']), ['user_acl_package_resources_ids_list' => json_encode($request->user_acl_package_resources_ids_list)]));

        if($request->submit=="Save"){
            return redirect()->route('admin.package.index')->with('success', 'Package created successfully');
        }
        return redirect()->route('admin.package.create')->with('success', 'Package added successfully');
    }

    public function destroy($id)
    {

        $package = UserAclPackage::find($id);
        $package->delete();
        return redirect()->route('admin.package.index')->with('success', 'Package deleted successfully');
    }
    public function  packageDestroy($id)
    {

        $package = UserAclPackage::find($id);
        $package->delete();
        return redirect()->route('admin.package.index')->with('success', 'Package deleted successfully');
    }


    public function create(Request $request)
    {
        $resources = UserAclPackageResource::all();

        return view('admin.package.create', compact('resources'));
    }
}
