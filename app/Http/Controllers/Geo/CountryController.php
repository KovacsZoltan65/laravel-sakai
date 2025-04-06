<?php

namespace App\Http\Controllers\Geo;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountryIndexRequest;
use App\Http\Requests\GetCountryRequest;
use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Models\Geo\City;
use App\Models\Geo\Country;
use App\Models\Geo\Region;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

use Inertia\Inertia;
use Inertia\Response AS InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class CountryController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(CountryIndexRequest $request): InertiaResponse
    {
        $cities = City::active()->select(columns: ['name', 'id'])->get();
        $regions = Region::active()->select(columns: ['id', 'name'])->get();

        return Inertia::render(component: 'Geo/Country/Index', props: [
            'title' => 'Country',
            'filters' => $request->all(keys: ['search', 'field', 'order']),
            'regions' => $regions,
            'cities' => $cities
        ]);
    }

    public function fetch(Request $request): JsonResponse
    {
        $_countries = Country::query();

        if( $request->has(key: 'search') ) {
            $_countries->whereRaw(
                sql: "CONCAT(name, ' ', email, ' ', address, ' ', phone) LIKE ?",
                bindings: ["%{$request->search}%"]
            );
        }

        if ($request->has(key: 'field') && $request->has(key: 'order')) {
            $_countries->orderBy(column: $request->field, direction: $request->order);
        }

        $countries = $_countries->with(relations: ['regions', 'cities'])
            ->withCount(relations: ['regions', 'cities'])
            ->paginate(
                perPage: 10,
                columns: ['*'],
                pageName:'page',
                page: $request->page ?? 1
            );

        return response()->json($countries);
    }

    public function getCountry(GetCountryRequest $request): JsonResponse
    {
        try {
            $country = Country::with(relations: ['cities', 'regions'])
                ->withCount(relations: ['cities', 'regions'])
                ->findOrFail(id: $request->id);

            return response()->json(data: $country, status: Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error(message: 'getCountry ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(data: ['error' => 'getCountry Country not found'], status: Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error(message: 'getCountry QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(data: ['error' => 'getCountry Database error'], status: Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error(message: 'getCountry Exception: ' . print_r(value: $ex, return: true));
            return response()->json(data: ['error' => 'getCountry Internal server error'], status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getCountryByName(string $name): JsonResponse
    {
        try {
            $countries = Country::where(column: 'name', operator: '=', value: $name)->firstOrFail();

            return response()->json(data: $countries, status: Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error(message: 'getCountryByName ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(data: ['error' => 'getCountryByName Country not found'], status: Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error(message: 'getCountryByName QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(data: ['error' => 'getCountryByName Database error'], status: Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error(message: 'getCountryByName Exception: ' . print_r(value: $ex, return: true));
            return response()->json(data: ['error' => 'getCountryByName Internal server error'], status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeCountry(StoreCountryRequest $request): JsonResponse
    {
        try {
            $country = null;

            DB::transaction(callback: function() use($request, &$country) {
                // 1. Ország létrehozása
                $country = Country::create(attributes: $request->all());

                // 2. Kapcsolódó rekordok létrehozása (pl. alapértelmezett beállítások)
                $this->createDefaultSettings(country: $country);

                // 3. Cache törlése, ha releváns

            });

            return response()->json(data: $country, status: Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error(message: 'storeCountry ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(data: ['error' => 'storeCountry Country not found'], status: Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error(message: 'storeCountry QueryException: ' . print_r($ex, return: true));
            return response()->json(data: ['error' => 'storeCountry Database error'], status: Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error(message: 'storeCountry Exception: ' . print_r($ex, return: true));
            return response()->json(data: ['error' => 'storeCountry Internal server error'], status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateCountry(UpdateCountryRequest $request, Country $country): JsonResponse
    {
        try {

            DB::transaction(callback: function() use($request, &$country) {

                // 1. Ország frissítése
                $country->update(attributes: $request->all());

                // 2. Frissített adatok visszatöltése
                $country->refresh();

                // 3. Kapcsolódó rekordok frissítése (pl. alapértelmezett beállítások)
                $this->updateDefaultSettings(country: $country);

                // 4. Cache törlése, ha releváns

            });

            return response()->json($country, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error('updateCountry ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'updateCountry Country not found'], status: Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error(message: 'updateCountry QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(data: ['error' => 'updateCountry Database error'], status: Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error(message: 'updateCountry Exception: ' . print_r($ex, return: true));
            return response()->json(data: ['error' => 'updateCountry Internal server error'], status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteCounties(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array|min:1', // Kötelező, legalább 1 id kell
                'ids.*' => 'integer|exists:countries,id', // Az id-k egész számok és létező cégek legyenek
            ]);

            $ids = $validated['ids'];
            $deletedCount = 0;

            DB::transaction(function () use ($ids, &$deletedCount) {
                // 1. Törlés - válaszd az egyik verziót:
                // a) Observer nélküli, gyors SQL törlés:
                $deletedCount = Country::whereIn('id', $ids)->delete();

                // b) Observer-kompatibilis, egyenkénti törlés:
                //$counties = Country::whereIn('id', $ids)->lockForUpdate()->get();
                //$counties->each(function (Country $country) use (&$deletedCount) {
                //    if ($country->delete()) {
                //        $deletedCount++;
                //    }
                //});

                // Cache törlése, ha szükséges
            });

            return response()->json($deletedCount,Response::HTTP_OK);
        } catch( QueryException $ex ) {
            \Log::error('deleteCounties QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCounties Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('deleteCounties Exception: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'deleteCounties Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteCountry(GetCountryRequest $request): JsonResponse
    {
        try {
            $country = null;

            DB::transaction(function()use($request, &$country) {
                // 1. Ország keresése
                $country = Country::lockForUpdate()->findOrFail($request->id);
                // 2. Ország törlése
                $country->delete();
            });

            return response()->json(['id' => $country->id, 'deleted' => true], Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error('deleteCountry ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCountry Country not found'], Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('deleteCountry QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'deleteCountry Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('deleteCountry Exception: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'deleteCountry Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function restoreCountry(GetCountryRequest $request): JsonResponse
    {
        try {
            $country = null;

            DB::transaction(function () use ($request, &$country) {
                // Soft-deleted ország lekérése
                $country = Country::withTrashed()->findOrFail($request->id);

                // Visszaállítás
                $country->restore();

                // Friss adat betöltése
                $country->refresh();
            });

            return response()->json($country, Response::HTTP_OK);

        } catch (ModelNotFoundException $ex) {
            \Log::error('restoreCountry ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreCountry Country not found'], Response::HTTP_NOT_FOUND);

        } catch (QueryException $ex) {
            \Log::error('restoreCountry QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreCountry Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);

        } catch (Exception $ex) {
            \Log::error('restoreCountry Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreCountry Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function realDeleteCountry(GetCountryRequest $request): JsonResponse
    {
        try {
            $country = null;

            DB::transaction(function()use($request, &$country) {
                // 1. Ország keresése
                $country = Country::withTrashed()->lockForUpdate()->findOrFail($request->id);
                // 2. Ország véglegesen törlése
                $country->forceDelete();
            });

            return response()->json(data: $country, status: Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error(message: 'realDeleteCountry ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(data: ['error' => 'realDeleteCountry Country not found'], status: Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error(message: 'realDeleteCountry QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(data: ['error' => 'realDeleteCountry Database error'], status: Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error(message: 'realDeleteCountry Exception: ' . print_r(value: $ex, return: true));
            return response()->json(data: ['error' => 'realDeleteCountry Internal server error'], status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function createDefaultSettings(Country $country): void
    {
        //
    }

    private function updateDefaultSettings(Country $country): void
    {
        //
    }
}
