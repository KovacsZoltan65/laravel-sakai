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
        $regions = Region::toSelect();
        
        $cities = City::toSelect();

        return Inertia::render('Geo/Country/Index', [
            'title' => 'Country',
            'filters' => $request->all(keys: ['search', 'field', 'order']),
            'regions' => $regions,
            'cities' => $cities
        ]);
    }

    public function fetch(Request $request): JsonResponse
    {
        $_countries = Country::query();

        if( $request->has('search') ) {
            $_countries->whereRaw(
                "CONCAT(name, ' ', email, ' ', address, ' ', phone) LIKE ?",
                ["%{$request->search}%"]
            );
        }

        if ($request->has('field') && $request->has('order')) {
            $_countries->orderBy($request->field, $request->order);
        }

        $countries = $_countries->with(['regions', 'cities'])
            ->withCount(['regions', 'cities'])
            ->paginate( 10, ['*'], 'page', $request->page ?? 1 );

        return response()->json($countries);
    }

    public function getCountry(GetCountryRequest $request): JsonResponse
    {
        try {
            $country = Country::with(['cities', 'regions'])
                ->withCount(['cities', 'regions'])
                ->findOrFail($request->id);

            return response()->json($country,  Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error('getCountry ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'getCountry Country not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('getCountry QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'getCountry Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('getCountry Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'getCountry Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getCountryByName(string $name): JsonResponse
    {
        try {
            $countries = Country::where('name', '=', $name)->firstOrFail();

            return response()->json($countries,  Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error('getCountryByName ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'getCountryByName Country not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('getCountryByName QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'getCountryByName Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('getCountryByName Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'getCountryByName Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeCountry(StoreCountryRequest $request): JsonResponse
    {
        try {

            $country = DB::transaction(function() use($request): Country {
                // 1. Ország létrehozása
                $_country = Country::create($request->all());

                // 2. Kapcsolódó rekordok létrehozása (pl. alapértelmezett beállítások)
                $this->createDefaultSettings($_country);

                // 3. Cache törlése, ha releváns

                return $_country;
            });

            return response()->json($country,  Response::HTTP_OK);
        } catch( QueryException $ex ) {
            \Log::error('storeCountry QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'storeCountry Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('storeCountry Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'storeCountry Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateCountry(UpdateCountryRequest $request, int $id): JsonResponse
    {
        try {

            $country = DB::transaction(function() use($request, $id): Country {
                // 1. Az ország lekerdezése
                $_country = Country::lockForUpdate()->findOrFail($id);
                // 1. Ország frissítése
                $_country->update($request->all());

                // 2. Frissített adatok visszatöltése
                $_country->refresh();

                // 3. Kapcsolódó rekordok frissítése (pl. alapértelmezett beállítások)
                $this->updateDefaultSettings($_country);

                // 4. Cache törlése, ha releváns


                return $_country;
            });

            return response()->json($country, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error('updateCountry ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'updateCountry Country not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('updateCountry QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'updateCountry Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('updateCountry Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'updateCountry Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
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

            $deletedCount = DB::transaction(function () use ($ids) {
                // 1. Törlés - válaszd az egyik verziót:
                // a) Observer nélküli, gyors SQL törlés:
                $count = Country::whereIn('id', $ids)->delete();

                // b) Observer-kompatibilis, egyenkénti törlés:
                //$counties = Country::whereIn('id', $ids)->lockForUpdate()->get();
                //$counties->each(function (Country $country, &$count) {
                //    if ($country->delete()) {
                //        $count++;
                //    }
                //  
                //});

                // Cache törlése, ha szükséges

                return $count;
            });

            return response()->json($deletedCount,Response::HTTP_OK);
        } catch( QueryException $ex ) {
            \Log::error('deleteCounties QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCounties Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('deleteCounties Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCounties Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteCountry(GetCountryRequest $request): JsonResponse
    {
        try {

            $country = DB::transaction(function()use($request) {
                // 1. Ország keresése
                $_country = Country::lockForUpdate()->findOrFail($request->id);
                // 2. Ország törlése
                $_country->delete();

                return $_country;
            });

            return response()->json(['id' => $country->id, 'deleted' => true], Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error('deleteCountry ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCountry Country not found'], Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('deleteCountry QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCountry Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('deleteCountry Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteCountry Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function restoreCountry(GetCountryRequest $request): JsonResponse
    {
        try {

            $country = DB::transaction(function () use ($request) {
                // Soft-deleted ország lekérése
                $_country = Country::withTrashed()->findOrFail($request->id);

                // Visszaállítás
                $_country->restore();

                // Friss adat betöltése
                $_country->refresh();

                return $_country;
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

            $country = DB::transaction(function()use($request): Country {
                // 1. Ország keresése
                $_country = Country::withTrashed()->lockForUpdate()->findOrFail($request->id);
                // 2. Ország véglegesen törlése
                $_country->forceDelete();

                return $_country;
            });

            return response()->json($country,  Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error('realDeleteCountry ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteCountry Country not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('realDeleteCountry QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteCountry Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('realDeleteCountry Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteCountry Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
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
