<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(Request $request): InertiaResponse
    {
        return Inertia::render(component: 'Company/Index', props: [
            'title' => 'Companies',
            'filters' => $request->all(['search', 'field', 'order']),
        ]);
    }

    public function fetch(Request $request): JsonResponse
    {
        $_companies = Company::query();

        if( $request->has(key: 'search') ) {
            $_companies->whereRaw("CONCAT(name, ' ', email, ' ', address, ' ', phone) LIKE ?", ["%{$request->search}%"]);
        }

        if ($request->has('field') && $request->has('order')) {
            $_companies->orderBy($request->field, $request->order);
        }

        $companies = $_companies->withCount('entities')
            ->paginate(10, ['*'], 'page', $request->page ?? 1);

        return response()->json($companies);
    }
    
    public function getCompany(Request $request): JsonResponse
    {
        try {
            $company = Company::with(['entities'])
            ->findOrFail($request->id);

            return response()->json($company, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'getCompany ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getCompany City not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'getCompany QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getCompany Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'getCompany Exception: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getCompany Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function getCompanyByName(string $name): JsonResponse
    {
        try {
            $company = Company::with(['entities'])
            ->where('name', '=', $name)->firstOrFail();

            return response()->json($company, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'getCompanyByName ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getCompanyByName City not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'getCompanyByName QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getCompanyByName Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'getCompanyByName Exception: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getCompanyByName Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function storeCompany(StoreCompanyRequest $request): JsonResponse
    {
        try {

            $company = DB::transaction(function() use($request): Company {
                // 1. Entitás létrehozása
                $_company = City::create($request->all());

                // 2. Kapcsolódó rekordok létrehozása (pl. alapértelmezett beállítások)
                $this->createDefaultSettings($_company);

                // 3. Cache törlése, ha releváns

                return $_company;
            });

            return response()->json($company, Response::HTTP_CREATED);
        } catch( QueryException $ex ) {
            \Log::info('storeCity QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'storeCity Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('storeCity Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'storeCity Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function updateCompany(UpdateCompanyRequest $request, int $id): JsonResponse
    {
        try {

            $company = DB::transaction(function() use($request, $id): Company {
                $_company = Company::lockForUpdate()->findOrFail($id);
                $_company->update($request->all());
                $_company->refresh();

                // 3. Kapcsolódó rekordok frissítése (pl. alapértelmezett beállítások)
                $this->updateDefaultSettings($_company);

                // 4. Cache törlése, ha releváns

                return $_company;

            });

            return response()->json($company, Response::HTTP_CREATED);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('updateCompany ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'updateCompany City not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('updateCompany QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'updateCompany Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('updateCompany Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'updateCompany Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function deleteCompanies(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array|min:1', // Kötelező, legalább 1 id kell
                'ids.*' => 'integer|exists:companies,id', // Az id-k egész számok és létező cégek legyenek
            ]);

            $ids = $validated['ids'];

            $deletedCount = DB::transaction(function () use ($ids): int {
                // 1. Törlés - válaszd az egyik verziót:
                // a) Observer nélküli, gyors SQL törlés:
                $count = City::whereIn('id', $ids)->delete();

                // b) Observer-kompatibilis, egyenkénti törlés:
                //$_cities = City::whereIn('id', $ids)->lockForUpdate()->get();
                //$_cities->each(function (City $city) use (&$deletedCount) {
                //    if ($city->delete()) {
                //        $deletedCount++;
                //    }
                //});

                // Cache törlése, ha szükséges

                return $count;
            });

            return response()->json($deletedCount, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('deleteCompanies ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCompanies Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( QueryException $ex ) {
            \Log::info('deleteCompanies QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCompanies Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('deleteCompanies Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCompanies Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function deleteCompany(GetCompanyRequest $request): JsonResponse
    {
        try {

            $company = DB::transaction(function() use($request): Company {
                $_company = Company::lockForUpdate()->findOrFail($request->id);
                $_company->delete();

                // Cache törlése, ha szükséges

                return $_company;
            });

            return response()->json($company, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('deleteCompany ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCompany City not found'], Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info('deleteCompany QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCompany Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('deleteCompany Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCompany Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function restoreCompany(GetCompanyRequest $request): JsonResponse
    {
        try {

            $company = DB::transaction(function () use ($request): Company {
                // Soft-deleted ország lekérése
                $_company = Company::withTrashed()->findOrFail($request->id);

                // Visszaállítás
                $_company->restore();

                // Friss adat betöltése
                $_company->refresh();

                return $_company;
            });

            return response()->json($company, Response::HTTP_OK);

        } catch (ModelNotFoundException $ex) {
            \Log::error('restoreCompany ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreCompany City not found'], Response::HTTP_NOT_FOUND);
        } catch (QueryException $ex) {
            \Log::error('restoreCompany QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreCompany Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $ex) {
            \Log::error('restoreCompany Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreCompany Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function realDeleteCompany(GetCompanyRequest $request): JsonResponse
    {
        try {

            $company = DB::transaction(function()use($request): Company {
                // 1. Ország keresése
                $_company = Company::withTrashed()->lockForUpdate()->findOrFail($request->id);
                // 2. Ország véglegesen törlése
                $_company->forceDelete();

                return $_company;
            });

            return response()->json($company,  Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error('realDeleteCompany ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteCompany City not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('realDeleteCompany QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteCompany Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('realDeleteCompany Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteCompany Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    private function createDefaultSettings(City $city): void
    {
        //
    }

    private function updateDefaultSettings(City $city): void
    {
        //
    }
}
