<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $roles = Role::with('permissions:permission_id')
            ->get()
            ->each(function (Role $role) {
                // override relation to return only IDs
                $role->setRelation('permissions', $role->permissions->pluck('permission_id'));
            });

        return response()->json($roles);
    }

    /**
     * Load options for resource selects
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function options()
    {
        $permissions = Permission::get(['name', 'id']);

        return response()->json(compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:170',
            'slug' => 'required|string|max:170|unique:roles',
            'description'  => 'nullable|string|max:170',
            'permisions'   => 'nullable|array',
            'permisions.*' => 'required|integer|exists:permisions,id',
        ]);

        $role = Role::create([
            'name'        => $request->get('name'),
            'slug'        => $request->get('slug'),
            'description' => $request->get('description'),
        ]);

        $permissions = $request->get('permissions', []);
        $role->permissions()->sync($permissions);
        $role->permissions = $permissions;

        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'id'   => 'required|integer',
            'name' => 'required|string|max:170',
            'slug' => [
                'required',
                'string',
                'max:170',
                Rule:: unique('roles', 'slug')->ignore($request->get('id')),
            ],
            'description'  => 'nullable|string|max:170',
            'permisions'   => 'nullable|array',
            'permisions.*' => 'required|integer|exists:permisions,id',
        ]);

        $role = Role::findOrFail($request->get('id'));
        $role->name = $request->get('name');
        $role->slug = $request->get('slug');
        $role->description = $request->get('description');
        $role->save();

        $permissions = $request->get('permissions', []);
        $role->permissions()->sync($permissions);
        $role->permissions = $permissions;

        return response()->json($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        try {
            $role->permissions()->sync([]);
            $role->delete();
        } catch (QueryException $exception) {
            return response()->json([
                'errors' => 'This record is in use and cannot be deleted',
            ], 422);
        }

        return response()->json(['message' => 'Role deleted successfully']);
    }
}
