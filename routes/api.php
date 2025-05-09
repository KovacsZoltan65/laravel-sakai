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
    // COMPANIES
    // =================================================
    Route::get(
        uri: '/companies/fetch',
        action: [App\Http\Controllers\CompanyController::class, 'fetch']
    )->name(name: 'api.companies.fetch');
    Route::post(
        uri:'/companies',
        action: [App\Http\Controllers\CompanyController::class, 'create']
    )->name(name: 'api.companies.create');

    // =================================================
    // ENTITIES
    // =================================================
    Route::get(
        uri: '/entities/fetch',
        action: [App\Http\Controllers\EntityController::class, 'fetch']
    )->name(name: 'api.entities.fetch');
    Route::post(
        uri: '/entities',
        action: [App\Http\Controllers\EntityController::class, 'create']
    )->name(name: 'api.entities.create');

    // =================================================
    // PERSONS
    // =================================================
    Route::get(
        uri: '/persons/fetch',
        action: [App\Http\Controllers\PersonController::class, 'fetch']
    )->name(name: 'api.persons');
    Route::post(
        uri: '/persons',
        action: [App\Http\Controllers\PersonController::class, 'create']
    )->name(name: 'api.persons.create');

    // =================================================
    // COUNTRIES
    // =================================================
    Route::get(
        uri: '/countries/fetch',
        action: [App\Http\Controllers\Geo\CountryController::class, 'fetch']
    )->name(name: 'api.countries.index');

    // =================================================
    // REGIONS
    // =================================================
    Route::get(
        uri: '/regions/fetch',
        action: [App\Http\Controllers\Geo\RegionController::class, 'fetch']
    )->name(name:'api.regions.index');

    // =================================================
    // CITIES
    // =================================================
    Route::get(
        uri: '/cities/fetch',
        action: [App\Http\Controllers\Geo\CityController::class, 'fetch']
    )->name(name: 'api.cities.index');

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

    // =================================================
    // SHIFTS
    // =================================================
    Route::get('/shifts/fetch', [App\Http\Controllers\ShiftController::class, 'fetch'])->name('api.shift.fetch');

    /*
    Route::get('/companies', function(Request $reques){
        dd('route');
    });
    */
});