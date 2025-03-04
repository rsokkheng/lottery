<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $data = Role::orderBy('id','DESC')->get();
        return view('admin.role.index', compact('data'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy('group');
        return view('admin.role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|unique:roles|max:255', // Name must be unique for new roles
            'permissions' => 'array', // Ensure 'permissions' is an array
            'permissions.*' => 'exists:permissions,id', // Ensure each permission ID exists in the 'permissions' table
        ]);
    
        // Create a new role
        $role = Role::create([
            'name' => $request->name,
        ]);
    
        // If there are permissions, sync them
        if ($request->has('permissions') && is_array($request->permissions)) {
            $permissions = array_map('intval', $request->permissions); // Convert permissions to integers
    
            // Sync the permissions with the role
            if (count($permissions) > 0) {
                $role->syncPermissions($permissions);
            }
        }
    
        // Return a success message
        return redirect()->route('admin.role.index')->with('success', 'Role created successfully.');
    }
    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|max:255|unique:roles,name,' . $id, // Allow the current role name for updating
            'permissions' => 'array', // Ensure 'permissions' is an array
            'permissions.*' => 'exists:permissions,id', // Ensure each permission ID exists in the 'permissions' table
        ]);

        // Find the existing role by ID
        $role = Role::findOrFail($id);

        // Update the role name
        $role->update([
            'name' => $request->name,
        ]);

        // If there are permissions, sync them
        if ($request->has('permissions') && is_array($request->permissions)) {
            $permissions = array_map('intval', $request->permissions); // Convert permissions to integers

            // Sync the permissions with the role
            if (count($permissions) > 0) {
                $role->syncPermissions($permissions);
            }
        }

        // Return a success message
        return redirect()->route('admin.role.index')->with('success', 'Role updated successfully.');
    }


    public function edit($id)
    {
        $data = Role::where('id',decrypt($id))->first();
        $permissions = Permission::all()->groupBy('group'); // Group by the 'group' column
        return view('admin.role.edit',compact('data','permissions'));
    }

    public function destroy($id)
    {
        Role::where('id',decrypt($id))->delete();
        return redirect()->route('admin.role.index')->with('error','Role deleted successfully.');
    }
}
