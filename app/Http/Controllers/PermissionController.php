<?php

namespace App\Http\Controllers;

use App\Permission;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(Permission::all());
    }

    /**
     * Load options for resource selects
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function options()
    {
        return response()->json([]);
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
            'name'        => 'required|string|max:170',
            'slug'        => 'required|string|max:170|unique:permissions',
            'description' => 'nullable|string|max:170',
        ]);

        $permission = Permission::create([
            'name'        => $request->get('name'),
            'slug'        => $request->get('slug'),
            'description' => $request->get('description'),
        ]);

        return response()->json($permission);
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
                Rule:: unique('permissions', 'slug')->ignore($request->get('id')),
            ],
            'description' => 'nullable|string|max:170',
        ]);

        $permission = Permission::findOrFail($request->get('id'));
        $permission->name        = $request->get('name');
        $permission->slug        = $request->get('slug');
        $permission->description = $request->get('description');
        $permission->save();

        return response()->json($permission);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        try {
            $permission->delete();
        } catch (QueryException $exception) {
            return response()->json([
                'errors' => 'This record is in use and cannot be deleted',
            ], 422);
        }

        return response()->json(['message' => 'Permission deleted successfully']);
    }
}
