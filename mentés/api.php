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


/*
// COMPANIES
//Route::get(uri: '/companies', action: [App\Http\Controllers\CompanyController::class, 'getCompanies'])->name('api.companies');
Route::get('/companies', function(Request $reques){
    dd('route');
});
*/

// ENTITIES
Route::get(
    uri: '/entities', 
    action: [App\Http\Controllers\EntityController::class, 'getEntities']
)->name(name: 'api.entities');

// PERSONS
Route::get(
    uri: '/persons',
    action: [App\Http\Controllers\PersonController::class, 'getPersons']
)->name(name: 'api.persons');


