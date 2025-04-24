<?php

namespace App\Http\Controllers\Geo;
//use App\Http\Requests\RegionIndexRequest;


use App\Http\Controllers\Controller;
use App\Http\Requests\GetRegionRequest;
use App\Http\Requests\IndexRegionRequest;
use App\Http\Requests\StoreRegionRequest;
use App\Http\Requests\UpdateRegionRequest;
use App\Models\Geo\City;
use App\Models\Geo\Country;
use App\Models\Geo\Region;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RegionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read region', ['only' => ['index', 'show', 'fetch', 'getRegion', 'getRegionByName']]);
        $this->middleware('permission:create region', ['only' => ['storeRegion']]);
        $this->middleware('permission:update region', ['only' => ['updateRegion', 'restoreRegion']]);
        $this->middleware('permission:delete region', ['only' => ['deleteRegions', 'deleteRegion', 'realDeleteRegion']]);
    }

    public function index(IndexRegionRequest $request): InertiaResponse
    {
        $countries = Country::active()->select('name', 'id')->get();
        $cities = City::active()->select('name', 'id')->get();

        return Inertia::render('Geo/Region/Index', [
            'title' => 'Regions',
            'filters' => $request->all(['search', 'field', 'order']),
            'countries' => $countries,
            'cities' => $cities
        ]);
    }

    public function fetch(Request $request): JsonResponse
    {
        $_regions = Region::query();

        if( $request->has('search') ) {
            $_regions->whereRaw(
                sql: "CONCAT(name, ' ', code) LIKE ?", 
                bindings: ["%{$request->search}%"]
            );
        }

        if ($request->has('field') && $request->has('order')) {
            $_regions->orderBy($request->field, $request->order);
        }

        $regions = $_regions->paginate( 10, ['*'], 'page', $request->page?? 1 );

        return response()->json($regions);
    }

    public function getRegion(GetRegionRequest $request): JsonResponse
    {
        try {
            $region = Region::with(['country', 'cities'])
                ->withCount(['cities'])
                ->findOrFail($request->id);

            return response()->json($region,  Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error('getRegion ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'getRegion Region not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('getRegion QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'getRegion Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('getRegion Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'getRegion Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getRegionByName(string $name): JsonResponse
    {
        try {
            $regions = Region::where('name', '=', $name)->firstOrFail();

            return response()->json($regions,  Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error('getRegionByName ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'getRegionByName Region not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('getRegionByName QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'getRegionByName Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('getRegionByName Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'getRegionByName Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeRegion(StoreRegionRequest $request): JsonResponse
    {
        try {

            $region = DB::transaction(function() use($request): Region {
                // 1. Ország létrehozása
                $_region = Region::create($request->all());

                // 2. Kapcsolódó rekordok létrehozása (pl. alapértelmezett beállítások)
                $this->createDefaultSettings($_region);

                // 3. Cache törlése, ha releváns

                return $_region;
            });

            return response()->json($region,  Response::HTTP_OK);
        } catch( QueryException $ex ) {
            \Log::error('stoReregion QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'stoReregion Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('stoReregion Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'stoReregion Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateRegion(UpdateRegionRequest $request, int $id): JsonResponse
    {
        try {

            $region = DB::transaction(function() use($request, $id): Region {
                // 1. Az ország lekerdezése
                $_region = Country::lockForUpdate()->findOrFail($id);
                // 1. Ország frissítése
                $_region->update($request->all());

                // 2. Frissített adatok visszatöltése
                $_region->refresh();

                // 3. Kapcsolódó rekordok frissítése (pl. alapértelmezett beállítások)
                $this->updateDefaultSettings($_region);

                // 4. Cache törlése, ha releváns


                return $_region;
            });

            return response()->json($region, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error('updateRegion ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'updateRegion Region not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('updateRegion QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'updateRegion Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('updateRegion Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'updateRegion Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteRegions(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array|min:1', // Kötelező, legalább 1 id kell
                'ids.*' => 'integer|exists:regions,id', // Az id-k egész számok és létező cégek legyenek
            ]);

            $ids = $validated['ids'];

            $deletedCount = DB::transaction(function () use ($ids): int {
                // 1. Törlés - válaszd az egyik verziót:
                // a) Observer nélküli, gyors SQL törlés:
                $regions = Region::whereIn('id', $ids)->delete();

                // b) Observer-kompatibilis, egyenkénti törlés:
                //$regions = Region::whereIn('id', $ids)->lockForUpdate()->get();
                //$regions->each(function (Region $region, &$count) {
                //    if ($region->delete()) {
                //        $count++;
                //    }
                //  
                //});

                // Cache törlése, ha szükséges

                return $regions;
            });

            return response()->json($deletedCount,Response::HTTP_OK);
        } catch( QueryException $ex ) {
            \Log::error('deleteRegions QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteRegions Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('deleteRegions Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteRegions Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteRegion(GetRegionRequest $request): JsonResponse
    {
        try {

            $region = DB::transaction(function()use($request) {
                // 1. Ország keresése
                $_region = Region::lockForUpdate()->findOrFail($request->id);
                // 2. Ország törlése
                $_region->delete();

                return $_region;
            });

            return response()->json(['id' => $region->id, 'deleted' => true], Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error('deleteRegion ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteRegion Region not found'], Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('deleteRegion QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteRegion Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('deleteRegion Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteRegion Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function restoreRegion(GetRegionRequest $request): JsonResponse
    {
        try {

            $region = DB::transaction(function () use ($request) {
                // Soft-deleted ország lekérése
                $_region = Region::withTrashed()->findOrFail($request->id);

                // Visszaállítás
                $_region->restore();

                // Friss adat betöltése
                $_region->refresh();

                return $_region;
            });

            return response()->json($region, Response::HTTP_OK);

        } catch (ModelNotFoundException $ex) {
            \Log::error('restoreRegion ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreRegion Region not found'], Response::HTTP_NOT_FOUND);
        } catch (QueryException $ex) {
            \Log::error('restoreRegion QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreRegion Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $ex) {
            \Log::error('restoreRegion Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreRegion Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function realDeleteRegion(GetRegionRequest $request): JsonResponse
    {
        try {

            $region = DB::transaction(function()use($request): Country {
                // 1. Ország keresése
                $_region = Region::withTrashed()->lockForUpdate()->findOrFail($request->id);
                // 2. Ország véglegesen törlése
                $_region->forceDelete();

                return $_region;
            });

            return response()->json($region,  Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error('realDeleteRegion ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteRegion Region not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('realDeleteRegion QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteRegion Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('realDeleteRegion Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteRegion Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
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
