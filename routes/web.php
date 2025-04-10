<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Models\User;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PermissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});
/*
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'users'         => (int) User::count(),
        'roles'         => (int) Role::count(),
        'permissions'   => (int) Permission::count(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('/user', UserController::class)->except('create', 'show', 'edit');
    Route::post('/user/destroy-bulk', [UserController::class, 'destroyBulk'])->name('user.destroy-bulk');

    Route::resource('/role', RoleController::class)->except('create', 'show', 'edit');

    Route::resource('/permission', PermissionController::class)->except('create', 'show', 'edit');

    // =================================================
    // COMPANIES
    // =================================================
    Route::get(
        uri: '/companies',
        action: [App\Http\Controllers\CompanyController::class, 'index']
    )->name('companies.index');

    // =================================================
    // PERSONS
    // =================================================
    Route::get(
        uri: '/persons', 
        action: [App\Http\Controllers\PersonController::class, 'index']
    )->name(name: 'persons.index');

    // =================================================
    // ENTITIES
    // =================================================
    Route::get(
        uri: '/entities', 
        action: [App\Http\Controllers\EntityController::class, 'index']
    )->name(name: 'entities.index');

    // =================================================
    // COUNTRIES
    // =================================================
    Route::get(
        uri: '/countries', 
        action: [App\Http\Controllers\Geo\CountryController::class, 'index']
    )->name(name: 'countries.index');

    // =================================================
    // REGIONS
    // =================================================
    Route::get(
        uri: '/regions', 
        action: [App\Http\Controllers\Geo\RegionController::class, 'index']
    )->name(name: 'countries.index');

    // =================================================
    // CITIES
    // =================================================
    Route::get(
        uri: '/cities', 
        action: [App\Http\Controllers\Geo\CityController::class, 'index']
    )->name(name: 'countries.index');

    // =================================================
    // SUBDOMAIN STATES
    // =================================================
    Route::get('/subdomain_states', [\App\Http\Controllers\SubdomainStateController::class, 'index'])->name('subdomain_states.index');
    
    // =================================================
    // SUBDOMAINS
    // =================================================
    Route::get('/subdomains', [\App\Http\Controllers\SubdomainController::class, 'index'])->name('subdomains.index');
});

Route::get('/form', function () {
    return Inertia::render('SakaiForm');
});

Route::get('/button', function () {
    return Inertia::render('SakaiButton');
});

Route::get('/list', function () {
    return Inertia::render('SakaiList');
});

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';
