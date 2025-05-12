<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\IndexUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create user', ['only' => ['create', 'store']]);
        $this->middleware('permission:read user', ['only' => ['index', 'show']]);
        $this->middleware('permission:update user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete user', ['only' => ['destroy', 'destroyBulk']]);
    }

    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('User/Index', [
            'title' => 'Users',
            'filters' => $request->all(['search', 'field', 'order']),
        ]);
    }

    public function fetch(IndexUserRequest $request): JsonResponse
    {
        $_users = User::query();

        if( $request->has(key: 'search') ) {
            $_users->whereRaw("CONCAT(name, ' ', email) LIKE ?", ["%{$request->search}%"]);
        }

        if ($request->has('field') && $request->has('order')) {
            $_users->orderBy($request->field, $request->order);
        }

        $users = $_users->paginate(10, ['*'], 'page', $request->page ?? 1);

        return response()->json($users);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*
    public function index(IndexUserRequest $request)
    {
        $users = User::query();
        if ($request->has('search')) {
            $users->where('name', 'LIKE', "%" . $request->search . "%");
            $users->orWhere('email', 'LIKE', "%" . $request->search . "%");
        }
        if ($request->has(['field', 'order'])) {
            $users->orderBy($request->field, $request->order);
        }
        $role = auth()->user()->roles->pluck('name')[0];
        $roles = Role::get();
        if ($role != 'superadmin') {
            $users->whereHas('roles', function ($query) {
                return $query->where('name', '<>', 'superadmin');
            });
            $roles = Role::where('name', '<>', 'superadmin')->get();
        }

        return Inertia::render('User/Index', [
            'title'         => 'User',
            'filters'       => $request->all(['search', 'field', 'order']),
            'users'         => $users->with('roles')->paginate(10),
            'roles'         => $roles,
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole($request->role);
            DB::commit();
            return back()->with('success', $user->name. ' created successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Error creating ' . $user->name . $th->getMessage());
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => $request->password ? Hash::make($request->password) : $user->password,
            ]);
            $user->syncRoles($request->role);
            DB::commit();
            return back()->with('success', $user->name. ' updated successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Error updating ' . $user->name . $th->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return back()->with('success', $user->name. ' deleted successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Error deleting ' . $user->name . $th->getMessage());
        }
    }
*/
}