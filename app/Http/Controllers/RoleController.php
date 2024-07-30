<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\RoleResource;
use App\Models\Role;

class RoleController extends Controller
{
    public function createRole(Request $request)
    {
        $data = $request->validate([
            'role_name' => 'required|string|unique:roles,role_name',
        ]);

        $role = Role::create($data);
        $createdRole = Role::findOrFail($role->id);

        return $this->sendResponse(RoleResource::make($createdRole)
                ->response()
                ->getData(true),'Role created successfully');
    }

    public function getRoles()
    {
        $roles = Role::paginate(20);

        return $this->sendResponse(RoleResource::collection($roles)
                ->response()
                ->getData(true), 'Roles retrieved successfully');
    }

    public function getARole(Role $role)
    {

        return $this->sendResponse(RoleResource::make($role)
                ->response()
                ->getData(true), 'Role retrieved successfully');
    }

    public function updateRole(Request $request, Role $role)
    {
        $role->update($request->all());
        $updatedRole = Role::findOrFail($role->id);

        return $this->sendResponse(RoleResource::make($updatedRole)
                ->response()
                ->getData(true), 'Role updated successfully');
    }

    public function deleteRole(Role $role)
    {
        $role->delete();
        return $this->sendResponse([], 'Role deleted successfully');
    }
}
