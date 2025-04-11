<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetSubdomainRequest;
use App\Http\Requests\SubdomainIndexRequest;
use App\Http\Requests\StoreSubdomainRequest;
use App\Http\Requests\UpdateSubdomainRequest;
use App\Models\Subdomain;
use App\Models\SubdomainState as State;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use DB;

class SubdomainController extends Controller
{
    public function __construct()
    {
        //
    }
    
    public function index(SubdomainIndexRequest $request)
    {
        $states = State::ToSelect();
        
        return Inertia::render('Subdomain/Index', [
            'title' => 'Subdomains',
            'filters' => $request->all(['search', 'field', 'order']),
            'states' => $states,
        ]);
    }
    
    public function fetch(Request $request): JsonResponse
    {
        $_subdomains = Subdomain::query();
        
        if( $search = $request->search ) {
            $_subdomains->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        }
        
        if( $request->has(['field', 'order']) ) {
            $_subdomains->orderBy($request->field, $request->order);
        }
        
        $subdomains = $_subdomains->with('subdomainState')
                ->paginate(10, ['*'], 'page', $request->page ?? 1);
        
        return response()->json($subdomains);
    }
    
    public function getSubdomain(GetSubdomainRequest $request): JsonResponse
    {
        try {
            $subdomain = Subdomain::with(['subdomainState'])
            ->findOrFail($request->id);
            
            return response()->json($subdomain, Response::HTTP_OK);
            
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'getSubdomain ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getSubdomain Entity not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'getSubdomain QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getSubdomain Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'getSubdomain Exception: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getSubdomain Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function getSubdomainByName(string $name): JsonResponse
    {
        try {
            $subdomain = State::where('name', '=', $name)->firstOrFail();
            
            return response()->json($subdomain, Response::HTTP_OK);
            
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'getSubdomainByName ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getSubdomainByName Entity not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'getSubdomainByName QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getSubdomainByName Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'getSubdomainByName Exception: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getSubdomainByName Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function storeSubdomain(StoreSubdomainRequest $request): JsonResponse
    {
        try {
            $subdomain = DB::transaction(function() use($request): Subdomain {
                // 1. Entitás létrehozása
                $_subdomain = Subdomain::create($request->all());
                
                // 2. Kapcsolódó rekordok létrehozása (pl. alapértelmezett beállítások)
                $this->createDefaultSettings($_subdomain);

                // 3. Cache törlése, ha releváns
                
                return $_subdomain;
            });
            
            return response()->json($subdomain, Response::HTTP_OK);
            
        } catch( QueryException $ex ) {
            \Log::info('storeSubdomain QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'storeSubdomain Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('storeSubdomain Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'storeSubdomain Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function updateSubdomain(UpdateSubdomainRequest $request, int $id): JsonResponse
    {
        try {

            $subdomain = DB::transaction(function() use($request, $id): Subdomain {
                $_subdomain = Subdomain::lockForUpdate()->findOrFail($id);
                $_subdomain->update($request->all());
                $_subdomain->refresh();

                // 3. Kapcsolódó rekordok frissítése (pl. alapértelmezett beállítások)
                $this->updateDefaultSettings($_subdomain);

                // 4. Cache törlése, ha releváns

                return $_subdomain;

            });

            return response()->json($subdomain, Response::HTTP_CREATED);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('updateSubdomain ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'updateSubdomain City not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('updateSubdomain QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'updateSubdomain Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('updateSubdomain Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'updateSubdomain Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function deleteSubdomains(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array|min:1', // Kötelező, legalább 1 id kell
                'ids.*' => 'integer|exists:subdomains,id', // Az id-k egész számok és létező aldomain legyenek
            ]);

            $ids = $validated['ids'];

            $deletedCount = DB::transaction(function () use ($ids): int {
                // 1. Törlés - válaszd az egyik verziót:
                // a) Observer nélküli, gyors SQL törlés:
                $count = Subdomain::whereIn('id', $ids)->delete();

                // b) Observer-kompatibilis, egyenkénti törlés:
                //$_subdomains = Subdomain::whereIn('id', $ids)->lockForUpdate()->get();
                //$_subdomains->each(function (Subdomain $subdomain) use (&$deletedCount) {
                //    if ($subdomain->delete()) {
                //        $deletedCount++;
                //    }
                //});

                // Cache törlése, ha szükséges

                return $count;
            });

            return response()->json($deletedCount, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('deleteSubdomains ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteSubdomains Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( QueryException $ex ) {
            \Log::info('deleteSubdomains QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteSubdomains Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('deleteSubdomains Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteSubdomains Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function deleteSubdomain(GetSubdomainRequest $request): JsonResponse
    {
        try {

            $subdomain = DB::transaction(function() use($request): Subdomain {
                $_subdomain = Subdomain::lockForUpdate()->findOrFail($request->id);
                $_subdomain->delete();

                // Cache törlése, ha szükséges

                return $_subdomain;
            });

            return response()->json($subdomain, Response::HTTP_OK);
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
    
    
    private function createDefaultSettings(Subdomain $subdomain): void
    {
        //
    }

    private function updateDefaultSettings(Subdomain $subdomain): void
    {
        //
    }
}
