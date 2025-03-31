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

// COMPANIES
//Route::get(uri: '/companies', action: [App\Http\Controllers\CompanyController::class, 'getCompanies'])->name('api.companies');

// PERSONS
Route::get(
    uri: '/persons',
    action: [App\Http\Controllers\PersonController::class, 'getPersons']
)->name(name: 'api.persons');

// web.php vagy api.php
Route::get(
    uri: '/entities/fetch',
    action: [App\Http\Controllers\EntityController::class, 'fetch']
)->name(name: 'api.entities.fetch');
Route::post(
    uri: '/entities',
    action: [App\Http\Controllers\EntityController::class, 'create']
)->name(name: 'api.entities.create');


/*
Route::get('/companies', function(Request $reques){
    dd('route');
});
*/
