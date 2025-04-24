<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response AS InertiaResponse;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Requests\IndexPermissionRequest;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;

class PermissionController extends Controller
{
    public function __construct()
    {
        /*
         | read permission,
         | create permission,
         | update permission
         | delete permission
         */
        $this->middleware('permission:read permission', ['only' => ['index', 'show', 'fetch', 'getPermission', 'getPermissionByName']]);
        $this->middleware('permission:create permission', ['only' => ['storePermission']]);
        $this->middleware('permission:update permission', ['only' => ['updatePermission', 'restorePermission']]);
        $this->middleware('permission:delete permission', ['only' => ['deletePermissions', 'deletePermission', 'realDeletePermission']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexPermissionRequest $request): InertiaResponse
    {
        $permissions = Permission::query();
        if ($request->has('search')) {
            $permissions->where('name', 'LIKE', "%" . $request->search . "%");
            $permissions->orWhere('guard_name', 'LIKE', "%" . $request->search . "%");
        }

        if ($request->has(['field', 'order'])) {
            $permissions->orderBy($request->field, $request->order);
        }

        $params = [
            'title'         => 'Permission',
            'filters'       => $request->all(['search', 'field', 'order']),
            'permissions'   => $permissions->paginate(10),
        ];

        return Inertia::render('Permission/Index', $params);
    }

    public function store(StorePermissionRequest $request)
    {
        DB::beginTransaction();
        try {
            $permission = Permission::create([
                'name'          => $request->name
            ]);
            $superadmin = Role::whereName('superadmin')->first();
            $superadmin->givePermissionTo([$request->name]);
            DB::commit();
            return back()->with('success', $permission->name. ' created successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Error creating ' .  $th->getMessage());
        }
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        DB::beginTransaction();
        try {
            $superadmin = Role::whereName('superadmin')->first();
            $superadmin->revokePermissionTo([$permission->name]);
            $permission->update([
                'name'          => $request->name
            ]);
            $superadmin->givePermissionTo([$permission->name]);
            DB::commit();
            return back()->with('success',  $permission->name. ' updated successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Error updating ' .  $th->getMessage());
        }
    }

    public function destroy(Permission $permission)
    {
        DB::beginTransaction();
        try {
            $superadmin = Role::whereName('superadmin')->first();
            $superadmin->revokePermissionTo([$permission->name]);
            $permission->delete();
            DB::commit();
            return back()->with('success', $permission->name. ' deleted successfully.');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Error deleting ' . $permission->name . $th->getMessage());
        }
    }
}
