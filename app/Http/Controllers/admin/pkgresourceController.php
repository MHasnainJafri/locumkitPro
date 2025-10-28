<?php

namespace App\Http\Controllers\admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;
use App\Models\UserAclPackage;
use App\Http\Controllers\Controller;
use App\Models\UserAclPackageResource;

use function PHPUnit\Framework\returnCallback;

class pkgresourceController extends Controller
{

    // protected $name = 'user_acl_package_resources';
    // resource_key must be uniqque

    public function index()
    {
        $packages = UserAclPackageResource::all();
        // dd($packages);
        return view('admin.packageResources.index', compact('packages'));
    }
    public function edit($id)
    {
        $resources = UserAclPackageResource::find($id);
        return view('admin.packageResources.edit', compact('resources'));
    }
    public function update(Request $request, $id)
    {
        // dd($request);
        // Find the UserAclPackage by ID
        $package = UserAclPackageResource::find($id);
        $validatedData = $request->validate([
        'resource_key' => [
            'required',
            'string',
            'min:3',
            'max:255',
            'regex:/^[A-Za-z_\s]+$/',
            Rule::unique('user_acl_package_resources')->ignore($id),
        ],
        'resource_value' => [
            'required',
            'string',
            'min:3',
            'max:100',
            'regex:/^[A-Za-z\s]+$/',
        ],
        'allow_count' => 'required|numeric|min:1|max:100',
    ], [
        'resource_key.required' => 'The Privilege Key field is required.',
        'resource_key.string' => 'The Privilege Key must be a string.',
        'resource_key.min' => 'The Privilege Key must be at least 3 characters.',
        'resource_key.max' => 'The Privilege Key may not be greater than 255 characters.',
        'resource_key.regex' => 'The Privilege Key must contain only letters and spaces.',
        'resource_key.unique' => 'This Privilege Key already exists.',

        'resource_value.required' => 'The Privilege field is required.',
        'resource_value.string' => 'The Privilege must be a string.',
        'resource_value.min' => 'The Privilege must be at least 3 characters.',
        'resource_value.max' => 'The Privilege may not be greater than 100 characters.',
        'resource_value.regex' => 'The Privilege must contain only letters and spaces.',

        'allow_count.required' => 'The Privilege value field is required.',
        'allow_count.numeric' => 'The Privilege value must be a number.',
        'allow_count.min' => 'Minimum one value is required.',
        'allow_count.max' => 'Not more than 100.',
    ]);
    
    $hasChanges = (
        $package->resource_key !== $validatedData['resource_key'] ||
        $package->resource_value !== $validatedData['resource_value'] ||
        $package->allow_count != $validatedData['allow_count']
    );

    if (!$hasChanges) {
        return back()->with('error', 'Please update at least one field before saving.');
    }
        $package->update($validatedData);


        return redirect()->route('admin.pkgresource.index')->with('success', 'Package updated successfully');;
    }

    public function store(Request $request)
    {


        // resource_key must be uniqid
        ///validation here

        $rules = [
            'resource_key' => [
        'required',
        'string',
        'min:3',
        'max:255',
        'regex:/^[A-Za-z_\s]+$/',
        Rule::unique('user_acl_package_resources'),
    ],
    'resource_value' => [
        'required',
        'string',
        'min:3',
        'max:100',
        'regex:/^[A-Za-z\s]+$/',
    ],
            'resource_allow_count' => 'required|numeric|min:1|max:100',
        ];


        $messages = [
    'resource_key.required' => 'The Privilege Key field is required.',
    'resource_key.string' => 'The Privilege Key must be a string.',
    'resource_key.min' => 'The Privilege Key must be at least 3 characters.',
    'resource_key.max' => 'The Privilege Key may not be greater than 255 characters.',
    'resource_key.regex' => 'The Privilege Key must contain only letters and spaces.',
    'resource_key.unique' => 'This Privilege Key already exists.',

    'resource_value.required' => 'The Privilege field is required.',
    'resource_value.string' => 'The Privilege must be a string.',
    'resource_value.min' => 'The Privilege must be at least 3 characters.',
    'resource_value.max' => 'The Privilege may not be greater than 100 characters.',
    'resource_value.regex' => 'The Privilege must contain only letters and spaces.',

    'resource_allow_count.required' => 'The Privilege value field is required.',
    'resource_allow_count.numeric' => 'The Privilege value must be a number.',
    'resource_allow_count.min' => 'Minimum one value is required.',
    'resource_allow_count.max' => 'Not more than 100.',
];


        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }



        UserAclPackageResource::create(array_merge($request->except(['user_acl_package_resources_ids_list', 'addNew']), ['user_acl_package_resources_ids_list' => json_encode($request->user_acl_package_resources_ids_list)]));
        
        if($request -> submit == 'Save & add new'){
            return redirect()->route('admin.pkgresource.create')->with('success', 'Package added successfully');        }
        else if($request -> submit == 'Save'){
            return redirect()->route('admin.pkgresource.index')->with('success', 'Package added successfully');        }

        return redirect()->back()->with('success', 'Package added successfully');
    }

    public function destroy($id)
    {
        $package = UserAclPackageResource::find($id);
        $package->delete();
        return redirect()->route('admin.pkgresource.index')->with('success','deleted successfully');
    }


    public function create(Request $request)
    {

        return view('admin.packageResources.create');
    }
}
