<?php

namespace App\Http\Controllers\Geo;

use App\Http\Controllers\Controller;
use App\Http\Requests\CityIndexRequest;
use App\Http\Requests\GetCityRequest;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Models\Geo\City;
use App\Models\Geo\Country;
use App\Models\Geo\Region;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response AS InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CityController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(CityIndexRequest $request): InertiaResponse
    {
        $regions = Region::active()->select('name', 'id')->get();
        $countries = Country::active()->select('name', 'id')->get();

        return Inertia::render('Geo/City/Index', [
            'title' => 'Cities',
            'filters' => $request->all(['search', 'field', 'order']),
            'regions' => $regions,
            'countries' => $countries,
        ]);
    }

    public function fetch(Request $request): JsonResponse
    {
        $_cities = City::query();

        if( $request->has('search') ) {
            $_cities->whereRaw(
                "CONCAT(name) LIKE ?", 
                ["%{$request->search}%"]
            );
        }

        if ($request->has('field') && $request->has('order')) {
            $_cities->orderBy($request->field, $request->order);
        }

        $cities = $_cities->with(['region', 'country'])->paginate( 10, ['*'], 'page', $request->page?? 1 );

        return response()->json($cities);
    }

    public function getCity(GetCityRequest $request): JsonResponse
    {
        try {
            $city = City::with(['country', 'region'])
            ->findOrFail($request->id);

            return response()->json($city, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'getCity ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getCity City not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'getCity QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getCity Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'getCity Exception: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getCity Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getCityByName(string $name): JsonResponse
    {
        try {
            $entity = City::where('name', '=', $name)->firstOrFail();

            return response()->json($entity, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('getCityByName ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'getCityByName Entity not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info('getCityByName QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'getCityByName Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('getCityByName Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'getCityByName Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeCity(StoreCityRequest $request): JsonResponse
    {
        try {

            $city = DB::transaction(function() use($request): City {
                // 1. Entitás létrehozása
                $_city = City::create($request->all());

                // 2. Kapcsolódó rekordok létrehozása (pl. alapértelmezett beállítások)
                $this->createDefaultSettings($_city);

                // 3. Cache törlése, ha releváns

                return $_city;
            });

            return response()->json($city, Response::HTTP_CREATED);
        } catch( QueryException $ex ) {
            \Log::info('storeCity QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'storeCity Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('storeCity Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'storeCity Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateCity(UpdateCityRequest $request, int $id): JsonResponse
    {
        try {

            $city = DB::transaction(function() use($request, $id): City {
                $_city = City::lockForUpdate()->findOrFail($id);
                $_city->update($request->all());
                $_city->refresh();

                // 3. Kapcsolódó rekordok frissítése (pl. alapértelmezett beállítások)
                $this->updateDefaultSettings($_city);

                // 4. Cache törlése, ha releváns

                return $_city;

            });

            return response()->json($city, Response::HTTP_CREATED);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('updateCity ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'updateCity City not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('updateCity QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'updateCity Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('updateCity Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'updateCity Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteCities(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array|min:1', // Kötelező, legalább 1 id kell
                'ids.*' => 'integer|exists:cities,id', // Az id-k egész számok és létező cégek legyenek
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
            \Log::info('deleteCities ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCities Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( QueryException $ex ) {
            \Log::info('deleteCities QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCities Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('deleteCities Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCities Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteCity(GetCityRequest $request): JsonResponse
    {
        try {

            $city = DB::transaction(function() use($request): City {
                $_city = City::lockForUpdate()->findOrFail($request->id);
                $_city->delete();

                // Cache törlése, ha szükséges

                return $_city;
            });

            return response()->json($city, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('deleteCity ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCity City not found'], Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info('deleteCity QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCity Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('deleteCity Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCity Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function restoreCity(GetCityRequest $request): JsonResponse
    {
        try {

            $city = DB::transaction(function () use ($request): City {
                // Soft-deleted ország lekérése
                $_city = City::withTrashed()->findOrFail($request->id);

                // Visszaállítás
                $_city->restore();

                // Friss adat betöltése
                $_city->refresh();

                return $_city;
            });

            return response()->json($city, Response::HTTP_OK);

        } catch (ModelNotFoundException $ex) {
            \Log::error('restoreCity ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreCity City not found'], Response::HTTP_NOT_FOUND);
        } catch (QueryException $ex) {
            \Log::error('restoreCity QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreCity Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $ex) {
            \Log::error('restoreCity Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreCity Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function realDeleteCity(GetCityRequest $request): JsonResponse
    {
        try {

            $city = DB::transaction(function()use($request): City {
                // 1. Ország keresése
                $_city = City::withTrashed()->lockForUpdate()->findOrFail($request->id);
                // 2. Ország véglegesen törlése
                $_city->forceDelete();

                return $_city;
            });

            return response()->json($city,  Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error('realDeleteCity ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteCity City not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('realDeleteCity QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteCity Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('realDeleteCity Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteCity Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
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
