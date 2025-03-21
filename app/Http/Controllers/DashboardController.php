<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    public function index(): InertiaResponse
    {
        return Inertia::render(component: 'Dashboard', props: [
            'title' => 'Dashboard',
            'users' => User::count(),
            'roles' => Role::count(),
            'permissions' => Permission::count(),
            'companies' => Company::count(),
        ]);
    }
}
