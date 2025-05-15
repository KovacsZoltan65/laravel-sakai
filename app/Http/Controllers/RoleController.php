<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
//use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {

        $this->middleware('permission:create role', ['only' => ['create', 'store']]);
        $this->middleware('permission:read role', ['only' => ['index', 'show']]);
        $this->middleware('permission:update role', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete role', ['only' => ['destroy', 'destroyBulk']]);
    }

    public function index(Request $request): InertiaResponse
    {
        $permissions = Permission::toSelect();

        $role = auth()->user()->roles->pluck('name')[0];

        if ($role != 'superadmin') {
            $permissions = Permission::whereNotIn('name', ['create permission', 'read permission', 'update permission', 'delete permission'])->latest();
        }

        return Inertia::render('Role/Index', [
            'title'   => 'Roles',
            'filters' => $request->all(['search', 'field', 'order']),
            'permissions' => $permissions,
        ]);
    }

    public function fetch(IndexRoleRequest $request) : JsonResponse
    {
        $_roles = Role::query();

        if( $request->has(key: 'search') ) {
            $_roles->whereRaw("CONCAT(name) LIKE ?", ["%{$request->search}%"]);
        }

        // A hitelesített felhasználó első szerepkör nevének lekérése
        $role = auth()->user()->roles->pluck('name')[0];
        
        // Kizárja a „szuperadmin” szerepkört, ha a felhasználó szerepköre nem „szuperadmin”
        if ($role !== 'superadmin') {
            $_roles->where('name', '<>', 'superadmin');
        }

        if ($request->has('field') && $request->has('order')) {
            $_roles->orderBy($request->field, $request->order);
        }

        $roles = $_roles->with('permissions')->paginate(10, ['*'], 'page', $request->page ?? 1);
        
        return response()->json($roles);
    }

    /*
    public function index(IndexRoleRequest $request): InertiaResponse
    {
        $roles = Role::query();
        if ($request->has('search')) {
            $roles->where('name', 'LIKE', "%" . $request->search . "%");
            $roles->orWhere('guard_name', 'LIKE', "%" . $request->search . "%");
        }
        if ($request->has(['field', 'order'])) {
            $roles->orderBy($request->field, $request->order);
        }
        $roles->with('permissions');
        $role = auth()->user()->roles->pluck('name')[0];
        $permissions = Permission::latest();
        if ($role != 'superadmin') {
            $permissions = Permission::whereNotIn('name', ['create permission', 'read permission', 'update permission', 'delete permission'])->latest();
            $roles->where('name', '<>', 'superadmin');
        }
        return Inertia::render('Role/Index', [
            'title'         => "Role",
            'filters'       => $request->all(['search', 'field', 'order']),
            'roles'         => $roles->paginate(10),
            'permissions'   => $permissions->get(),
        ]);
    }

    public function store(StoreRoleRequest $request)
    {
        DB::beginTransaction();
        try {
            $role = Role::create([
                'name'          => $request->name,
            ]);
            $role->givePermissionTo($request->permissions);
            DB::commit();
            return back()->with('success', $role->name. ' created successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Error creating ' .  $th->getMessage());
        }
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        DB::beginTransaction();
        try {
            $role->update([
                'name'          => $request->name,
            ]);
            $role->syncPermissions($request->permissions);
            DB::commit();
            return back()->with('success', $role->name. ' updated successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Error updating ' .  $th->getMessage());
        }
    }

    public function destroy(Role $role)
    {
        try {
            $role->delete();
            return back()->with('success', $role->name. ' deleted successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Error deleting ' . $role->name . $th->getMessage());
        }
    }
    */
}
