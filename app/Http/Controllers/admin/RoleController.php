<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\userAclPermisssion;
use App\Models\UserAclRole;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = UserAclRole::latest()->get();
        return view('admin.role.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = userAclPermisssion::all();

        $permissionGroups = [];

        foreach ($permissions as $permission) {
            $parts = explode('/', $permission->permission);
            $resource = $parts[0];
            $action = [isset($parts[1]) ? $parts[1] : null,$permission->id];

            if (!isset($permissionGroups[$resource])) {
                $permissionGroups[$resource] = [];
            }

            if ($action !== null) {
                $permissionGroups[$resource][] = $action;
            } else {
                // Handle single record permission like 'job', 'tax', etc.
                $permissionGroups[$resource][] = $resource;
            }
        }

         $permissions= $permissionGroups;

        // $permissionGroups now contains the desired structure

        // $permissionGroups now contains the desired structure
        return view('admin.role.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request->submit);
        $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'min:3',
            'max:30',
            'regex:/^[a-zA-Z\s]+$/'
        ],
        'description' => 'required|string|max:255',
        'permissions' => 'required|array',
    ], [
        'name.required' => 'The name field is required.',
        'description.required' => 'The description field is required.',
        'name.min' => 'Name must be at least 3 characters.',
        'name.max' => 'Name must not exceed 30 characters.',
        'name.regex' => 'Name must contain only letters and spaces.',
        'permissions.required' => 'At least one permission must be selected.',
    ]);
    
        $role = UserAclRole::create($request->only(['name', 'description']));
    
        if ($request->has('permissions') && is_array($request->permissions)) {
            $permissions = userAclPermisssion::whereIn('id', $request->permissions)->get();
            $role->permissions()->attach($permissions);
        }
        if($request->submit=="Save"){
                return redirect()->route('admin.roles.index')->with('success', 'Role created successfully');
    
        }
        return redirect()->route('admin.roles.create')->with('success', 'Role created successfully');
    
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {


        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role=UserAclRole::find($id);
        $permissions = userAclPermisssion::all();

        $permissionGroups = [];

        foreach ($permissions as $permission) {
            $parts = explode('/', $permission->permission);
            $resource = $parts[0];
            $action = [isset($parts[1]) ? $parts[1] : null,$permission->id];

            if (!isset($permissionGroups[$resource])) {
                $permissionGroups[$resource] = [];
            }

            if ($action !== null) {
                $permissionGroups[$resource][] = $action;
            } else {
                // Handle single record permission like 'job', 'tax', etc.
                $permissionGroups[$resource][] = $resource;
            }
        }

         $permissions= $permissionGroups;


         if ($role) {
             $rolePermissions = $role->permissions->pluck('id')->toArray();
         } else {
             $rolePermissions = [];
         }
        return view('admin.role.edit', compact('permissions','rolePermissions','role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
        $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'min:3',
            'max:30',
            'regex:/^[a-zA-Z\s]+$/'
        ],
        'description' => 'required|string|max:255',
        'permissions' => 'required|array',
    ], [
        'name.required' => 'The name field is required.',
        'description.required' => 'The description field is required.',
        'name.min' => 'Name must be at least 3 characters.',
        'name.max' => 'Name must not exceed 30 characters.',
        'name.regex' => 'Name must contain only letters and spaces.',
        'permissions.required' => 'At least one permission must be selected.',
    ]);
        // Find the role by ID
        $role = UserAclRole::find($id);

        if (!$role) {
            // Handle the case where the role is not found, e.g., show an error message or redirect
            return redirect()->route('roles.index')->with('error', 'Role not found');
        }

        // Update the role's attributes
        $role->name = $request->input('name');
        $role->description = $request->input('description');

        // Save the updated role
        $role->save();

        // Detach all existing permissions from the role
        $role->permissions()->detach();

        // Attach new permissions to the role
        if ($request->has('permissions') && is_array($request->permissions)) {
            $permissions = userAclPermisssion::whereIn('id', $request->permissions)->get();
            $role->permissions()->attach($permissions);
        }


        if($request->submit=="Save"){
            return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully');

    }
    return redirect()->route('admin.roles.create')->with('success', 'Role updated successfully');


        // Redirect to a success page or return a response
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = UserAclRole::findOrFail($id);
        
        if ($role->users()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete role. It is assigned to one or more users.');
        }
    
        $role->delete();
        
        return redirect()->back()->with('success', 'Role deleted successfully.');
    }
}
