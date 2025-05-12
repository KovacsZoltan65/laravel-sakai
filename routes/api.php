<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['web', 'auth'])->group(function(){
    // =================================================
    // USERS
    // =================================================
    Route::get(
        '/users/fetch', 
        [App\Http\Controllers\UserController::class, 'fetch']
    )->name('api.users.fetch');
    Route::post(
        '/users', 
        [App\Http\Controllers\UserController::class, 'create']
    )->name('api.users.create');
    // =================================================
    // COMPANIES
    // =================================================
    Route::get(
        '/companies/fetch',
        [App\Http\Controllers\CompanyController::class, 'fetch']
    )->name('api.companies.fetch');
    Route::post(
        '/companies',
        [App\Http\Controllers\CompanyController::class, 'create']
    )->name('api.companies.create');

    // =================================================
    // ENTITIES
    // =================================================
    Route::get(
        '/entities/fetch',
        [App\Http\Controllers\EntityController::class, 'fetch']
    )->name('api.entities.fetch');
    Route::post(
        '/entities',
        [App\Http\Controllers\EntityController::class, 'create']
    )->name('api.entities.create');

    // =================================================
    // PERSONS
    // =================================================
    Route::get(
        '/persons/fetch',
        [App\Http\Controllers\PersonController::class, 'fetch']
    )->name('api.persons');
    Route::post(
        '/persons',
        [App\Http\Controllers\PersonController::class, 'create']
    )->name('api.persons.create');

    // =================================================
    // COUNTRIES
    // =================================================
    Route::get(
        '/countries/fetch',
        [App\Http\Controllers\Geo\CountryController::class, 'fetch']
    )->name('api.countries.index');

    // =================================================
    // REGIONS
    // =================================================
    Route::get(
        '/regions/fetch',
        [App\Http\Controllers\Geo\RegionController::class, 'fetch']
    )->name('api.regions.index');

    // =================================================
    // CITIES
    // =================================================
    Route::get(
        '/cities/fetch',
        [App\Http\Controllers\Geo\CityController::class, 'fetch']
    )->name('api.cities.index');

    // =================================================
    // SUBDOMAIN STATES
    // =================================================
    Route::get('/subdomain_states/fetch', [App\Http\Controllers\Subdomains\SubdomainStateController::class, 'fetch'])->name('api.subdomain_states.fetch');

    // =================================================
    // SUBDOMAINS
    // =================================================
    Route::get('/subdomains/fetch', [App\Http\Controllers\Subdomains\SubdomainController::class, 'fetch'])->name('api.subdomains.fetch');

    // =================================================
    // CALENDARS
    // =================================================
    Route::get('/calendar/fetch', [App\Http\Controllers\Calendars\Calendar\CalendarController::class, 'fetch'])->name('api.calendar.fetch');

    /*
    Route::get('/companies', function(Request $reques){
        dd('route');
    });
    */
});