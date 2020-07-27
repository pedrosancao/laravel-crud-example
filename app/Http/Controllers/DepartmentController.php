<?php

namespace App\Http\Controllers;

use App\Department;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(Department::all());
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
            'slug'        => 'required|string|max:170|unique:departments',
            'description' => 'nullable|string|max:170',
        ]);

        $department = Department::create([
            'name'        => $request->get('name'),
            'slug'        => $request->get('slug'),
            'description' => $request->get('description'),
        ]);

        return response()->json($department);
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
                Rule:: unique('departments', 'slug')->ignore($request->get('id')),
            ],
            'description' => 'nullable|string|max:170',
        ]);

        $department = Department::findOrFail($request->get('id'));
        $department->name        = $request->get('name');
        $department->slug        = $request->get('slug');
        $department->description = $request->get('description');
        $department->save();

        return response()->json($department);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        try {
            $department->delete();
        } catch (QueryException $exception) {
            return response()->json([
                'errors' => 'This record is in use and cannot be deleted',
            ], 422);
        }

        return response()->json(['message' => 'Department deleted successfully']);
    }
}
