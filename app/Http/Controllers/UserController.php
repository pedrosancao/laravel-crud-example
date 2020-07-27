<?php

namespace App\Http\Controllers;

use App\Department;
use App\Role;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::with('departments:department_id', 'roles:role_id')
            ->get()
            ->each(function (User $user) {
                // override relation to return only IDs
                $user->setRelation('departments', $user->departments->pluck('department_id'));
                $user->setRelation('roles', $user->roles->pluck('role_id'));
            });

        return response()->json($users);
    }

    /**
     * Load options for resource selects
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function options()
    {
        $departments = Department::get(['name', 'id']);
        $roles = Role::get(['name', 'id']);

        return response()->json(compact('departments', 'roles'));
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
            'name'          => 'required|string|max:170',
            'email'         => 'required|string|email|max:170|unique:users',
            'description'   => 'nullable|string|max:170',
            'password'      => 'required|string|min:8|confirmed',
            'departments'   => 'nullable|array',
            'departments.*' => 'required|integer|exists:departments,id',
        ]);

        $user = User::create([
            'name'        => $request->get('name'),
            'email'       => $request->get('email'),
            'description' => $request->get('description'),
            'password'    => Hash::make($request->get('password')),
        ]);

        $departments = $request->get('departments', []);
        $user->departments()->sync($departments);
        $user->departments = $departments;
        
        $roles = $request->get('roles', []);
        $user->roles()->sync($roles);
        $user->roles = $roles;

        return response()->json($user);
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
            'id'    => 'required|integer',
            'name'  => 'required|string|max:170',
            'email' => [
                'required',
                'string',
                'email',
                'max:170',
                Rule:: unique('users', 'email')->ignore($request->get('id')),
            ],
            'description'   => 'nullable|string|max:170',
            'password'      => 'nullable|string|min:8,confirmed',
            'departments'   => 'nullable|array',
            'departments.*' => 'required|integer|exists:departments,id',
            'roles'         => 'nullable|array',
            'roles.*'       => 'required|integer|exists:roles,id',
        ]);

        $user = User::findOrFail($request->get('id'));
        $user->name        = $request->get('name');
        $user->email       = $request->get('email');
        $user->description = $request->get('description');
        if ($request->has('password')) {
            $user->password = Hash::make($request->get('password'));
        }
        $user->save();

        $departments = $request->get('departments', []);
        $user->departments()->sync($departments);
        $user->departments = $departments;

        $roles = $request->get('roles', []);
        $user->roles()->sync($roles);
        $user->roles = $roles;

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        try {
            $user->departments()->sync([]);
            $user->roles()->sync([]);
            $user->delete();
        } catch (QueryException $exception) {
            return response()->json([
                'errors' => 'This record is in use and cannot be deleted',
            ], 422);
        }

        return response()->json(['message' => 'User deleted successfully']);
    }
}
